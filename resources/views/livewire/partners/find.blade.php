<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Find Partners</h1>
    @if (session('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    <div class="grid gap-4 md:grid-cols-3 mb-6">
        <div class="md:col-span-2">
            <div id="partners-map" class="w-full h-80 rounded border" wire:ignore></div>
        </div>
        <div class="space-y-3">
            <div>
                <label class="block text-sm font-medium">Cari lokasi</label>
                <input id="partnerPlaceSearch" type="text" placeholder="Cari alamat/tempat" class="border rounded p-2 w-full" />
            </div>
            <div class="flex gap-2">
                <button type="button" class="bg-gray-100 px-3 py-2 rounded border" onclick="useMyPartnerLocation()">Gunakan lokasiku</button>
                <button type="button" class="bg-gray-100 px-3 py-2 rounded border" onclick="resetPartnerCenter()">Reset</button>
            </div>
            <div>
                <label class="block text-sm font-medium">Radius (km)</label>
                <input type="number" min="1" max="100" wire:model.live="radiusKm" class="border rounded p-2 w-full" />
            </div>
        </div>
    </div>

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
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium">Latitude</label>
                <input type="number" step="0.000001" wire:model.live="latitude" class="border rounded p-2 w-full" />
            </div>
            <div>
                <label class="block text-sm font-medium">Longitude</label>
                <input type="number" step="0.000001" wire:model.live="longitude" class="border rounded p-2 w-full" />
            </div>
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
                @if(isset($req->distance_km))
                    <div class="text-xs text-gray-600">~ {{ number_format($req->distance_km, 2) }} km</div>
                @endif
            </div>
        @empty
            <div class="text-gray-600">No open requests yet.</div>
        @endforelse
    </div>

    <h2 class="text-xl font-semibold mt-6 mb-2">Open Sessions (from bookings)</h2>
    <div class="space-y-2">
        @forelse ($openSessions as $s)
            <div class="border p-3 rounded">
                <div class="font-semibold">{{ $s['court']['name'] ?? 'Court' }} — Host: {{ $s['host'] ?? '-' }}</div>
                <div class="text-sm">{{ $s['start_time'] }} - {{ $s['end_time'] }}</div>
                <div class="text-sm">Remaining: {{ $s['remaining'] }} / {{ $s['desired_size'] }}</div>
            </div>
        @empty
            <div class="text-gray-600">No open sessions yet.</div>
        @endforelse
    </div>

    <script id="partner-requests-data" type="application/json">@json($openRequests)</script>
    <script id="sessions-data" type="application/json">@json($openSessions)</script>
    <script>
        let pMap, pRequestMarkers=[], pSessionMarkers=[];

        function getJson(elId, fallback = []) {
            try { return JSON.parse(document.getElementById(elId)?.textContent || '[]'); } catch (e) { return fallback; }
        }

        function initPartnersMap() {
            const reqs = getJson('partner-requests-data');
            const sess = getJson('sessions-data');
            const defaultCenter = {
                lat: reqs[0]?.latitude ? parseFloat(reqs[0].latitude) : (sess[0]?.court?.latitude ? parseFloat(sess[0].court.latitude) : -6.200000),
                lng: reqs[0]?.longitude ? parseFloat(reqs[0].longitude) : (sess[0]?.court?.longitude ? parseFloat(sess[0].court.longitude) : 106.816666)
            };
            pMap = new google.maps.Map(document.getElementById('partners-map'), { center: defaultCenter, zoom: 12 });

            initPartnerAutocomplete();
            renderPartnerRequestMarkers();
            renderSessionMarkers();

            ['partner-requests-data','sessions-data'].forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;
                const obs = new MutationObserver(() => { renderPartnerRequestMarkers(); renderSessionMarkers(); });
                obs.observe(el, { childList: true, characterData: true, subtree: true });
            });
        }

        function renderPartnerRequestMarkers() {
            pRequestMarkers.forEach(m => m.setMap(null));
            pRequestMarkers = [];
            const data = getJson('partner-requests-data');
            data.forEach(r => {
                if (!r.latitude || !r.longitude) return;
                const m = new google.maps.Marker({ position: {lat: parseFloat(r.latitude), lng: parseFloat(r.longitude)}, map: pMap, title: r.requester?.name || 'Request', label: 'R' });
                const info = new google.maps.InfoWindow({ content: `<div><strong>${r.requester?.name ?? 'Request'}</strong><br>${r.message ?? ''}</div>` });
                m.addListener('click', () => info.open({anchor: m, map: pMap}));
                pRequestMarkers.push(m);
            });
        }

        function renderSessionMarkers() {
            pSessionMarkers.forEach(m => m.setMap(null));
            pSessionMarkers = [];
            const sess = getJson('sessions-data');
            sess.forEach(s => {
                const c = s.court || {};
                if (!c.latitude || !c.longitude) return;
                const m = new google.maps.Marker({ position: {lat: parseFloat(c.latitude), lng: parseFloat(c.longitude)}, map: pMap, title: c.name || 'Session', label: 'S' });
                const infoHtml = `
                    <div>
                        <strong>${c.name ?? 'Court'}</strong><br>
                        Host: ${s.host ?? '-'}<br>
                        ${s.start_time ?? ''} - ${s.end_time ?? ''}<br>
                        Remaining: ${s.remaining} / ${s.desired_size}
                    </div>`;
                const info = new google.maps.InfoWindow({ content: infoHtml });
                m.addListener('click', () => info.open({anchor: m, map: pMap}));
                pSessionMarkers.push(m);
            });
        }

        function initPartnerAutocomplete() {
            const input = document.getElementById('partnerPlaceSearch');
            if (!input || !google?.maps?.places) return;
            const autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.addListener('place_changed', () => {
                const place = autocomplete.getPlace();
                if (!place?.geometry) return;
                const loc = place.geometry.location;
                pMap.setCenter(loc);
                pMap.setZoom(14);
                if (window.Livewire) {
                    const c = { lat: loc.lat(), lng: loc.lng() };
                    @this.set('centerLat', c.lat);
                    @this.set('centerLng', c.lng);
                    @this.set('latitude', c.lat);
                    @this.set('longitude', c.lng);
                }
            });
        }

        function useMyPartnerLocation() {
            if (!navigator.geolocation) return;
            navigator.geolocation.getCurrentPosition(pos => {
                const { latitude, longitude } = pos.coords;
                pMap.setCenter({ lat: latitude, lng: longitude });
                pMap.setZoom(14);
                if (window.Livewire) {
                    @this.set('centerLat', latitude);
                    @this.set('centerLng', longitude);
                    @this.set('latitude', latitude);
                    @this.set('longitude', longitude);
                }
            });
        }
        function resetPartnerCenter() {
            if (window.Livewire) {
                @this.set('centerLat', null);
                @this.set('centerLng', null);
                @this.set('radiusKm', null);
            }
        }

        window.initPartnersMap = initPartnersMap;
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initPartnersMap" async defer></script>
</div>
