<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductApproval extends Model
{
    protected $fillable = [
        'product_id',
        'manager_id',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
