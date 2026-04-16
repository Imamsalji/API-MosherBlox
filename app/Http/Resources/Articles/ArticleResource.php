<?php

namespace App\Http\Resources\Articles;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'excerpt'      => $this->excerpt,
            'content'      => $this->when(
                $request->routeIs('articles.show'),
                $this->content
            ),
            'thumbnail_url' => $this->thumbnail_url,
            'status'       => $this->status,
            'published_at' => $this->published_at?->toISOString(),
            'created_at'   => $this->created_at->toISOString(),
            'updated_at'   => $this->updated_at->toISOString(),

            // Relasi — hanya muncul jika di-load
            'author'       => new UserResource($this->whenLoaded('author')),
            'categories'   => CategoryResource::collection($this->whenLoaded('categories')),
            'tags'         => TagResource::collection($this->whenLoaded('tags')),
            'comments'     => CommentResource::collection($this->whenLoaded('comments')),

            // Count — hanya muncul jika di-withCount
            'comments_count' => $this->whenCounted('comments'),
            'views_count'    => $this->whenCounted('views'),
        ];
    }
}
