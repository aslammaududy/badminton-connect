<x-layouts.auth>
    <h1 class="text-xl font-semibold mb-4">Sign in</h1>
    @if ($errors->any())
        <div class="mb-3 rounded border border-red-300 bg-red-50 text-red-700 px-3 py-2 text-sm">
            {{ $errors->first() }}
        </div>
    @endif
    <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1" for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" class="w-full rounded border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200" placeholder="you@example.com" required />
        </div>
        <div>
            <label class="block text-sm font-medium mb-1" for="password">Password</label>
            <input id="password" name="password" type="password" class="w-full rounded border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200" required />
        </div>
        <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" name="remember" class="rounded border-gray-300"> Remember me
        </label>
        <button type="submit" class="w-full bg-blue-600 text-white rounded px-4 py-2">Sign in</button>
    </form>
    <p class="mt-4 text-sm text-gray-600">No account? <a class="text-blue-600 hover:underline" href="/register">Register</a></p>
</x-layouts.auth>
