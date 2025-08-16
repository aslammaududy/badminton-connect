<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Auth • Badminton Connect</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="antialiased bg-gray-50 text-gray-900 min-h-dvh flex flex-col">
    <header class="border-b bg-white/80 backdrop-blur">
        <div class="mx-auto max-w-md px-4 py-3 flex items-center justify-between">
            <a href="/" class="font-semibold tracking-tight">Badminton Connect</a>
            <nav class="text-sm">
                <a href="/login" class="px-2 py-1 rounded {{ request()->is('login') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">Login</a>
                <a href="/register" class="px-2 py-1 rounded {{ request()->is('register') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">Register</a>
            </nav>
        </div>
    </header>

    <main class="flex-1 flex items-center justify-center px-4">
        <div class="w-full max-w-md bg-white border rounded-xl shadow-sm p-6">
            {{ $slot }}
        </div>
    </main>

    <footer class="border-t bg-white/60">
        <div class="mx-auto max-w-md px-4 py-4 text-xs text-gray-600 text-center">
            &copy; {{ date('Y') }} Badminton Connect
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
</body>
</html>
