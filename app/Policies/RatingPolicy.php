<?php

namespace App\Policies;

use App\Models\Rating;
use App\Models\User;

class RatingPolicy
{
    public function update(User $user, Rating $rating): bool
    {
        return $user->id === $rating->user_id;
    }

    public function delete(User $user, Rating $rating): bool
    {
        // User bisa hapus punya sendiri, admin bisa hapus semua
        return $user->id === $rating->user_id || $user->is_admin;
    }
}
