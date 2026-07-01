<?php

namespace Tests\Feature;

use App\Models\Bot;
use App\Models\BotSource;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BotCrudTest extends TestCase
{
    use RefreshDatabase;

    private function ownedWorkspace(User $user): Workspace
    {
        return Workspace::factory()->create([
            'owner_id' => $user->id,
            'slug' => 'workspace-' . $user->id,
        ]);
    }

    public function test_guest_cannot_access_bot_routes(): void
    {
        $bot = Bot::factory()->create();

        $this->get(route('bots.index'))->assertRedirect(route('login'));
        $this->get(route('bots.create'))->assertRedirect(route('login'));
        $this->post(route('bots.store'))->assertRedirect(route('login'));
        $this->get(route('bots.edit', $bot))->assertRedirect(route('login'));
        $this->put(route('bots.update', $bot))->assertRedirect(route('login'));
        $this->delete(route('bots.destroy', $bot))->assertRedirect(route('login'));
    }

    public function test_user_can_create_bot_with_document(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $workspace = $this->ownedWorkspace($user);
        $file = UploadedFile::fake()->create('manual.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)->post(route('bots.store'), [
            'workspace_id' => $workspace->id,
            'name' => 'FAQ Bot',
            'description' => 'Answers FAQs',
            'system_prompt' => 'Be helpful',
            'model' => 'gpt-4o-mini',
            'sources' => [
                [
                    'type' => 'document',
                    'file' => $file,
                ],
            ],
        ]);

        $response->assertRedirect(route('bots.index'));

        $bot = Bot::query()->where('name', 'FAQ Bot')->first();

        $this->assertNotNull($bot);
        $this->assertSame('faq-bot', $bot->slug);
        $this->assertSame('processing', $bot->status);

        $source = BotSource::query()->where('bot_id', $bot->id)->first();

        $this->assertNotNull($source);
        $this->assertSame('document', $source->type);
        Storage::disk('public')->assertExists($source->file_path);
        $this->assertStringStartsWith("bots/{$bot->id}-faq-bot/", $source->file_path);
    }

    public function test_user_can_create_bot_with_website_and_text_sources(): void
    {
        $user = User::factory()->create();
        $workspace = $this->ownedWorkspace($user);

        $this->actingAs($user)->post(route('bots.store'), [
            'workspace_id' => $workspace->id,
            'name' => 'Knowledge Bot',
            'model' => 'gpt-4o-mini',
            'sources' => [
                [
                    'type' => 'website',
                    'url' => 'https://example.com',
                    'title' => 'Example Site',
                ],
                [
                    'type' => 'text',
                    'title' => 'Notes',
                    'content' => 'Some reference text',
                ],
            ],
        ])->assertRedirect(route('bots.index'));

        $bot = Bot::query()->where('name', 'Knowledge Bot')->firstOrFail();

        $this->assertDatabaseHas('bot_sources', [
            'bot_id' => $bot->id,
            'type' => 'website',
            'url' => 'https://example.com',
        ]);

        $this->assertDatabaseHas('bot_sources', [
            'bot_id' => $bot->id,
            'type' => 'text',
            'content' => 'Some reference text',
        ]);
    }

    public function test_user_cannot_create_bot_with_duplicate_name(): void
    {
        $user = User::factory()->create();
        $workspace = $this->ownedWorkspace($user);

        Bot::factory()->create([
            'workspace_id' => $workspace->id,
            'name' => 'Support Bot',
            'slug' => 'support-bot',
        ]);

        $response = $this->actingAs($user)->post(route('bots.store'), [
            'workspace_id' => $workspace->id,
            'name' => 'Support Bot',
            'model' => 'gpt-4o-mini',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('bots', 1);
    }

    public function test_user_cannot_create_bot_in_another_users_workspace(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id,
            'slug' => 'owner-workspace',
        ]);

        $this->actingAs($intruder)->post(route('bots.store'), [
            'workspace_id' => $workspace->id,
            'name' => 'Intruder Bot',
            'model' => 'gpt-4o-mini',
        ])->assertNotFound();
    }

    public function test_user_can_update_bot_and_add_document(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $workspace = $this->ownedWorkspace($user);

        $bot = Bot::factory()->create([
            'workspace_id' => $workspace->id,
            'name' => 'Updater Bot',
            'slug' => 'updater-bot',
        ]);

        $file = UploadedFile::fake()->create('guide.txt', 50, 'text/plain');

        $this->actingAs($user)->put(route('bots.update', $bot), [
            'workspace_id' => $workspace->id,
            'name' => 'Updater Bot Renamed',
            'model' => 'gpt-4o-mini',
            'sources' => [
                [
                    'type' => 'document',
                    'file' => $file,
                ],
            ],
        ])->assertRedirect(route('bots.index'));

        $bot->refresh();

        $this->assertSame('Updater Bot Renamed', $bot->name);
        $this->assertDatabaseHas('bot_sources', [
            'bot_id' => $bot->id,
            'type' => 'document',
        ]);
    }

    public function test_user_can_delete_bot_and_storage_is_removed(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $workspace = $this->ownedWorkspace($user);

        $bot = Bot::factory()->create([
            'workspace_id' => $workspace->id,
            'name' => 'Delete Bot',
            'slug' => 'delete-bot',
        ]);

        $path = "bots/{$bot->id}-delete-bot/test.pdf";
        Storage::disk('public')->put($path, 'content');

        BotSource::query()->create([
            'bot_id' => $bot->id,
            'type' => 'document',
            'title' => 'Test',
            'file_name' => 'test.pdf',
            'file_path' => $path,
            'file_type' => 'pdf',
            'file_size' => 7,
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->delete(route('bots.destroy', $bot))
            ->assertRedirect(route('bots.index'));

        $this->assertDatabaseMissing('bots', ['id' => $bot->id]);
        Storage::disk('public')->assertMissing($path);
        Storage::disk('public')->assertMissing("bots/{$bot->id}-delete-bot");
    }

    public function test_user_can_delete_single_source_and_empty_folder_is_removed(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $workspace = $this->ownedWorkspace($user);

        $bot = Bot::factory()->create([
            'workspace_id' => $workspace->id,
            'name' => 'Source Bot',
            'slug' => 'source-bot',
        ]);

        $directory = "bots/{$bot->id}-source-bot";
        $path = "{$directory}/doc.pdf";
        Storage::disk('public')->put($path, 'content');

        $source = BotSource::query()->create([
            'bot_id' => $bot->id,
            'type' => 'document',
            'title' => 'Doc',
            'file_name' => 'doc.pdf',
            'file_path' => $path,
            'file_type' => 'pdf',
            'file_size' => 7,
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->delete(route('bots.sources.destroy', [$bot, $source]))
            ->assertRedirect(route('bots.edit', $bot));

        $this->assertDatabaseMissing('bot_sources', ['id' => $source->id]);
        Storage::disk('public')->assertMissing($path);
        Storage::disk('public')->assertMissing($directory);
    }

    public function test_user_cannot_delete_source_from_another_bot(): void
    {
        $user = User::factory()->create();
        $workspace = $this->ownedWorkspace($user);

        $botA = Bot::factory()->create([
            'workspace_id' => $workspace->id,
            'slug' => 'bot-a',
        ]);

        $botB = Bot::factory()->create([
            'workspace_id' => $workspace->id,
            'slug' => 'bot-b',
        ]);

        $source = BotSource::query()->create([
            'bot_id' => $botB->id,
            'type' => 'text',
            'title' => 'Foreign',
            'content' => 'Secret',
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->delete(route('bots.sources.destroy', [$botA, $source]))
            ->assertNotFound();

        $this->assertDatabaseHas('bot_sources', ['id' => $source->id]);
    }

    public function test_user_cannot_access_another_users_bot(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id,
            'slug' => 'owner-only',
        ]);

        $bot = Bot::factory()->create([
            'workspace_id' => $workspace->id,
            'slug' => 'owner-bot',
        ]);

        $this->actingAs($intruder)
            ->get(route('bots.edit', $bot))
            ->assertForbidden();

        $this->actingAs($intruder)
            ->delete(route('bots.destroy', $bot))
            ->assertForbidden();
    }

    public function test_workspace_delete_is_blocked_when_bots_exist(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $workspace = $this->ownedWorkspace($user);

        $bot = Bot::factory()->create([
            'workspace_id' => $workspace->id,
            'name' => 'Workspace Bot',
            'slug' => 'workspace-bot',
        ]);

        $directory = "bots/{$bot->id}-workspace-bot";
        $path = "{$directory}/policy.pdf";
        Storage::disk('public')->put($path, 'content');

        BotSource::query()->create([
            'bot_id' => $bot->id,
            'type' => 'document',
            'title' => 'Policy',
            'file_name' => 'policy.pdf',
            'file_path' => $path,
            'file_type' => 'pdf',
            'file_size' => 7,
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->delete(route('workspaces.destroy', $workspace))
            ->assertRedirect(route('workspaces.index'))
            ->assertSessionHas('flash.type', 'error');

        $this->assertDatabaseHas('workspaces', ['id' => $workspace->id]);
        $this->assertDatabaseHas('bots', ['id' => $bot->id]);
        Storage::disk('public')->assertExists($path);
    }
}
