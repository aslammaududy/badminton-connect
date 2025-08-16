<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameMatch extends Model
{
    use HasFactory;
    protected $table = 'matches';

    protected $fillable = [
        'organizer_id', 'tournament_id', 'court_id', 'start_time', 'end_time', 'status',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function court()
    {
        return $this->belongsTo(Court::class);
    }
}
