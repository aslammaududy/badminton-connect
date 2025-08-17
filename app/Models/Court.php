<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Court extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'location', 'address', 'latitude', 'longitude', 'place_id', 'description', 'hourly_rate',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function matches()
    {
        return $this->hasMany(\App\Models\GameMatch::class);
    }
}
