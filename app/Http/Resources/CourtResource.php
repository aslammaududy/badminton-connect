<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourtResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'location' => $this->location,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'place_id' => $this->place_id,
            'description' => $this->description,
            'hourly_rate' => $this->hourly_rate,
            'distance_km' => $this->when(isset($this->distance_km), round((float) $this->distance_km, 3)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
