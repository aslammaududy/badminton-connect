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

    public function save()
    {
        $data = $this->validate();
        $data['price'] = $this->price;
        $data['status'] = 'pending';
        Booking::create($data);
        session()->flash('success', 'Booking created');
        $this->reset(['user_id','court_id','start_time','end_time','price']);
    }

    public function render()
    {
        return view('livewire.bookings.create', [
            'users' => User::query()->limit(50)->get(['id','name']),
            'courts' => Court::query()->limit(50)->get(['id','name']),
        ]);
    }
}
