<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItems extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    // Додаємо поле is_completed до fillable
    protected $fillable = ['is_completed'];

    // Встановлюємо значення за замовчуванням
    protected $attributes = [
        'is_completed' => 0,
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Додаємо зв’язок із Product через product_variant_id
    public function product()
    {
        return $this->hasOneThrough(
            Product::class,
            Product_variants::class,
            'id',              // Foreign key у Product_variants
            'id',              // Foreign key у Product
            'product_variant_id', // Local key у OrderItems
            'product_id'       // Local key у Product_variants
        );
    }
}
