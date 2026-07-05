<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BotResource extends JsonResource
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

    'workspace_id' => $this->workspace_id,

    'name' => $this->name,

    'description' => $this->description,

    'system_prompt' => $this->system_prompt,

    'status' => $this->status,

    'workspace' => new WorkspaceResource(
        $this->whenLoaded('workspace')
    ),

    'sources' => BotSourceResource::collection(
        $this->whenLoaded('sources')
    ),

];
    }
}
