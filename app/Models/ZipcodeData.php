<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZipcodeData extends Model
{
    protected $table = 'zipcodes_data';
    protected $primaryKey = 'id';
    public $incrementing = false; // id не автоінкрементне
    protected $fillable = ['id', 'data', 'created_at', 'updated_at'];
    protected $casts = [
        'data' => 'array', // Автоматична десеріалізація JSON
    ];

    public function zipcode()
    {
        return $this->belongsTo(Zipcode::class, 'id', 'id');
    }
}
