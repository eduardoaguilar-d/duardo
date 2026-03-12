<x-app-shell>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
    </x-slot>
    <div class="py-4 max-w-7xl mx-auto" x-data="{ openWhatsAppModal: false }" @close-whatsapp-modal.window="openWhatsAppModal = false">

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

        @if (session('status'))
            <div class="mb-6 rounded-xl bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200 px-4 py-3 text-sm">
                {{ session('status') }}
            </div>
        @endif

        {{-- Paso 1: WhatsApp --}}
        @php
            $whatsappConnected = Auth::user()->clients()->whereHas('whatsappConnection')->exists();
        @endphp
        <div class="mb-8 p-6 rounded-2xl border-2 {{ $whatsappConnected ? 'bg-green-50 dark:bg-green-950/30 border-green-200 dark:border-green-800' : 'bg-amber-50 dark:bg-amber-950/30 border-amber-200 dark:border-amber-800' }}">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <span class="text-3xl">{{ $whatsappConnected ? '✅' : '1️⃣' }}</span>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Conectar WhatsApp (Meta Cloud API)</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">
                            {{ $whatsappConnected ? 'Cuenta conectada. Puedes gestionar la conexión o añadir otra.' : 'Primer paso: vincula tu número de WhatsApp Business con las credenciales de Meta.' }}
                        </p>
                    </div>
                </div>
                <button type="button"
                        @click="openWhatsAppModal = true"
                        class="inline-flex items-center px-4 py-2.5 rounded-xl font-medium text-sm {{ $whatsappConnected ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-amber-500 hover:bg-amber-600 text-white' }}">
                    {{ $whatsappConnected ? 'Gestionar conexión' : 'Conectar cuenta' }}
                </button>
            </div>
        </div>

        {{-- Modal: Conectar WhatsApp (Facebook / Meta) --}}
        <div x-show="openWhatsAppModal"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             style="display: none;">
            <div class="absolute inset-0 bg-gray-900/60 dark:bg-gray-950/70" @click="openWhatsAppModal = false" aria-hidden="true"></div>
            <div class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-gray-900 shadow-xl border border-gray-200 dark:border-gray-700 p-6"
                 @click.stop>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Conectar WhatsApp (Meta)</h3>
                    <button type="button"
                            @click="openWhatsAppModal = false"
                            class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800"
                            aria-label="Cerrar">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                </div>
                <livewire:connect-whatsapp-form />
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
</x-app-shell>
