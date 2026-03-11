<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Duardo') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script>
        (function () {
            const saved = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (saved === 'dark' || (!saved && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        }
    </script>
</head>
<body class="antialiased dark:bg-gray-950" style="font-family: 'Inter', sans-serif;">

    <div class="relative min-h-screen flex items-center justify-center overflow-hidden"
         style="background: linear-gradient(135deg, #6366f1 0%, #7c3aed 40%, #4f46e5 100%);"
         id="guest-bg">

        {{-- Orbes decorativos --}}
        <div class="absolute top-[-10%] left-[-5%] w-96 h-96 rounded-full opacity-20 blur-3xl" style="background: radial-gradient(circle, #a78bfa, transparent);"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-96 h-96 rounded-full opacity-20 blur-3xl" style="background: radial-gradient(circle, #818cf8, transparent);"></div>

        {{-- Toggle dark mode --}}
        <div class="absolute top-4 right-4 z-20">
            <button onclick="toggleTheme(); updateGuestBg();"
                    class="w-10 h-10 flex items-center justify-center rounded-xl text-white/70 hover:text-white hover:bg-white/10 transition"
                    title="Cambiar tema">
                {{-- Luna en light --}}
                <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
                {{-- Sol en dark --}}
                <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </button>
        </div>

        {{-- Card --}}
        <div class="relative z-10 w-full px-4" style="max-width: 440px;">
            <div class="text-center mb-8">
                <a href="/" wire:navigate>
                    <span class="text-4xl font-black text-white tracking-tight">Duardo</span>
                </a>
                <p class="text-purple-200 text-sm mt-1">{{ config('app.name', 'Duardo') }}</p>
            </div>

            <div class="rounded-2xl p-8" style="background: rgba(255,255,255,0.08); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.12); box-shadow: 0 25px 50px rgba(0,0,0,0.4);">
                {{ $slot }}
            </div>

            <p class="text-center text-purple-300 text-xs mt-6">
                © {{ date('Y') }} Duardo. Todos los derechos reservados.
            </p>
        </div>
    </div>

    @livewireScripts
    <script>
        function updateGuestBg() {
            const bg = document.getElementById('guest-bg');
            if (!bg) return;
            bg.style.background = document.documentElement.classList.contains('dark')
                ? 'linear-gradient(135deg, #1e1b4b 0%, #312e81 25%, #4c1d95 50%, #3730a3 75%, #1e1b4b 100%)'
                : 'linear-gradient(135deg, #6366f1 0%, #7c3aed 40%, #4f46e5 100%)';
        }
        updateGuestBg();
    </script>
</body>
</html>
