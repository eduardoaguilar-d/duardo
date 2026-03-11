<x-app-layout>
    <div class="py-10 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">

        {{-- Bienvenida --}}
        <div class="mb-8 rounded-2xl p-8 text-white"
             style="background: linear-gradient(135deg, #3730a3 0%, #6d28d9 50%, #4c1d95 100%); box-shadow: 0 10px 40px rgba(109,40,217,0.25);">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <p class="text-purple-300 text-sm font-medium mb-1">Bienvenido de vuelta 👋</p>
                    <h1 class="text-3xl font-black">{{ Auth::user()->name }}</h1>
                    <p class="text-purple-300 mt-1 text-sm">{{ Auth::user()->email }}</p>
                </div>
                <div class="text-right">
                    <p class="text-purple-300 text-xs">Miembro desde</p>
                    <p class="text-white font-semibold">{{ Auth::user()->created_at->format('d M Y') }}</p>
                </div>
            </div>
        </div>

        {{-- Cards de sección --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 border border-gray-100 dark:border-gray-800 shadow-sm transition-colors duration-300">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-4"
                     style="background: linear-gradient(135deg, #ede9fe, #ddd6fe);">
                    <span class="text-xl">📊</span>
                </div>
                <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-1">Mi cuenta</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Gestiona tu perfil y configuración.</p>
                <a href="/profile" wire:navigate
                   class="inline-flex items-center mt-4 text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition">
                    Ver perfil →
                </a>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 border border-gray-100 dark:border-gray-800 shadow-sm transition-colors duration-300">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-4"
                     style="background: linear-gradient(135deg, #fef9c3, #fef08a);">
                    <span class="text-xl">💳</span>
                </div>
                <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-1">Pagos</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Próximamente: gestión de suscripción y pagos.</p>
                <span class="inline-flex items-center mt-4 text-xs font-semibold px-3 py-1 rounded-full"
                      style="background: #fef9c3; color: #854d0e;">
                    Próximamente
                </span>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 border border-gray-100 dark:border-gray-800 shadow-sm transition-colors duration-300">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-4"
                     style="background: linear-gradient(135deg, #dcfce7, #bbf7d0);">
                    <span class="text-xl">🔒</span>
                </div>
                <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-1">Seguridad</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Actualiza contraseña y preferencias de seguridad.</p>
                <a href="/profile" wire:navigate
                   class="inline-flex items-center mt-4 text-sm font-semibold text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 transition">
                    Configurar →
                </a>
            </div>
        </div>

    </div>
</x-app-layout>
