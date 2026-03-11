<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
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
            'format' => $this->format,
            'size' => $this->size,
            'visibility' => $this->visibility,
            'download_count' => $this->download_count,
            'uploaded_at' => $this->created_at,
            'folder_id' => $this->folder_id,
        ];
    }
}
