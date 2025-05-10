<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'transaction_type',
        'user_id',
        'order_id',
        'order_item_id',
        'type',
        'txn_id',
        'payu_txn_id',
        'amount',
        'fee',
        'status',
        'currency_code',
        'payer_email',
        'message',
        'transaction_date',
        'is_refund',
        'refund_amount',    // Додано
        'refund_status',    // Додано
        'refund_id',        // Додано
    ];


    protected $attributes = [
        'fee' => 0,
        'refund_amount' => 0,    // За замовчуванням 0
        'refund_status' => null, // За замовчуванням null
        'refund_id' => null,     // За замовчуванням null
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
