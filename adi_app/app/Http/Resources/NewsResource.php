<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class NewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'url' => $this->url,
            'embed_url' => $this->when(
                isset($this->embed_url), 
                $this->embed_url
            ),
            'thumbnail_url' => $this->when(
                isset($this->thumbnail_url), 
                $this->thumbnail_url
            ),
            'featured_image' => $this->when(
                $this->images->isNotEmpty(),
                function () {
                    $firstImage = $this->images->first();
                    return $firstImage ? Storage::url($firstImage->image) : null;
                }
            ),
            'images' => $this->whenLoaded('images', function () {
                return $this->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'url' => Storage::url($image->image),
                        'filename' => basename($image->image),
                        'created_at' => $image->created_at,
                    ];
                });
            }),
            'images_count' => $this->whenLoaded('images', function () {
                return $this->images->count();
            }),
            'creator' => $this->whenLoaded('creator', function () {
                return [
                    'id' => $this->creator->id,
                    'name' => $this->creator->name,
                    'email' => $this->creator->email,
                ];
            }),
            'updater' => $this->whenLoaded('updater', function () {
                return [
                    'id' => $this->updater->id,
                    'name' => $this->updater->name,
                    'email' => $this->updater->email,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_at_formatted' => $this->created_at?->format('d M Y H:i'),
            'updated_at_formatted' => $this->updated_at?->format('d M Y H:i'),
            'created_at_human' => $this->created_at?->diffForHumans(),
            'updated_at_human' => $this->updated_at?->diffForHumans(),
        ];
    }
}