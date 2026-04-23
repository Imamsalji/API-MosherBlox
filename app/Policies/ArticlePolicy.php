<?php

namespace App\Policies;

use App\Models\Articles\Article;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticlePolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability)
    {
        if ($user->role === 'admin') {
            return true;
        }
    }

    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function view(User $user, Article $article): bool
    {
        return $user->id === $article->author_id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Article $article): bool
    {
        return $user->id === $article->author_id;
    }

    public function delete(User $user, Article $article): bool
    {
        return $user->id === $article->author_id;
    }
}
