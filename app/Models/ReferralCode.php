<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralCode extends Model
{
    protected $fillable = ['code', 'product_id', 'dealer_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function dealer()
    {
        return $this->belongsTo(User::class, 'dealer_id');
    }
}
