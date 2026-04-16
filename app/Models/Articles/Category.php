<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    public $timestamps = false;

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Category $category) {
            $category->slug ??= Str::slug($category->name);
        });
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_category');
    }
}
