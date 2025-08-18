<?php

namespace App\Livewire\Bookings;

use App\Models\Booking;
use App\Models\Court;
use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    #[Validate('required|exists:users,id')]
    public $user_id;

    #[Validate('required|exists:courts,id')]
    public $court_id;

    #[Validate('required|date')]
    public $start_time;

    #[Validate('required|date|after:start_time')]
    public $end_time;

    public $price;

    #[Validate('required|integer|min:2|max:8')]
    public int $desired_size = 8;

    // Only true when user initiates booking from map flow
    public bool $open_to_join = false;

    // For UI: indicates booking initiated from map
    public bool $from_map = false;

    public function mount(): void
    {
        $this->from_map = (bool) request()->boolean('from_map');
        if ($this->from_map) {
            $this->open_to_join = true; // enabled only for map-initiated flow
        }
        $this->court_id = $this->court_id ?: request()->query('court_id');
    }

    public function save()
    {
        $data = $this->validate([
            'user_id' => 'required|exists:users,id',
            'court_id' => 'required|exists:courts,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'desired_size' => 'required|integer|min:2|max:8',
        ]);
        $data['price'] = $this->price;
        $data['status'] = 'pending';
        // Enforce open_to_join based on flow: only true when from_map flag is present
        $data['open_to_join'] = $this->from_map ? true : false;
        $data['desired_size'] = $this->desired_size;

        Booking::create($data);
        session()->flash('success', 'Booking created');
        $this->reset(['user_id','court_id','start_time','end_time','price','desired_size','open_to_join','from_map']);
    }

    public function render()
    {
        return view('livewire.bookings.create', [
            'users' => User::query()->limit(50)->get(['id','name']),
            'courts' => Court::query()->limit(50)->get(['id','name']),
        ]);
    }
}
