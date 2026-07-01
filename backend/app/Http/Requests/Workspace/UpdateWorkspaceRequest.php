<?php

namespace App\Http\Requests\Workspace;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateWorkspaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $workspace = $this->route('workspace');

        return $workspace && $this->user() && $workspace->owner_id === $this->user()->id;
    }

    public function rules(): array
    {
        $workspace = $this->route('workspace');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('workspaces', 'name')->where('owner_id', $this->user()->id)->ignore($workspace?->id),
                Rule::unique('workspaces', 'slug')->where('owner_id', $this->user()->id)->where('slug', $this->slugFromName())->ignore($workspace?->id),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Workspace name is required.',
            'name.max' => 'Workspace name cannot be longer than 255 characters.',
            'name.unique' => 'You already have a workspace with this name.',
            'description.max' => 'Description cannot be longer than 1000 characters.',
            'is_active.boolean' => 'Workspace status must be active or inactive.',
        ];
    }

    private function slugFromName(): string
    {
        return Str::slug((string) $this->input('name')) ?: 'workspace';
    }
}