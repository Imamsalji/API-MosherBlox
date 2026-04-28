<?php

namespace App\Models\Concerns;

use App\Models\Reaction;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasReactions
{
    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactable')->where('type', 'like');
    }

    public function dislikes(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactable')->where('type', 'dislike');
    }
}
