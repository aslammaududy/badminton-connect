<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_id', 'responder_id', 'status', 'message',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function responder()
    {
        return $this->belongsTo(User::class, 'responder_id');
    }
}

