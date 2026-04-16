<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleMeta extends Model
{
    public $timestamps = false;

    protected $fillable = ['article_id', 'meta_key', 'meta_value'];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
