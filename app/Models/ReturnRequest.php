<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReturnRequest extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'user_id',
        'product_id',
        'product_variant_id',
        'order_id',
        'order_item_id',
        'status',
        'remarks',
        'delivery_status',
        'reason',
        'application_type',
        'refund_amount',
        'refund_method',
        'description',
        'evidence_path',
        'return_method',
    ];
}
