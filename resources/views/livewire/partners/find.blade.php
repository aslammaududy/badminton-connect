<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Find Partners</h1>
    @if (session('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    <form wire:submit.prevent="submit" class="space-y-3 mb-6">
        <div>
            <label class="block text-sm font-medium">Requester</label>
            <select wire:model.live="requester_id" class="border rounded p-2 w-full">
                <option value="">Select user</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            @error('requester_id') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Message</label>
            <textarea wire:model.live="message" class="border rounded p-2 w-full" rows="3"></textarea>
            @error('message') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded" wire:loading.attr="disabled">Post Request</button>
    </form>

    <h2 class="text-xl font-semibold mb-2">Open Requests</h2>
    <div class="space-y-2">
        @forelse ($openRequests as $req)
            <div class="border p-3 rounded">
                <div class="font-semibold">{{ $req->requester->name }}</div>
                <div class="text-sm">{{ $req->message }}</div>
            </div>
        @empty
            <div class="text-gray-600">No open requests yet.</div>
        @endforelse
    </div>
</div>
