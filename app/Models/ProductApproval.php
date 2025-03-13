<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductApproval extends Model
{
    protected $fillable = [
        'product_id',
        'manager_id',
        'approved_at',
        'status', // Додано нове поле
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'status' => 'string', // Для enum
    ];

    // Дозволені значення для status
    const STATUS_APPROVED = 'approved';
    const STATUS_DISAPPROVED = 'disapproved';

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
