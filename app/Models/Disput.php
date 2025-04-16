<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disput extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_request_id',
        'user_id',
        'seller_id',
        'status',
        'admin_requested_at',
        'admin_requester_id',
    ];

    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function messages()
    {
        return $this->hasMany(DisputMessage::class);
    }
}
