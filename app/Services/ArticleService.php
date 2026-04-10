<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ArticleService
{
    public function __construct(
        private readonly Article $model
    ) {}

    //Get ARTICLE WITH PAGINATE
    public function getAllPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['author', 'categories', 'tags'])
            ->when(
                isset($filters['status']),
                fn($q) => $q->where('status', $filters['status'])
            )
            ->when(
                isset($filters['published_at']),
                fn($q) => $q->forMonth($filters['Month'])
            )
            ->latest()
            ->paginate($perPage);
    }
}
