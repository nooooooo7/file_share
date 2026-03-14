<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FolderResource extends JsonResource
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
            'name' => $this->name,
            'files_count' => $this->files_count,
            'description' => $this->description,
            'created_by' => $this->user->name,
            'created_at' => $this->created_at->format('Y m d, h:i A'),
            'files' => FileResource::collection($this->whenLoaded('files')),

        ];
    }
}
