<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class Court extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'location', 'address', 'latitude', 'longitude', 'place_id', 'description', 'hourly_rate', 'total_courts', 'owner_user_id',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function matches()
    {
        return $this->hasMany(\App\Models\GameMatch::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    protected static function booted(): void
    {
        static::saving(function (Court $model): void {
            $driver = $model->getConnection()->getDriverName();

            if ($driver === 'sqlite') {
                // Keep SQLite simple: only lat/long decimals are used.
                return;
            }

            $lat = $model->latitude;
            $lon = $model->longitude;

            if ($lat !== null && $lon !== null) {
                $latF = (float) $lat;
                $lonF = (float) $lon;

                if ($driver === 'mysql') {
                    $model->setAttribute('location_point', DB::raw("ST_SRID(POINT($lonF, $latF), 4326)"));
                } elseif ($driver === 'pgsql') {
                    // PostgreSQL native point type
                    $model->setAttribute('location_point', DB::raw(sprintf("'(%F,%F)'::point", $lonF, $latF)));
                } else {
                    $model->setAttribute('location_point', null);
                }
            } else {
                $model->setAttribute('location_point', null);
            }
        });
    }

    public function scopeWithDistanceFrom(Builder $query, float $latitude, float $longitude): Builder
    {
        $connection = $this->getConnection();
        $driver = $connection->getDriverName();

        $query->whereNotNull('latitude')->whereNotNull('longitude');

        if ($driver === 'mysql') {
            return $query->select('*')->selectRaw(
                'ST_Distance_Sphere(location_point, POINT(?, ?)) / 1000 as distance_km',
                [$longitude, $latitude]
            );
        }

        if ($driver === 'pgsql') {
            return $query->select('*')->selectRaw(
                '6371 * acos(least(1, greatest(-1, sin(radians(?)) * sin(radians(latitude)) + cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude - ?))))) as distance_km',
                [$latitude, $latitude, $longitude]
            );
        }

        // SQLite fallback: planar approximation in meters -> km
        $kLat = 111320.0;
        $kLon = 111320.0 * cos(deg2rad($latitude));
        return $query->select('*')->selectRaw(
            'sqrt( pow(? * (latitude - ?), 2 ) + pow(? * (longitude - ?), 2 ) ) / 1000 as distance_km',
            [$kLat, $latitude, $kLon, $longitude]
        );
    }

    public function scopeNearestTo(Builder $query, float $latitude, float $longitude): Builder
    {
        $connection = $this->getConnection();
        $driver = $connection->getDriverName();

        // Ensure distance_km is present and order by it
        $this->scopeWithDistanceFrom($query, $latitude, $longitude);

        if ($driver === 'mysql') {
            return $query->orderBy('distance_km');
        }
        if ($driver === 'pgsql') {
            return $query->orderBy('distance_km');
        }
        return $query->orderBy('distance_km');
    }

    public function scopeWithinRadius(Builder $query, float $latitude, float $longitude, float $radiusMeters): Builder
    {
        $connection = $this->getConnection();
        $driver = $connection->getDriverName();

        $query->whereNotNull('latitude')->whereNotNull('longitude');

        if ($driver === 'mysql') {
            return $query->whereRaw(
                'ST_Distance_Sphere(location_point, POINT(?, ?)) <= ?',
                [$longitude, $latitude, $radiusMeters]
            );
        }

        if ($driver === 'pgsql') {
            return $query->whereRaw(
                '(
                    6371000 * acos(least(1, greatest(-1, sin(radians(?)) * sin(radians(latitude)) + cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude - ?)))))
                ) <= ?',
                [$latitude, $latitude, $longitude, $radiusMeters]
            );
        }

        // SQLite fallback: ellipse approximation in meters
        $kLat = 111320.0;
        $kLon = 111320.0 * cos(deg2rad($latitude));
        return $query->whereRaw(
            '((? * (latitude - ?)) * (? * (latitude - ?)) + (? * (longitude - ?)) * (? * (longitude - ?))) <= (? * ?)',
            [$kLat, $latitude, $kLat, $latitude, $kLon, $longitude, $kLon, $longitude, $radiusMeters, $radiusMeters]
        );
    }
}
