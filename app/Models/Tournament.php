<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'location', 'start_date', 'end_date', 'description', 'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function participants()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function matches()
    {
        return $this->hasMany(\App\Models\GameMatch::class);
    }
}
