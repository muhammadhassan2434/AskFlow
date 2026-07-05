<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BotSourceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'type' => $this->type,

            'title' => $this->title,

            'content' => $this->content,

            'url' => $this->url,

            'file_name' => $this->file_name,

            'file_path' => $this->file_path,

            'file_type' => $this->file_type,

            'file_size' => $this->file_size,

            'status' => $this->status,

            'file_url' => $this->file_path? Storage::disk('public')->url($this->file_path): null,

        ];
    }
}
