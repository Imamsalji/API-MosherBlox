<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RatingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'score'      => $this->score,
            'title'      => $this->title,
            'body'       => $this->body,
            'status'     => $this->status,
            'likes'      => $this->reactions->where('type', 'like')->count(),
            'dislikes'   => $this->reactions->where('type', 'dislike')->count(),
            'my_reaction' => $this->when(
                auth()->check(),
                fn() => optional(
                    $this->reactions->firstWhere('user_id', auth()->id())
                )->type
            ),
            'user' => [
                'id'     => $this->user->id,
                'name'   => $this->user->name,
                'avatar' => $this->user->avatar_url ?? null,
            ],
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
