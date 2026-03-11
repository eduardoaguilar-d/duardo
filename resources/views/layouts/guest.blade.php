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

    {{-- Anti-FOUC: aplica dark class antes de que el navegador pinte --}}
    <script>
        (function() {
            const saved = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (saved === 'dark' || (!saved && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>
<body class="antialiased transition-colors duration-300" style="font-family: 'Inter', sans-serif;">

    {{-- Fondo adaptado a dark/light mode --}}
    <div class="relative min-h-screen flex items-center justify-center overflow-hidden
                dark:bg-[#1e1b4b]"
         style="background: linear-gradient(135deg, #6366f1 0%, #7c3aed 40%, #4f46e5 100%);"
         x-data x-bind:style="$store.theme.dark
            ? 'background: linear-gradient(135deg, #1e1b4b 0%, #312e81 25%, #4c1d95 50%, #3730a3 75%, #1e1b4b 100%);'
            : 'background: linear-gradient(135deg, #6366f1 0%, #7c3aed 40%, #4f46e5 100%);'">

        {{-- Orbes decorativos --}}
        <div class="absolute top-[-10%] left-[-5%] w-96 h-96 rounded-full opacity-20 blur-3xl" style="background: radial-gradient(circle, #a78bfa, transparent);"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-96 h-96 rounded-full opacity-20 blur-3xl" style="background: radial-gradient(circle, #818cf8, transparent);"></div>

        {{-- Toggle dark mode (top right) --}}
        <div class="absolute top-4 right-4" x-data>
            <button
                @click="$store.theme.toggle()"
                class="w-10 h-10 flex items-center justify-center rounded-xl transition-all duration-200 text-white/70 hover:text-white hover:bg-white/10"
                title="Cambiar tema"
            >
                <svg x-show="$store.theme.dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <svg x-show="!$store.theme.dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </button>
        </div>

        {{-- Card glassmorphism --}}
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

    {{-- Alpine store --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                dark: document.documentElement.classList.contains('dark'),
                toggle() {
                    this.dark = !this.dark;
                    document.documentElement.classList.toggle('dark', this.dark);
                    localStorage.setItem('theme', this.dark ? 'dark' : 'light');
                }
            });
        });
    </script>
</body>
</html>
