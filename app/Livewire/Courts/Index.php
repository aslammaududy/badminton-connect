<?php

namespace App\Livewire\Courts;

use App\Models\Court;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public ?float $centerLat = null;
    public ?float $centerLng = null;
    public ?int $radiusKm = null;

    protected $queryString = [
        'centerLat' => ['as' => 'lat'],
        'centerLng' => ['as' => 'lng'],
        'radiusKm' => ['as' => 'radius'],
        'page' => ['except' => 1],
    ];

    public function updated($name): void
    {
        if (in_array($name, ['centerLat','centerLng','radiusKm'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $q = Court::query();

        if ($this->centerLat !== null && $this->centerLng !== null) {
            $q->withDistanceFrom($this->centerLat, $this->centerLng)
              ->nearestTo($this->centerLat, $this->centerLng);

            if ($this->radiusKm !== null) {
                $q->withinRadius($this->centerLat, $this->centerLng, (float) $this->radiusKm * 1000);
            }
        } else {
            $q->latest();
        }

        $courts = $q->paginate(10);
        return view('livewire.courts.index', compact('courts'));
    }
}
