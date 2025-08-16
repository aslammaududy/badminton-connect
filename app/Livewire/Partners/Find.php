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

    public function submit()
    {
        $data = $this->validate([
            'requester_id' => 'required|exists:users,id',
            'message' => 'nullable|string',
        ]);
        PartnerRequest::create([
            'requester_id' => $data['requester_id'],
            'message' => $data['message'] ?? null,
            'status' => 'open',
        ]);
        session()->flash('success', 'Partner request posted');
        $this->reset(['requester_id','message']);
    }

    public function render()
    {
        $openRequests = PartnerRequest::query()->with('requester')->where('status','open')->latest()->limit(20)->get();
        $users = User::query()->limit(50)->get(['id','name']);
        return view('livewire.partners.find', compact('openRequests','users'));
    }
}
