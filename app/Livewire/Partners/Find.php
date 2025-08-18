<?php

namespace App\Livewire\Partners;

use App\Models\PartnerRequest;
use App\Models\User;
use App\Models\Booking;
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
            $q->withDistanceFrom($this->centerLat, $this->centerLng)
              ->nearestTo($this->centerLat, $this->centerLng);
            if ($this->radiusKm !== null) {
                $q->withinRadius($this->centerLat, $this->centerLng, (float) $this->radiusKm * 1000);
            }
        } else {
            $q->latest();
        }
        $openRequests = $q->limit(50)->get();

        // Open sessions: bookings created from map flow and open to join
        $sessions = Booking::query()
            ->with(['court:id,name,latitude,longitude,address', 'user:id,name'])
            ->withCount(['acceptedParticipants as accepted_count'])
            ->where('open_to_join', true)
            ->when($this->centerLat !== null && $this->centerLng !== null, function ($b) {
                $lat = (float) $this->centerLat; $lng = (float) $this->centerLng;
                $radius = $this->radiusKm !== null ? ((float) $this->radiusKm * 1000) : null;
                $b->whereHas('court', function ($q) use ($lat, $lng, $radius) {
                    $q->withDistanceFrom($lat, $lng)->nearestTo($lat, $lng);
                    if ($radius !== null) {
                        $q->withinRadius($lat, $lng, $radius);
                    }
                });
            })
            ->latest('start_time')
            ->limit(100)
            ->get()
            ->map(function ($s) {
                $accepted = (int) ($s->accepted_count ?? 0);
                $desired = (int) ($s->desired_size ?? 8);
                $remaining = max($desired - (1 + $accepted), 0);
                return [
                    'id' => $s->id,
                    'host' => $s->user?->name,
                    'start_time' => optional($s->start_time)->toDateTimeString(),
                    'end_time' => optional($s->end_time)->toDateTimeString(),
                    'desired_size' => $desired,
                    'accepted' => $accepted,
                    'remaining' => $remaining,
                    'court' => [
                        'id' => $s->court?->id,
                        'name' => $s->court?->name,
                        'latitude' => $s->court?->latitude,
                        'longitude' => $s->court?->longitude,
                        'address' => $s->court?->address,
                    ],
                ];
            });
        $users = User::query()->limit(50)->get(['id','name']);
        return view('livewire.partners.find', [
            'openRequests' => $openRequests,
            'users' => $users,
            'openSessions' => $sessions,
        ]);
    }
}
