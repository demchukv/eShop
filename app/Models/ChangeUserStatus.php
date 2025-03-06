<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeUserStatus extends Model
{
    use HasFactory;

    protected $table = 'user_statuses';

    protected $casts = [
        'photos' => 'array'
    ];

    protected $fillable = [
        'user_id',
        'type',
        'status',
        'photos',
        'message',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPhotosAttribute($value)
    {
        return json_decode($value, true);
    }
}
