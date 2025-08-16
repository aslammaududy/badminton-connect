<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Courts</h1>
    <div class="space-y-2">
        @foreach ($courts as $court)
            <div class="border p-3 rounded">
                <div class="font-semibold">{{ $court->name }}</div>
                <div class="text-sm text-gray-600">{{ $court->location }}</div>
                <div class="text-sm">{{ $court->description }}</div>
                @if($court->hourly_rate)
                    <div class="text-sm">Rate: {{ number_format($court->hourly_rate, 2) }}</div>
                @endif
            </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $courts->links() }}</div>
</div>
