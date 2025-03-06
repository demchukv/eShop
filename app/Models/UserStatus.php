<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'role',
        'first_name',
        'last_name',
        'birthdate',
        'passport',
        'tax_id',
        'photos',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
