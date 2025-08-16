<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Badminton Connect</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="antialiased bg-gray-50 text-gray-900">
    <header class="border-b bg-white/80 backdrop-blur">
        <div class="mx-auto max-w-6xl px-4 py-3 flex items-center justify-between">
            <a href="/" class="font-semibold tracking-tight text-lg">Badminton Connect</a>
            <nav class="flex items-center gap-1.5 text-sm">
                <a href="/courts" class="px-3 py-1.5 rounded transition-colors {{ request()->is('courts*') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">Courts</a>
                <a href="/bookings/create" class="px-3 py-1.5 rounded transition-colors {{ request()->is('bookings*') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">Bookings</a>
                <a href="/tournaments" class="px-3 py-1.5 rounded transition-colors {{ request()->is('tournaments*') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">Tournaments</a>
                <a href="/partners/find" class="px-3 py-1.5 rounded transition-colors {{ request()->is('partners*') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">Find Partners</a>
                <span class="mx-1 text-gray-300">|</span>
                @auth
                    <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">Dashboard</a>
                    <a href="{{ route('profile') }}" class="px-3 py-1.5 rounded transition-colors {{ request()->routeIs('profile') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">Profile</a>
                    <span class="text-sm text-gray-700 hidden sm:inline">Hi, {{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded transition-colors text-gray-700 hover:bg-gray-100">Logout</button>
                    </form>
                @else
                    <a href="/login" class="px-3 py-1.5 rounded transition-colors {{ request()->is('login') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">Login</a>
                    <a href="/register" class="px-3 py-1.5 rounded transition-colors {{ request()->is('register') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">Register</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-6">
        {{ $slot }}
    </main>

    <footer class="mt-8 border-t bg-white/60">
        <div class="mx-auto max-w-6xl px-4 py-6 text-sm text-gray-600 flex items-center justify-between">
            <span>&copy; {{ date('Y') }} Badminton Connect</span>
            <span class="hidden sm:inline">Built with Laravel, Livewire, and Tailwind CSS</span>
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
</body>
<!-- Simple base layout for Livewire page components -->
</html>
