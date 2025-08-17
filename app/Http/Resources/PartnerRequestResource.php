<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartnerRequestResource extends JsonResource
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
            'requester_id' => $this->requester_id,
            'responder_id' => $this->responder_id,
            'status' => $this->status,
            'message' => $this->message,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'distance_km' => $this->when(isset($this->distance_km), round((float) $this->distance_km, 3)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
