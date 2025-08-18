<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'court_id', 'start_time', 'end_time', 'status', 'price', 'desired_size', 'open_to_join',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'price' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    public function participants()
    {
        return $this->hasMany(BookingParticipant::class);
    }

    public function acceptedParticipants()
    {
        return $this->participants()->where('status', 'accepted');
    }
}
