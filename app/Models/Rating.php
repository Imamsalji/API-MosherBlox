<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Rating extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'ratable_id',
        'ratable_type',
        'score',
        'title',
        'body',
        'status',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    // ─── Relationships ───────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ratable(): MorphTo
    {
        return $this->morphTo();
    }

    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeByScore($query, int $score)
    {
        return $query->where('score', $score);
    }

    public function getLikesCountAttribute(): int
    {
        return $this->reactions()->where('type', 'like')->count();
    }

    public function getDislikesCountAttribute(): int
    {
        return $this->reactions()->where('type', 'dislike')->count();
    }
}
