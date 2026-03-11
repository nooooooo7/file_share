<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class folderResource extends JsonResource
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
            'files count' => $this->files_count,
            'description'=>$this->description,
            'created_by' => $this->user->name,
            'files' => FileResource::collection($this->whenLoaded('files'))

        ];
    }
}
