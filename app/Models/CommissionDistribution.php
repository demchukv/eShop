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
    ];
}
