<x-layouts.auth>
    <h1 class="text-xl font-semibold mb-4">Create account</h1>
    @if ($errors->any())
        <div class="mb-3 rounded border border-red-300 bg-red-50 text-red-700 px-3 py-2 text-sm">
            {{ $errors->first() }}
        </div>
    @endif
    <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1" for="name">Name</label>
            <input id="name" name="name" type="text" class="w-full rounded border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200" placeholder="Your name" required />
        </div>
        <div>
            <label class="block text-sm font-medium mb-1" for="email">Email</label>
            <input id="email" name="email" type="email" class="w-full rounded border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200" placeholder="you@example.com" required />
        </div>
        <div>
            <label class="block text-sm font-medium mb-1" for="password">Password</label>
            <input id="password" name="password" type="password" class="w-full rounded border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200" required />
        </div>
        <div>
            <label class="block text-sm font-medium mb-1" for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="w-full rounded border px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200" required />
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white rounded px-4 py-2">Create account</button>
    </form>
    <p class="mt-4 text-sm text-gray-600">Already have an account? <a class="text-blue-600 hover:underline" href="/login">Sign in</a></p>
</x-layouts.auth>
