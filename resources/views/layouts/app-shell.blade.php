<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Duardo') }}@isset($title) – {{ $title }}@endisset</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script>
        (function () {
            var saved = localStorage.getItem('theme');
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (saved === 'dark' || (!saved && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
</head>
<body class="antialiased bg-gray-50 dark:bg-gray-900 transition-colors duration-300" style="font-family: 'Inter', sans-serif;">
    @php $drawerId = $drawerId ?? 'drawer-navigation'; @endphp
    <div class="min-h-screen" x-data="{ sidebarOpen: false }">
        <x-app.navbar :drawer-id="$drawerId">
            @isset($navbarActions)
                {{ $navbarActions }}
            @else
                @auth
                    <x-app.dropdown-menu title="{{ __('Profile') }}" align="right">
                        <x-slot:trigger>
                            <button type="button" class="flex mx-3 text-sm bg-gray-800 rounded-full md:mr-0 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" aria-expanded="false">
                                <span class="sr-only">Open user menu</span>
                                <img class="w-8 h-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=6366f1&color=fff" alt="">
                            </button>
                        </x-slot:trigger>
                        <div class="py-3 px-4">
                            <span class="block text-sm font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</span>
                            <span class="block text-sm text-gray-500 truncate dark:text-gray-400">{{ auth()->user()->email }}</span>
                        </div>
                        <ul class="py-1 text-gray-700 dark:text-gray-300">
                            <li>
                                <a href="{{ route('profile') }}" wire:navigate class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Mi perfil</a>
                            </li>
                        </ul>
                        <ul class="py-1 text-gray-700 dark:text-gray-300">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Cerrar sesión</button>
                                </form>
                            </li>
                        </ul>
                    </x-app.dropdown-menu>
                @else
                    <a href="{{ route('login') }}" wire:navigate class="text-sm text-gray-700 dark:text-gray-300 hover:underline">Iniciar sesión</a>
                    <a href="{{ route('register') }}" wire:navigate class="ml-2 text-sm text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg px-3 py-1.5">Registrarse</a>
                @endauth
            @endisset
        </x-app.navbar>

        {{-- Backdrop móvil --}}
        <div x-show="sidebarOpen"
             x-transition:opacity.duration.200
             @click="sidebarOpen = false"
             class="fixed inset-0 z-30 bg-gray-900/50 md:hidden"
             style="display: none;"
             aria-hidden="true"></div>

        <x-app.sidebar :drawer-id="$drawerId">
            @isset($sidebar)
                {{ $sidebar }}
            @else
                <li>
                    <x-app.sidebar-item :href="route('dashboard')" icon='<svg fill="currentColor" viewBox="0 0 20 20"><path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path><path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path></svg>'>
                        Dashboard
                    </x-app.sidebar-item>
                </li>
                @auth
                <li>
                    <x-app.sidebar-item :href="route('profile')" icon='<svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>'>
                        Perfil
                    </x-app.sidebar-item>
                </li>
                @endauth
            @endisset
        </x-app.sidebar>

        <main class="p-4 md:ml-64 pt-20 min-h-screen">
            @isset($header)
                <header class="mb-6">
                    {{ $header }}
                </header>
            @endisset
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
    <script>
        function toggleDarkMode() {
            var isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            var sunIcon = document.getElementById('icon-sun');
            var moonIcon = document.getElementById('icon-moon');
            if (sunIcon) sunIcon.style.display = isDark ? 'block' : 'none';
            if (moonIcon) moonIcon.style.display = isDark ? 'none' : 'block';
        }
        function applyThemeFromStorage() {
            var saved = localStorage.getItem('theme');
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (saved === 'dark' || (!saved && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            var isDark = document.documentElement.classList.contains('dark');
            var sunIcon = document.getElementById('icon-sun');
            var moonIcon = document.getElementById('icon-moon');
            if (sunIcon) sunIcon.style.display = isDark ? 'block' : 'none';
            if (moonIcon) moonIcon.style.display = isDark ? 'none' : 'block';
        }
        document.addEventListener('DOMContentLoaded', applyThemeFromStorage);
        document.addEventListener('livewire:navigated', applyThemeFromStorage);
    </script>
</body>
</html>
