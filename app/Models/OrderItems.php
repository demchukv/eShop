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
}
