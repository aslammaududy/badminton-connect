<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class PartnerRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_id', 'responder_id', 'status', 'message', 'latitude', 'longitude',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function responder()
    {
        return $this->belongsTo(User::class, 'responder_id');
    }

    protected static function booted(): void
    {
        static::saving(function (PartnerRequest $model): void {
            $driver = $model->getConnection()->getDriverName();

            if ($driver === 'sqlite') {
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
            return $query->select('partner_requests.*')->selectRaw(
                'ST_Distance_Sphere(location_point, POINT(?, ?)) / 1000 as distance_km',
                [$longitude, $latitude]
            );
        }

        if ($driver === 'pgsql') {
            return $query->select('partner_requests.*')->selectRaw(
                '6371 * acos(least(1, greatest(-1, sin(radians(?)) * sin(radians(latitude)) + cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude - ?))))) as distance_km',
                [$latitude, $latitude, $longitude]
            );
        }

        // SQLite fallback: planar approximation in meters -> km
        $kLat = 111320.0;
        $kLon = 111320.0 * cos(deg2rad($latitude));
        return $query->select('partner_requests.*')->selectRaw(
            'sqrt( pow(? * (latitude - ?), 2 ) + pow(? * (longitude - ?), 2 ) ) / 1000 as distance_km',
            [$kLat, $latitude, $kLon, $longitude]
        );
    }

    public function scopeNearestTo(Builder $query, float $latitude, float $longitude): Builder
    {
        $this->scopeWithDistanceFrom($query, $latitude, $longitude);
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
