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
        (function() {
            const saved = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (saved === 'dark' || (!saved && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>
<body class="antialiased" style="font-family: 'Inter', sans-serif;">

<div class="relative min-h-screen flex flex-col items-center justify-center p-6 overflow-hidden"
     x-data
     x-bind:style="$store.theme.dark
        ? 'background: linear-gradient(135deg, #1e1b4b 0%, #312e81 35%, #4c1d95 65%, #1e1b4b 100%);'
        : 'background: linear-gradient(135deg, #6366f1 0%, #7c3aed 40%, #4f46e5 100%);'">

    {{-- Orbes de fondo --}}
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none overflow-hidden">
        <div class="absolute top-[-5%] left-[-5%] w-96 h-96 rounded-full opacity-20 blur-3xl" style="background: radial-gradient(circle, #a78bfa, transparent);"></div>
        <div class="absolute bottom-[-5%] right-[-5%] w-96 h-96 rounded-full opacity-20 blur-3xl" style="background: radial-gradient(circle, #818cf8, transparent);"></div>
    </div>

    {{-- Toggle dark mode --}}
    <div class="absolute top-4 right-4 z-20">
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

    <div class="relative z-10 text-center max-w-2xl w-full">
        <h1 class="text-7xl font-black text-white mb-3 tracking-tight">Duardo</h1>
        <p class="text-purple-200 text-xl mb-10">Laravel · Livewire · Alpine.js · Tailwind v4</p>

        @auth
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('dashboard') }}" wire:navigate
                   class="px-8 py-4 rounded-xl font-bold text-white text-base transition-all hover:scale-105"
                   style="background: linear-gradient(135deg, #7c3aed, #4f46e5); box-shadow: 0 8px 25px rgba(124,58,237,0.4);">
                    Ir al Dashboard →
                </a>
            </div>
            <p class="text-purple-300 text-sm mt-4">Sesión iniciada como <span class="text-purple-100 font-medium">{{ Auth::user()->name }}</span></p>
        @else
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" wire:navigate
                   class="px-8 py-4 rounded-xl font-bold text-white text-base transition-all hover:scale-105"
                   style="background: linear-gradient(135deg, #7c3aed, #4f46e5); box-shadow: 0 8px 25px rgba(124,58,237,0.4);">
                    Crear cuenta gratis
                </a>
                <a href="{{ route('login') }}" wire:navigate
                   class="px-8 py-4 rounded-xl font-bold text-white text-base transition-all hover:scale-105"
                   style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(10px);">
                    Iniciar sesión
                </a>
            </div>
        @endauth

        <div class="flex flex-wrap gap-3 justify-center mt-14">
            @foreach(['✅ Laravel '.app()->version(), '✅ Livewire 3', '✅ Alpine.js', '✅ Tailwind v4'] as $tech)
                <span class="px-4 py-1.5 rounded-full text-sm font-medium"
                      style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12); color: #c4b5fd;">
                    {{ $tech }}
                </span>
            @endforeach
        </div>
    </div>
</div>

@livewireScripts

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
