<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_price',
        'status',
        'payment_proof',
        'payment_proof_url',
        'bukti_admin',
        'bukti_admin_url',
        'email',
        'username',
        'admin_note',
        'user_note'
    ];
    protected $appends = ['payment_proof_url', 'bukti_admin_url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getPaymentProofUrlAttribute()
    {
        return asset('storage/' . $this->payment_proof);
    }

    public function getBuktiAdminUrlAttribute()
    {
        return asset('storage/' . $this->bukti_admin);
    }
}
