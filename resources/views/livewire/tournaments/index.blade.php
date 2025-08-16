<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Tournaments</h1>
    <div class="space-y-2">
        @foreach ($tournaments as $t)
            <div class="border p-3 rounded">
                <div class="font-semibold">{{ $t->name }}</div>
                <div class="text-sm text-gray-600">{{ $t->location }}</div>
                <div class="text-sm">{{ $t->start_date }} to {{ $t->end_date }}</div>
                <div class="text-sm">Status: {{ $t->status }}</div>
            </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $tournaments->links() }}</div>
</div>
