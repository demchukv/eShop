<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent;

class City extends Model
{
    protected $fillable = [
        'name',
        'native_name',
        'country_id',
        'region_id',
        'minimum_free_delivery_order_amount',
        'delivery_charges',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }
}
