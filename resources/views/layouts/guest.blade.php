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
</head>
<body class="antialiased" style="font-family: 'Inter', sans-serif;">

    {{-- Fondo con gradiente animado --}}
    <div class="relative min-h-screen flex items-center justify-center overflow-hidden" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 25%, #4c1d95 50%, #3730a3 75%, #1e1b4b 100%);">

        {{-- Orbes decorativos --}}
        <div class="absolute top-[-10%] left-[-5%] w-96 h-96 rounded-full opacity-20 blur-3xl" style="background: radial-gradient(circle, #a78bfa, transparent);"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-96 h-96 rounded-full opacity-20 blur-3xl" style="background: radial-gradient(circle, #818cf8, transparent);"></div>
        <div class="absolute top-[40%] right-[20%] w-64 h-64 rounded-full opacity-10 blur-3xl" style="background: radial-gradient(circle, #c4b5fd, transparent);"></div>

        {{-- Card glassmorphism --}}
        <div class="relative z-10 w-full px-4" style="max-width: 440px;">

            {{-- Logo / título --}}
            <div class="text-center mb-8">
                <a href="/" wire:navigate>
                    <span class="text-4xl font-black text-white tracking-tight">Duardo</span>
                </a>
                <p class="text-purple-300 text-sm mt-1">{{ config('app.name', 'Duardo') }}</p>
            </div>

            {{-- Card --}}
            <div class="rounded-2xl p-8" style="background: rgba(255,255,255,0.08); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.12); box-shadow: 0 25px 50px rgba(0,0,0,0.4);">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            <p class="text-center text-purple-400 text-xs mt-6">
                © {{ date('Y') }} Duardo. Todos los derechos reservados.
            </p>
        </div>
    </div>

    @livewireScripts
</body>
</html>
