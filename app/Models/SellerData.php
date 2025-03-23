<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerData extends Model
{
    protected $table = 'seller_data';
    protected $fillable = [
        'user_id',
        'national_identity_card',
        'authorized_signature',
        'disk',
        'pan_number',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
