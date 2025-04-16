<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisputMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'disput_id',
        'sender_id',
        'message',
        'refund_amount',
        'application_type',
        'refund_method',
        'evidence_path',
        'proposal_status',
    ];

    public function disput()
    {
        return $this->belongsTo(Disput::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function getEvidencePathAttribute($value)
    {
        return is_string($value) ? json_decode($value, true) ?? [] : ($value ?? []);
    }

    public function setEvidencePathAttribute($value)
    {
        $this->attributes['evidence_path'] = is_array($value) ? json_encode($value) : $value;
    }
}
