<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    public $timestamps = false;

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Tag $tag) {
            $tag->slug ??= Str::slug($tag->name);
        });
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_tag');
    }
}
