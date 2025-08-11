<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' =>$this -> id,
            'title' => $this -> title,
            'content' => $this->content,
            'url' => $this->url,
            'slug' => $this->slug,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->creator?->name ?? 'Unknown',
            'updated_by' => $this->updater?->name ?? 'Unknown', 
        ];
    }
}
