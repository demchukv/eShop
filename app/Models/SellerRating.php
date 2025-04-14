<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerRating extends Model
{
    use HasFactory;

    protected $table = 'seller_ratings';

    protected $fillable = [
        'seller_id',
        'store_id',
        'order_id',
        'user_id',
        'comment',
        'quality_of_service',
        'on_time_delivery',
        'relevance_price_availability',
    ];

    /**
     * Отримати середній рейтинг на основі трьох оцінок.
     *
     * @return float
     */
    public function getAverageRatingAttribute(): float
    {
        return round(
            ($this->quality_of_service + $this->on_time_delivery + $this->relevance_price_availability) / 3,
            2
        );
    }

    /**
     * Відношення до продавця.
     */
    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    /**
     * Відношення до магазину.
     */
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    /**
     * Відношення до замовлення.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Відношення до користувача (покупця).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
