<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disput extends Model
{
    protected $fillable = [
        'return_request_id',
        'user_id',
        'seller_id',
        'status',
    ];

    // Зв’язок із запитом на повернення
    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class);
    }

    // Зв’язок із повідомленнями диспуту
    public function messages()
    {
        return $this->hasMany(DisputMessage::class);
    }

    // Зв’язок із користувачем
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Зв’язок із продавцем
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
