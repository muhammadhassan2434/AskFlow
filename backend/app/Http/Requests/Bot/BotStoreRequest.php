<?php

namespace App\Http\Requests\Bot;

use App\Models\Bot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BotStoreRequest extends FormRequest
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
                Rule::exists('workspaces', 'id')
                    ->where('owner_id', $this->user()->id),
            ],

            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('bots', 'name'),
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'system_prompt' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'model' => [
                'required',
                'string',
                'max:100',
                Rule::in([Bot::DEFAULT_MODEL]),
            ],

            'sources' => [
                'nullable',
                'array',
            ],

            'sources.*.type' => [
                'required',
                Rule::in([
                    'document',
                    'website',
                    'text',
                ]),
            ],

            /*
            |--------------------------------------------------------------------------
            | Document
            |--------------------------------------------------------------------------
            */

            'sources.*.file' => [
                'required_if:sources.*.type,document',
                'nullable',
                'file',
                'mimes:pdf,doc,docx,txt',
                'max:20480',
            ],

            /*
            |--------------------------------------------------------------------------
            | Website
            |--------------------------------------------------------------------------
            */

            'sources.*.url' => [
                'required_if:sources.*.type,website',
                'nullable',
                'url',
                'max:2048',
            ],

            /*
            |--------------------------------------------------------------------------
            | Website/Text Title
            |--------------------------------------------------------------------------
            */

            'sources.*.title' => [
                'nullable',
                'string',
                'max:255',
            ],

            /*
            |--------------------------------------------------------------------------
            | Text Source
            |--------------------------------------------------------------------------
            */

            'sources.*.content' => [
                'required_if:sources.*.type,text',
                'nullable',
                'string',
            ],

        ];
    }

    public function messages(): array
    {
        return [

            'workspace_id.required' =>
                'Please select a workspace.',

            'workspace_id.exists' =>
                'The selected workspace is invalid.',

            'name.required' =>
                'Bot name is required.',

            'name.unique' =>
                'A bot with this name already exists.',

            'name.max' =>
                'Bot name may not exceed 255 characters.',

            'description.max' =>
                'Description may not exceed 1000 characters.',

            'system_prompt.max' =>
                'System prompt may not exceed 5000 characters.',

            'model.required' =>
                'Model is required.',

            'model.in' =>
                'Only the default GPT model is currently available.',

            'sources.*.type.required' =>
                'Source type is required.',

            'sources.*.type.in' =>
                'Invalid source type selected.',

            'sources.*.file.required_if' =>
                'Please upload a document.',

            'sources.*.file.file' =>
                'Invalid file uploaded.',

            'sources.*.file.mimes' =>
                'Only PDF, DOC, DOCX and TXT files are allowed.',

            'sources.*.file.max' =>
                'Document size must not exceed 20MB.',

            'sources.*.url.required_if' =>
                'Website URL is required.',

            'sources.*.url.url' =>
                'Please enter a valid website URL.',

            'sources.*.content.required_if' =>
                'Text content is required.',

        ];
    }
}
