<?php

namespace App\Livewire\Tournaments;

use App\Models\Tournament;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        $tournaments = Tournament::query()->latest()->paginate(10);
        return view('livewire.tournaments.index', compact('tournaments'));
    }
}
