<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent;

class Region extends Model
{
    protected $fillable = [
        'name',
        'native_name',
        'admin1_code',
        'country_id',
        'minimum_free_delivery_order_amount',
        'delivery_charges',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function cities()
    {
        return $this->hasMany(City::class, 'region_id');
    }
}
