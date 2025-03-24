<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionDistribution extends Model
{
    protected $table = 'commission_distributions';

    protected $fillable = [
        'order_id',
        'user_id',
        'amount',
        'message',
        'status',
    ];

    // Доступні значення для status
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELED = 'canceled';

    protected $attributes = [
        'status' => self::STATUS_PENDING, // Значення за замовчуванням
    ];

    // Зв'язок із замовленням
    public function order()
    {
        return $this->belongsTo(Order::class); // Припускаємо, що у вас є модель Order
    }

    // Зв'язок із користувачем
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Скоп для фільтрації лише очікуваних платежів
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
