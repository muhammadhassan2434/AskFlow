<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkspaceCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_workspace_routes(): void
    {
        $workspace = Workspace::factory()->create();

        $this->get(route('workspaces.index'))->assertRedirect(route('login'));
        $this->get(route('workspaces.create'))->assertRedirect(route('login'));
        $this->post(route('workspaces.store'))->assertRedirect(route('login'));
        $this->get(route('workspaces.edit', $workspace))->assertRedirect(route('login'));
        $this->put(route('workspaces.update', $workspace))->assertRedirect(route('login'));
        $this->delete(route('workspaces.destroy', $workspace))->assertRedirect(route('login'));
    }

    public function test_user_can_create_workspace(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('workspaces.store'), [
            'name' => 'Support Team',
            'description' => 'Customer support workspace',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('workspaces.index'));

        $this->assertDatabaseHas('workspaces', [
            'owner_id' => $user->id,
            'name' => 'Support Team',
            'description' => 'Customer support workspace',
            'is_active' => true,
        ]);
    }

    public function test_user_cannot_create_duplicate_workspace_name(): void
    {
        $user = User::factory()->create();

        Workspace::factory()->create([
            'owner_id' => $user->id,
            'name' => 'Support Team',
            'slug' => 'support-team',
        ]);

        $response = $this->actingAs($user)->post(route('workspaces.store'), [
            'name' => 'Support Team',
            'description' => 'Duplicate',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('workspaces', 1);
    }

    public function test_different_users_can_use_same_workspace_name(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        Workspace::factory()->create([
            'owner_id' => $userA->id,
            'name' => 'General',
            'slug' => 'general-a',
        ]);

        $response = $this->actingAs($userB)->post(route('workspaces.store'), [
            'name' => 'General',
        ]);

        $response->assertRedirect(route('workspaces.index'));
        $this->assertDatabaseCount('workspaces', 2);
    }

    public function test_user_can_update_workspace_without_resetting_is_active(): void
    {
        $user = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $user->id,
            'name' => 'Old Name',
            'slug' => 'old-name',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->put(route('workspaces.update', $workspace), [
            'name' => 'New Name',
            'description' => 'Updated description',
        ]);

        $response->assertRedirect(route('workspaces.index'));

        $workspace->refresh();

        $this->assertSame('New Name', $workspace->name);
        $this->assertTrue($workspace->is_active);
    }

    public function test_user_can_deactivate_workspace_on_update(): void
    {
        $user = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $user->id,
            'name' => 'Active Workspace',
            'slug' => 'active-workspace',
            'is_active' => true,
        ]);

        $this->actingAs($user)->put(route('workspaces.update', $workspace), [
            'name' => 'Active Workspace',
            'is_active' => false,
        ])->assertRedirect(route('workspaces.index'));

        $this->assertFalse($workspace->fresh()->is_active);
    }

    public function test_user_cannot_deactivate_workspace_when_used_by_bots(): void
    {
        $user = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $user->id,
            'name' => 'Busy Workspace',
            'slug' => 'busy-workspace',
            'is_active' => true,
        ]);

        \App\Models\Bot::factory()->create([
            'workspace_id' => $workspace->id,
            'slug' => 'busy-bot',
        ]);

        $this->actingAs($user)->put(route('workspaces.update', $workspace), [
            'name' => 'Busy Workspace',
            'is_active' => false,
        ])
            ->assertRedirect(route('workspaces.edit', $workspace))
            ->assertSessionHas('flash.type', 'error');

        $this->assertTrue($workspace->fresh()->is_active);
    }

    public function test_user_cannot_delete_workspace_when_used_by_bots(): void
    {
        $user = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $user->id,
            'slug' => 'used-workspace',
        ]);

        \App\Models\Bot::factory()->create([
            'workspace_id' => $workspace->id,
            'slug' => 'used-bot',
        ]);

        $this->actingAs($user)
            ->delete(route('workspaces.destroy', $workspace))
            ->assertRedirect(route('workspaces.index'))
            ->assertSessionHas('flash.type', 'error');

        $this->assertDatabaseHas('workspaces', [
            'id' => $workspace->id,
        ]);
    }

    public function test_user_cannot_edit_another_users_workspace(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id,
            'slug' => 'private-workspace',
        ]);

        $this->actingAs($intruder)
            ->get(route('workspaces.edit', $workspace))
            ->assertForbidden();

        $this->actingAs($intruder)
            ->put(route('workspaces.update', $workspace), [
                'name' => 'Hacked',
            ])
            ->assertForbidden();
    }

    public function test_user_can_delete_own_workspace(): void
    {
        $user = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $user->id,
            'slug' => 'delete-me',
        ]);

        $this->actingAs($user)
            ->delete(route('workspaces.destroy', $workspace))
            ->assertRedirect(route('workspaces.index'));

        $this->assertDatabaseMissing('workspaces', [
            'id' => $workspace->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_workspace(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id,
            'slug' => 'protected-workspace',
        ]);

        $this->actingAs($intruder)
            ->delete(route('workspaces.destroy', $workspace))
            ->assertForbidden();

        $this->assertDatabaseHas('workspaces', [
            'id' => $workspace->id,
        ]);
    }
}
