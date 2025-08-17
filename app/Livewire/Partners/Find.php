<?php

namespace App\Livewire\Partners;

use App\Models\PartnerRequest;
use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Find extends Component
{
    #[Validate('required|exists:users,id')]
    public $requester_id;

    public $message = '';

    public ?float $latitude = null;
    public ?float $longitude = null;

    public ?float $centerLat = null;
    public ?float $centerLng = null;
    public ?int $radiusKm = null;

    protected $queryString = [
        'centerLat' => ['as' => 'lat'],
        'centerLng' => ['as' => 'lng'],
        'radiusKm' => ['as' => 'radius'],
    ];

    public function updated($name): void
    {
        // no pagination here, but may be used if added later
    }

    public function submit()
    {
        $data = $this->validate([
            'requester_id' => 'required|exists:users,id',
            'message' => 'nullable|string',
            'latitude' => 'sometimes|nullable|numeric|between:-90,90',
            'longitude' => 'sometimes|nullable|numeric|between:-180,180',
        ]);
        PartnerRequest::create([
            'requester_id' => $data['requester_id'],
            'message' => $data['message'] ?? null,
            'status' => 'open',
            'latitude' => $data['latitude'] ?? $this->latitude,
            'longitude' => $data['longitude'] ?? $this->longitude,
        ]);
        session()->flash('success', 'Partner request posted');
        $this->reset(['requester_id','message','latitude','longitude']);
    }

    public function render()
    {
        $q = PartnerRequest::query()->with('requester')->where('status','open');
        if ($this->centerLat !== null && $this->centerLng !== null) {
            $q->select('partner_requests.*')
              ->selectRaw('(
                    6371 * acos(
                        cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) +
                        sin(radians(?)) * sin(radians(latitude))
                    )
                ) as distance_km', [$this->centerLat, $this->centerLng, $this->centerLat])
              ->orderBy('distance_km');
            if ($this->radiusKm !== null) {
                $q->having('distance_km', '<=', (float) $this->radiusKm);
            }
        } else {
            $q->latest();
        }
        $openRequests = $q->limit(50)->get();
        $users = User::query()->limit(50)->get(['id','name']);
        return view('livewire.partners.find', compact('openRequests','users'));
    }
}
