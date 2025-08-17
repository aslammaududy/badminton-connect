<?php

namespace App\Livewire\Courts;

use App\Models\Court;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Manage extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:255')]
    public ?string $location = null;

    #[Validate('nullable|string|max:255')]
    public ?string $address = null;

    #[Validate('nullable|numeric|between:-90,90')]
    public $latitude = null;

    #[Validate('nullable|numeric|between:-180,180')]
    public $longitude = null;

    #[Validate('nullable|string|max:255')]
    public ?string $place_id = null;

    #[Validate('nullable|string')]
    public ?string $description = null;

    #[Validate('nullable|numeric|between:0,999999.99')]
    public $hourly_rate = null;

    public function save()
    {
        $data = $this->validate();
        Court::create($data);
        session()->flash('success', 'Court created');
        $this->reset(['name','location','address','latitude','longitude','place_id','description','hourly_rate']);
    }

    public function render()
    {
        return view('livewire.courts.manage');
    }
}

