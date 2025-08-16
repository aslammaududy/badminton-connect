<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Create Booking</h1>
    @if (session('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    <form wire:submit.prevent="save" class="space-y-3">
        <div>
            <label class="block text-sm font-medium">User</label>
            <select wire:model.live="user_id" class="border rounded p-2 w-full">
                <option value="">Select user</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            @error('user_id') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Court</label>
            <select wire:model.live="court_id" class="border rounded p-2 w-full">
                <option value="">Select court</option>
                @foreach ($courts as $court)
                    <option value="{{ $court->id }}">{{ $court->name }}</option>
                @endforeach
            </select>
            @error('court_id') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Start Time</label>
            <input type="datetime-local" wire:model.live="start_time" class="border rounded p-2 w-full" />
            @error('start_time') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">End Time</label>
            <input type="datetime-local" wire:model.live="end_time" class="border rounded p-2 w-full" />
            @error('end_time') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Price (optional)</label>
            <input type="number" step="0.01" wire:model.live="price" class="border rounded p-2 w-full" />
            @error('price') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded" wire:loading.attr="disabled">Save</button>
    </form>
</div>
