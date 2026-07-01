<?php

namespace App\Services\Workspace;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class WorkspaceService
{
    public function list(User $user): Collection
    {
        return Workspace::query()
            ->where('owner_id', $user->id)
            ->latest()
            ->get();
    }

    public function create(User $user, array $data): Workspace
    {
        return Workspace::create([
            'owner_id' => $user->id,
            'name' => $data['name'],
            'slug' => $this->uniqueSlug($data['name']),
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function update(User $user, Workspace $workspace, array $data): Workspace
    {
        $this->assertOwner($user, $workspace);

        $workspace->update([
            'name' => $data['name'],
            'slug' => $this->uniqueSlug($data['name'], $workspace),
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? false,
        ]);

        return $workspace;
    }

    public function delete(User $user, Workspace $workspace): void
    {
        $this->assertOwner($user, $workspace);

        $workspace->delete();
    }

    private function assertOwner(User $user, Workspace $workspace): void
    {
        abort_if($workspace->owner_id !== $user->id, 403);
    }

    private function uniqueSlug(string $name, ?Workspace $workspace = null): string
    {
        $baseSlug = Str::slug($name) ?: 'workspace';
        $slug = $baseSlug;
        $counter = 2;

        while (
            Workspace::query()
                ->where('slug', $slug)
                ->when($workspace, fn ($query) => $query->whereKeyNot($workspace->id))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}