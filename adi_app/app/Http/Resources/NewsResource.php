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

         $videoId = $this->extractYoutubeVideoId($this->url);

        return [
            'id' =>$this -> id,
            'title' => $this -> title,
            'content' => $this->content,
            'url' => $this->url,
            'url_thumbnail' => $this ->getYoutubeThumbnailUrl($videoId),
            'slug' => $this->slug,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->creator?->name ?? 'Unknown',
            'updated_by' => $this->updater?->name ?? 'Unknown', 
        ];
    }

    protected function extractYoutubeVideoId(string $url): ?string
    {
        $patterns = [
            '/[?&]v=([^&]+)/',          
            '/youtu\.be\/([^?&]+)/',   
            '/embed\/([^?&]+)/',      
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    protected function getYoutubeThumbnailUrl(string $videoId): string
    {
        return "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg";
    }

     protected function getDefaultThumbnailUrl(): string
    {
        return 'https://via.placeholder.com/480x360?text=No+Thumbnail';
    }
}
