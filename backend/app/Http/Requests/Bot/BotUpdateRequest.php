<?php

namespace App\Http\Requests\Bot;

use Illuminate\Foundation\Http\FormRequest;

class BotUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'workspace_id' => [
                'required',
                'exists:workspaces,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'system_prompt' => [
                'nullable',
                'string',
            ],

            'model' => [
                'required',
                'string',
                'max:100',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'workspace_id.required' => 'Workspace is required.',
            'workspace_id.exists' => 'Selected workspace does not exist.',

            'name.required' => 'Bot name is required.',
            'name.max' => 'Bot name cannot exceed 255 characters.',

            'description.max' => 'Description cannot exceed 1000 characters.',

            'model.required' => 'Model is required.',
        ];
    }
}