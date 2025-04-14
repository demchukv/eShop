<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisputMessage extends Model
{
    protected $fillable = [
        'disput_id',
        'sender_id',
        'message',
    ];

    // Зв’язок із диспутом
    public function disput()
    {
        return $this->belongsTo(Disput::class);
    }

    // Зв’язок із відправником (користувачем або продавцем)
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
