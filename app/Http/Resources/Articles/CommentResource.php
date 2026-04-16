<?php

namespace App\Http\Resources\Articles;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'author_name' => $this->author_name, // accessor dari model
            'content'     => $this->content,
            'created_at'  => $this->created_at?->toISOString(),
        ];
    }
}
