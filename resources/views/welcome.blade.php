<x-layouts.app>
    <div class="min-h-screen flex flex-col items-center justify-center p-6"
         style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 35%, #4c1d95 65%, #1e1b4b 100%);">

        {{-- Orbes --}}
        <div class="fixed top-0 left-0 w-full h-full pointer-events-none overflow-hidden">
            <div class="absolute top-[-5%] left-[-5%] w-96 h-96 rounded-full opacity-20 blur-3xl" style="background: radial-gradient(circle, #a78bfa, transparent);"></div>
            <div class="absolute bottom-[-5%] right-[-5%] w-96 h-96 rounded-full opacity-20 blur-3xl" style="background: radial-gradient(circle, #818cf8, transparent);"></div>
        </div>

        <div class="relative z-10 text-center max-w-2xl w-full">

            {{-- Logo --}}
            <h1 class="text-7xl font-black text-white mb-3 tracking-tight">Duardo</h1>
            <p class="text-purple-300 text-xl mb-10">Laravel · Livewire · Alpine.js · Tailwind v4</p>

            {{-- CTA según estado de auth --}}
            @auth
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('dashboard') }}" wire:navigate
                       class="px-8 py-4 rounded-xl font-bold text-white text-base transition-all hover:scale-105"
                       style="background: linear-gradient(135deg, #7c3aed, #4f46e5); box-shadow: 0 8px 25px rgba(124,58,237,0.4);">
                        Ir al Dashboard →
                    </a>
                </div>
                <p class="text-purple-400 text-sm mt-4">Sesión iniciada como <span class="text-purple-200 font-medium">{{ Auth::user()->name }}</span></p>
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

            {{-- Stack badges --}}
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
</x-layouts.app>
