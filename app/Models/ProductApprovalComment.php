<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductApprovalComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'manager_id',
        'comment',
        'reason', // Додано нове поле
    ];

    protected $casts = [
        'reason' => 'array', // Зберігатимемо як JSON-масив
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id', 'id');
    }
}
