<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'mobile',
        'alternate_mobile',
        'address',
        'landmark',
        'area_id',
        'city_id',
        'region_id',
        'city',
        'area',
        'pincode',
        'system_pincode',
        'country_code',
        'state',
        'country',
        'country_id',
        'zipcode_id',
        'latitude',
        'longitude',
        'is_default',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function zipcode()
    {
        return $this->belongsTo(Zipcode::class, 'zipcode_id');
    }
}
