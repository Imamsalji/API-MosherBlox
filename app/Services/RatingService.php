<?php

namespace App\Services;

use App\Models\Rating;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RatingService
{
    /**
     * Simpan rating baru. Satu user hanya bisa rating 1x per item.
     */
    public function store(User $user, Model $ratable, array $data): Rating
    {
        $exists = Rating::where('user_id', $user->id)
            ->where('ratable_id', $ratable->id)
            ->where('ratable_type', $ratable->getMorphClass())
            ->exists();

        if ($exists) {
            throw new HttpException(422, 'Kamu sudah memberikan rating untuk item ini.');
        }

        return DB::transaction(function () use ($user, $ratable, $data) {
            return Rating::create([
                'user_id'      => $user->id,
                'ratable_id'   => $ratable->id,
                'ratable_type' => $ratable->getMorphClass(),
                'score'        => $data['score'],
                'title'        => $data['title'] ?? null,
                'body'         => $data['body'] ?? null,
            ]);
        });
    }

    /**
     * Update rating milik user sendiri.
     */
    public function update(Rating $rating, array $data): Rating
    {
        $rating->update(array_filter([
            'score' => $data['score'] ?? null,
            'title' => $data['title'] ?? null,
            'body'  => $data['body'] ?? null,
        ], fn($v) => !is_null($v)));

        return $rating->fresh();
    }

    /**
     * Toggle like/dislike pada sebuah rating (review).
     * - Jika belum ada reaksi → insert
     * - Jika reaksi sama → hapus (toggle off)
     * - Jika reaksi beda → update
     */
    public function toggleReaction(User $user, Rating $rating, string $type): array
    {
        $reaction = Reaction::where('user_id', $user->id)
            ->where('reactable_id', $rating->id)
            ->where('reactable_type', Rating::class)
            ->first();

        if (!$reaction) {
            Reaction::create([
                'user_id'       => $user->id,
                'reactable_id'  => $rating->id,
                'reactable_type' => Rating::class,
                'type'          => $type,
            ]);
            $action = 'added';
        } elseif ($reaction->type === $type) {
            $reaction->delete();
            $action = 'removed';
        } else {
            $reaction->update(['type' => $type]);
            $action = 'changed';
        }

        return [
            'action'   => $action,
            'type'     => $action === 'removed' ? null : $type,
            'likes'    => $rating->reactions()->where('type', 'like')->count(),
            'dislikes' => $rating->reactions()->where('type', 'dislike')->count(),
        ];
    }
}
