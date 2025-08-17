<div class="p-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Courts</h1>
        @auth
            <a href="{{ route('courts.create') }}" class="bg-blue-600 text-white px-3 py-2 rounded">Create Court</a>
        @endauth
    </div>

    <div class="grid gap-4 md:grid-cols-3 mb-4">
        <div class="md:col-span-2">
            <div id="courts-map" class="w-full h-80 rounded border" wire:ignore></div>
        </div>
        <div class="space-y-3">
            <div>
                <label class="block text-sm font-medium">Cari lokasi</label>
                <input id="courtPlaceSearch" type="text" placeholder="Cari alamat/tempat" class="border rounded p-2 w-full" />
            </div>
            <div class="flex gap-2">
                <button type="button" class="bg-gray-100 px-3 py-2 rounded border" onclick="useMyLocation()">Gunakan lokasiku</button>
                <button type="button" class="bg-gray-100 px-3 py-2 rounded border" onclick="resetCenter()">Reset</button>
            </div>
            <div>
                <label class="block text-sm font-medium">Radius (km)</label>
                <input type="number" min="1" max="100" wire:model.live="radiusKm" class="border rounded p-2 w-full" />
            </div>
        </div>
    </div>
    <div class="space-y-2">
        @foreach ($courts as $court)
            <div class="border p-3 rounded">
                <div class="font-semibold">{{ $court->name }}</div>
                <div class="text-sm text-gray-600">{{ $court->location }}</div>
                <div class="text-sm">{{ $court->description }}</div>
                @if($court->hourly_rate)
                    <div class="text-sm">Rate: {{ number_format($court->hourly_rate, 2) }}</div>
                @endif
                @if(isset($court->distance_km))
                    <div class="text-xs text-gray-600">~ {{ number_format($court->distance_km, 2) }} km</div>
                @endif
            </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $courts->links() }}</div>
    <script id="courts-data" type="application/json">@json($courts->items())</script>

    <script>
        let map, markers=[];

        function getCourtsData() {
            try { return JSON.parse(document.getElementById('courts-data').textContent || '[]'); } catch (e) { return []; }
        }

        function initMap() {
            const data = getCourtsData();
            const defaultCenter = { lat: data[0]?.latitude ? parseFloat(data[0].latitude) : -6.200000, lng: data[0]?.longitude ? parseFloat(data[0].longitude) : 106.816666 };
            map = new google.maps.Map(document.getElementById('courts-map'), { center: defaultCenter, zoom: 12 });

            initAutocomplete();
            renderMarkers();

            // Observe data updates from Livewire rerenders
            const dataEl = document.getElementById('courts-data');
            if (dataEl) {
                const obs = new MutationObserver(() => { renderMarkers(); initAutocomplete(); });
                obs.observe(dataEl, { childList: true, characterData: true, subtree: true });
            }
        }

        function renderMarkers() {
            markers.forEach(m => m.setMap(null));
            markers = [];
            const data = getCourtsData();
            data.forEach(c => {
                if (!c.latitude || !c.longitude) return;
                const m = new google.maps.Marker({ position: {lat: parseFloat(c.latitude), lng: parseFloat(c.longitude)}, map, title: c.name });
                const info = new google.maps.InfoWindow({ content: `<div><strong>${c.name}</strong><br>${c.location ?? ''}<br>${c.address ?? ''}<br>${c.hourly_rate ? 'Rate: ' + c.hourly_rate : ''}</div>` });
                m.addListener('click', () => info.open({anchor: m, map}));
                markers.push(m);
            });
        }

        function initAutocomplete() {
            const input = document.getElementById('courtPlaceSearch');
            if (!input || !google?.maps?.places) return;
            const autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.addListener('place_changed', () => {
                const place = autocomplete.getPlace();
                if (!place?.geometry) return;
                const loc = place.geometry.location;
                map.setCenter(loc);
                map.setZoom(14);
                if (window.Livewire) {
                    const c = { lat: loc.lat(), lng: loc.lng() };
                    @this.set('centerLat', c.lat);
                    @this.set('centerLng', c.lng);
                }
            });
        }

        function useMyLocation() {
            if (!navigator.geolocation) return;
            navigator.geolocation.getCurrentPosition(pos => {
                const { latitude, longitude } = pos.coords;
                map.setCenter({ lat: latitude, lng: longitude });
                map.setZoom(14);
                if (window.Livewire) {
                    @this.set('centerLat', latitude);
                    @this.set('centerLng', longitude);
                }
            });
        }
        function resetCenter() {
            if (window.Livewire) {
                @this.set('centerLat', null);
                @this.set('centerLng', null);
                @this.set('radiusKm', null);
            }
        }

        window.initCourtsMap = initMap;
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initCourtsMap" async defer></script>
</div>
