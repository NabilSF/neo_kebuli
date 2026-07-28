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
        $thumbnailUrl = null;
        if ($this->thumbnail) {
            $thumbnailUrl = str_starts_with($this->thumbnail, 'http')
                ? $this->thumbnail
                : asset('storage/' . $this->thumbnail);
        }

        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'slug'          => $this->slug,
            'excerpt'       => $this->excerpt,
            'thumbnail_url' => $thumbnailUrl,
            'category'      => $this->relationLoaded('category') && $this->category ? $this->category->name : null,
            'published_at'  => $this->published_at ? $this->published_at->toISOString() : null,
            'author'        => $this->penulis ?? ($this->relationLoaded('author') && $this->author ? $this->author->name : 'Admin'),
            'content'       => $this->when($request->routeIs('*api.v1.news.show*') || isset($this->is_detail), $this->content),
        ];
    }
}
