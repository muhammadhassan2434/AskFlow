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

        return $bot->load('sources');
    }

    public function create(User $user, array $data): Bot
    {
        $workspace = $this->resolveOwnedWorkspace(
            $user,
            $data['workspace_id']
        );

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
        $newWorkspace = $this->resolveOwnedWorkspace(
            $user,
            $data['workspace_id']
        );

        return DB::transaction(function () use (
            $bot,
            $newWorkspace,
            $data
        ) {
            $bot->update(
                $this->botAttributes(
                    $newWorkspace,
                    $data,
                    $bot
                )
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

        DB::transaction(function () use ($bot) {
            $sources = $bot->sources()->get();
            $directories = $this->collectBotDirectories($bot, $sources);

            $this->deleteSourceFiles($sources);

            $bot->delete();

            $this->deleteBotDirectories($directories);
        });
    }

    public function deleteSource(
        User $user,
        Bot $bot,
        BotSource $source
    ): void {
        $this->assertOwner($user, $bot);

        abort_if(
            $source->bot_id !== $bot->id,
            404
        );

        DB::transaction(function () use ($source) {
            $this->deleteSourceFiles(
                new Collection([$source])
            );

            $source->delete();
        });
    }

    public function workspaceOptions(
        User $user
    ): Collection {
        return Workspace::query()
            ->where('owner_id', $user->id)
            ->where('is_active', 1)
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
        $bot->loadMissing('workspace');

        abort_if(
            $bot->workspace->owner_id !== $user->id,
            403
        );
    }

    private function uniqueSlug(
        string $name,
        ?Bot $bot = null
    ): string {
        $baseSlug = Str::slug($name) ?: 'bot';

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

        return Bot::create(
            $this->botAttributes(
                $workspace,
                $data
            )
        );
    }

    private function resolveOwnedWorkspace(
        User $user,
        int|string $workspaceId
    ): Workspace {
        return Workspace::query()
            ->whereKey($workspaceId)
            ->where('owner_id', $user->id)
            ->firstOrFail();
    }

    private function botAttributes(
        Workspace $workspace,
        array $data,
        ?Bot $bot = null
    ): array {
        return [
            'workspace_id' => $workspace->id,
            'name' => trim($data['name']),
            'slug' => $this->uniqueSlug(
                $data['name'],
                $bot
            ),
            'description' => $data['description'] ?? null,
            'system_prompt' => $data['system_prompt'] ?? null,
            'model' => $data['model'] ?? $bot?->model ?? Bot::DEFAULT_MODEL,
            'status' => $bot?->status ?? 'draft',
        ];
    }

    protected function saveSources(
        Bot $bot,
        array $sources
    ): void {
        if (empty($sources)) {
            return;
        }

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

        $bot->update([
            'status' => 'processing',
        ]);
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

        $fileName = sprintf(
            '%d-%s-%s.%s',
            $bot->id,
            $safeName,
            Str::ulid(),
            $extension
        );

        $path = $file->storeAs(
            $this->documentDirectory($bot),
            $fileName,
            'public'
        );

        if (! $path) {
            throw new \RuntimeException(
                'Failed to store the uploaded document.'
            );
        }

        try {
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
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($path);

            throw $exception;
        }
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
                : (parse_url(
                    $source['url'],
                    PHP_URL_HOST
                ) ?: 'Website'),

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

    private function documentDirectory(Bot $bot): string
    {
        $existingPath = $bot->sources()
            ->where('type', 'document')
            ->whereNotNull('file_path')
            ->value('file_path');

        if ($existingPath) {
            return dirname($existingPath);
        }

        return $this->botStorageDirectory($bot);
    }

    private function botStorageDirectory(Bot $bot): string
    {
        return sprintf(
            'bots/%d-%s',
            $bot->id,
            $bot->slug
        );
    }

    private function deleteSourceFiles(Collection $sources): void
    {
        $directories = collect();

        foreach ($sources as $source) {
            if (! $source->file_path) {
                continue;
            }

            $disk = Storage::disk('public');

            if ($disk->exists($source->file_path)) {
                $disk->delete($source->file_path);
            }

            $directories->push(dirname($source->file_path));
        }

        $directories
            ->unique()
            ->each(fn (string $directory) => $this->deleteDirectoryIfEmpty($directory));
    }

    private function deleteDirectoryIfEmpty(string $directory): void
    {
        $disk = Storage::disk('public');

        while (
            $directory &&
            $directory !== '.' &&
            str_starts_with($directory, 'bots/')
        ) {
            if (
                ! empty($disk->files($directory)) ||
                ! empty($disk->directories($directory))
            ) {
                break;
            }

            $disk->deleteDirectory($directory);
            $directory = dirname($directory);
        }
    }

    private function collectBotDirectories(
        Bot $bot,
        Collection $sources
    ): array {
        return $sources
            ->pluck('file_path')
            ->filter()
            ->map(fn (string $path) => dirname($path))
            ->push($this->botStorageDirectory($bot))
            ->push(sprintf('bots/%d', $bot->id))
            ->unique()
            ->values()
            ->all();
    }

    private function deleteBotDirectories(array $directories): void
    {
        $disk = Storage::disk('public');

        foreach ($directories as $directory) {
            if ($disk->exists($directory)) {
                $disk->deleteDirectory($directory);
            }
        }
    }
}
