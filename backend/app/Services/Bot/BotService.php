<?php

namespace App\Services\Bot;

use App\Models\Bot;
use App\Models\User;
use App\Models\BotSource;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BotService
{
    public function list(User $user): Collection
    {
        return Bot::query()
            ->whereHas('workspace', function ($query) use ($user) {
                $query->where('owner_id', $user->id);
            })
            ->with('workspace')
            ->latest()
            ->get();
    }

    public function show(User $user, Bot $bot): Bot
    {
        $this->assertOwner($user, $bot);

        return Bot::query()
            ->with('sources')
            ->findOrFail($bot->id);
    }

    public function create(User $user, array $data): Bot
    {
        $workspace = Workspace::query()
            ->whereKey($data['workspace_id'])
            ->where('owner_id', $user->id)
            ->firstOrFail();

        $bot = DB::transaction(function () use ($workspace, $data) {

            $bot = $this->createBot(
                $workspace,
                $data
            );

            $this->saveSources(
                $bot,
                $data['sources'] ?? []
            );

            return $bot;
        });

        // DB::afterCommit(function () use ($bot) {
        //     ProcessBotSourcesJob::dispatch($bot->id);
        // });

        return $bot->fresh();
    }

    public function update(
        User $user,
        Bot $bot,
        array $data
    ): Bot {

        $this->assertOwner($user, $bot);
        $newWorkspace = Workspace::query()
            ->whereKey($data['workspace_id'])
            ->where('owner_id', $user->id)
            ->firstOrFail();

        return DB::transaction(function () use (
            $bot,
            $newWorkspace,
            $data
        ) {
            $bot->update([
                'workspace_id' => $newWorkspace->id,
                'name' => trim($data['name']),
                'slug' => $this->uniqueSlug(
                    $data['name'],
                    $bot
                ),
                'description' => $data['description'] ?? null,
                'system_prompt' => $data['system_prompt'] ?? null,
                'model' => $data['model'],
            ]);

            $this->deleteSources(
                $bot,
                $data['deleted_source_ids'] ?? []
            );

            $this->saveSources(
                $bot,
                $data['sources'] ?? []
            );

            // DB::afterCommit(function () use ($bot) {

            //     ProcessBotSourcesJob::dispatch(
            //         $bot->id
            //     );
            // });

            return $bot->fresh('sources');
        });
    }


    public function delete(
        User $user,
        Bot $bot
    ): void {
        $this->assertOwner($user, $bot);

        $bot->delete();
    }

    public function workspaceOptions(
        User $user
    ): Collection {
        return Workspace::query()
            ->where('owner_id', $user->id)
            ->select(
                'id',
                'name'
            )
            ->orderBy('name')
            ->get();
    }

    private function assertOwner(
        User $user,
        Bot $bot
    ): void {
        abort_if(
            $bot->workspace->owner_id !== $user->id,
            403
        );
    }

    private function uniqueSlug(
        string $name,
        ?Bot $bot = null
    ): string {
        $baseSlug = Str::slug($name);

        $slug = $baseSlug;

        $counter = 2;

        while (
            Bot::query()
            ->where('slug', $slug)
            ->when(
                $bot,
                fn($query) => $query->whereKeyNot(
                    $bot->id
                )
            )
            ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;

            $counter++;
        }

        return $slug;
    }


    protected function createBot(
        Workspace $workspace,
        array $data
    ): Bot {

        return Bot::create([

            'workspace_id' => $workspace->id,

            'name' => trim($data['name']),

            'slug' => $this->uniqueSlug(
                $data['name']
            ),

            'description' => $data['description'] ?? null,

            'system_prompt' => $data['system_prompt'] ?? null,

            'model' => $data['model'],

            'status' => 'draft',

        ]);
    }
    protected function saveSources(
        Bot $bot,
        array $sources
    ): void {

        foreach ($sources as $source) {

            match ($source['type']) {

                'document' => $this->saveDocument(
                    $bot,
                    $source['file']
                ),

                'website' => $this->saveWebsite(
                    $bot,
                    $source
                ),

                'text' => $this->saveText(
                    $bot,
                    $source
                ),

                default => null,
            };
        }

        if (! empty($sources)) {

            $bot->update([
                'status' => 'processing',
            ]);
        }
    }

    protected function saveDocument(
        Bot $bot,
        UploadedFile $file
    ): void {

        $originalName = pathinfo(
            $file->getClientOriginalName(),
            PATHINFO_FILENAME
        );

        $extension = strtolower(
            $file->getClientOriginalExtension()
        );

        $safeName = Str::slug($originalName) ?: 'document';

        $folderName = sprintf(
            '%d-%s-%s',
            $bot->id,
            $bot->slug,
            Str::ulid()
        );

        $fileName = sprintf(
            '%d-%s-%s.%s',
            $bot->id,
            $safeName,
            Str::ulid(),
            $extension
        );

        $path = $file->storeAs(
            "bots/{$folderName}",
            $fileName,
            'public'
        );

        BotSource::create([

            'bot_id' => $bot->id,

            'type' => 'document',

            'title' => $originalName,

            'content' => null,

            'url' => null,

            'file_name' => $file->getClientOriginalName(),

            'file_path' => $path,

            'file_type' => $extension,

            'file_size' => $file->getSize(),

            'status' => 'pending',

            'error_message' => null,

            'meta' => null,

            'processed_at' => null,

        ]);
    }

    protected function saveWebsite(
        Bot $bot,
        array $source
    ): void {

        BotSource::create([

            'bot_id' => $bot->id,

            'type' => 'website',

            'title' => ! empty($source['title'])
                ? trim($source['title'])
                : parse_url(
                    $source['url'],
                    PHP_URL_HOST
                ),

            'content' => null,

            'url' => trim($source['url']),

            'file_name' => null,

            'file_path' => null,

            'file_type' => null,

            'file_size' => null,

            'status' => 'pending',

            'error_message' => null,

            'meta' => null,

            'processed_at' => null,

        ]);
    }

    protected function saveText(
        Bot $bot,
        array $source
    ): void {

        BotSource::create([

            'bot_id' => $bot->id,

            'type' => 'text',

            'title' => ! empty($source['title'])
                ? trim($source['title'])
                : 'Text Source',

            'content' => trim($source['content']),

            'url' => null,

            'file_name' => null,

            'file_path' => null,

            'file_type' => null,

            'file_size' => null,

            'status' => 'pending',

            'error_message' => null,

            'meta' => null,

            'processed_at' => null,

        ]);
    }

    protected function dispatchProcessingJob(
        Bot $bot
    ): void {

        // ProcessBotSourcesJob::dispatch(
        //     $bot->id
        // );

    }

    protected function deleteSources(
        Bot $bot,
        array $sourceIds
    ): void {

        if (empty($sourceIds)) {
            return;
        }

        $sources = $bot->sources()
            ->whereIn('id', $sourceIds)
            ->get();

        foreach ($sources as $source) {

            if ($source->file_path) {
                Storage::disk('public')->delete($source->file_path);
            }

            $source->delete();
        }
    }
}
