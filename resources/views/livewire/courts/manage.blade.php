<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Create Court</h1>

    @if (session('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label class="block text-sm font-medium">Name</label>
            <input type="text" wire:model.live="name" class="border rounded p-2 w-full" />
            @error('name') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium">Location (city/area)</label>
            <input type="text" wire:model.live="location" class="border rounded p-2 w-full" />
            @error('location') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium">Search Address (Google Places)</label>
            <input id="courtPlaceInput" type="text" placeholder="Cari alamat/tempat" class="border rounded p-2 w-full" />
            <div class="text-xs text-gray-600 mt-1">Pilih hasil untuk mengisi alamat dan koordinat otomatis.</div>
        </div>

        <div>
            <label class="block text-sm font-medium">Address</label>
            <input type="text" wire:model.live="address" class="border rounded p-2 w-full" />
            @error('address') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium">Latitude</label>
                <input type="number" step="0.000001" wire:model.live="latitude" class="border rounded p-2 w-full" />
                @error('latitude') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Longitude</label>
                <input type="number" step="0.000001" wire:model.live="longitude" class="border rounded p-2 w-full" />
                @error('longitude') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium">Place ID</label>
            <input type="text" wire:model.live="place_id" class="border rounded p-2 w-full" />
            @error('place_id') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium">Description</label>
            <textarea wire:model.live="description" class="border rounded p-2 w-full" rows="3"></textarea>
            @error('description') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium">Hourly Rate</label>
            <input type="number" step="0.01" wire:model.live="hourly_rate" class="border rounded p-2 w-full" />
            @error('hourly_rate') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded" wire:loading.attr="disabled">Save</button>
    </form>

    <script>
        function initCourtAutocomplete() {
            const input = document.getElementById('courtPlaceInput');
            if (!input) return;
            const ac = new google.maps.places.Autocomplete(input, { fields: ['formatted_address','geometry','place_id','name'] });
            ac.addListener('place_changed', () => {
                const place = ac.getPlace();
                if (!place || !place.geometry) return;
                const addr = place.formatted_address || place.name || '';
                const lat = place.geometry.location.lat();
                const lng = place.geometry.location.lng();
                @this.set('address', addr);
                @this.set('latitude', lat);
                @this.set('longitude', lng);
                @this.set('place_id', place.place_id || null);
                if (!@this.get('name') && place.name) {
                    @this.set('name', place.name);
                }
            });
        }
        window.initCourtAutocomplete = initCourtAutocomplete;
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initCourtAutocomplete" async defer></script>
</div>

