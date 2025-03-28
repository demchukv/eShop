<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderTracking extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'order_id',
        'shiprocket_order_id',
        'shipment_id',
        'courier_company_id',
        'awb_code',
        'pickup_status',
        'pickup_scheduled_date',
        'pickup_token_number',
        'status',
        'others',
        'pickup_generated_date',
        'data',
        'date',
        'is_canceled',
        'manifest_url',
        'label_url',
        'invoice_url',
        'order_item_id',
        'courier_agency',
        'tracking_id',
        'url',
        // Нові поля для Custom Carrier
        'carrier_id',        // Ідентифікатор перевізника зі списку
        'tracking_number',   // Трек-код, введений продавцем
        'aftership_tracking_id', // ID трекінгу від AfterShip
        'aftership_data',        // Повна відповідь від API AfterShip (тип text)
    ];
}
