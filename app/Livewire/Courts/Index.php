<?php

namespace App\Livewire\Courts;

use App\Models\Court;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        $courts = Court::query()->latest()->paginate(10);
        return view('livewire.courts.index', compact('courts'));
    }
}
