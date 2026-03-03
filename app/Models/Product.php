<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'name',
        'price',
        'specification',
        'image',
        'image_url',
        'stock',
        'status'
    ];
    protected $appends = ['image_url'];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image);
    }
}
