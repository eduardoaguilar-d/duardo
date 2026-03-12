<div>
    @if(config('services.meta.embedded_signup_configuration_id') && config('services.meta.app_id') && config('services.meta.oauth_redirect_uri'))
        {{-- Conectar con Meta por redirección (config_id va en la URL; evita error "config_id es obligatorio" del SDK) --}}
        <div class="mb-4">
            <a href="{{ route('whatsapp.oauth.redirect') }}"
               class="inline-flex items-center justify-center gap-2 w-full px-4 py-3 rounded-xl font-medium text-white bg-[#1877F2] hover:bg-[#166fe5] focus:ring-2 focus:ring-offset-2 focus:ring-[#1877F2] transition">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                Conectar con Meta (Registro insertado WhatsApp)
            </a>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Te llevará a Meta para autorizar con tu configuración de WhatsApp (config_id). Tras aceptar, volverás aquí.</p>
            <details class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                <summary class="cursor-pointer font-medium text-gray-700 dark:text-gray-300">Si aparece "El dominio no está incluido"</summary>
                <p class="mt-2 pl-2 border-l-2 border-gray-200 dark:border-gray-600">En <strong>Meta for Developers</strong> → tu app → <strong>Configuración</strong> → <strong>Básica</strong>: en <strong>Dominios de la aplicación</strong> añade <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">localhost</code>. En <strong>URI de redireccionamiento de OAuth válidos</strong> añade <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">{{ config('services.meta.oauth_redirect_uri') }}</code>. Guarda los cambios.</p>
            </details>
        </div>
        <div class="relative my-4">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200 dark:border-gray-600"></div></div>
            <div class="relative flex justify-center text-sm"><span class="px-2 bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400">O bien, credenciales manuales</span></div>
        </div>
    @elseif(config('services.meta.oauth_redirect_uri'))
        <div class="mb-4">
            <a href="{{ route('whatsapp.oauth.redirect') }}"
               target="_blank"
               rel="noopener noreferrer"
               class="inline-flex items-center justify-center gap-2 w-full px-4 py-3 rounded-xl font-medium text-white bg-[#1877F2] hover:bg-[#166fe5] focus:ring-2 focus:ring-offset-2 focus:ring-[#1877F2] transition">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                Conectar con Meta (redirección)
            </a>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Para usar el SDK (recomendado), configura <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">META_EMBEDDED_SIGNUP_CONFIGURATION_ID</code> en .env.</p>
        </div>
        <div class="relative my-4">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200 dark:border-gray-600"></div></div>
            <div class="relative flex justify-center text-sm"><span class="px-2 bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400">O bien, credenciales manuales</span></div>
        </div>
    @else
        <p class="text-amber-700 dark:text-amber-300 text-sm mb-4">Configura <code class="bg-amber-100 dark:bg-amber-900/50 px-1 rounded">META_OAUTH_REDIRECT_URI</code> o <code class="bg-amber-100 dark:bg-amber-900/50 px-1 rounded">META_EMBEDDED_SIGNUP_CONFIGURATION_ID</code> en .env.</p>
    @endif

    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
        Obtén las credenciales en
        <a href="https://developers.facebook.com" target="_blank" rel="noopener" class="text-indigo-600 dark:text-indigo-400 hover:underline">Meta for Developers</a>
        (WhatsApp → Configuración). Necesitas <strong>Phone Number ID</strong>, <strong>WABA ID</strong> y <strong>Access Token</strong>.
    </p>

    @if($verifyStatus)
        <div class="mb-4 p-4 rounded-xl text-sm {{ $verifyStatus === 'success' ? 'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-200' }}">
            @if($verifyStatus === 'success')
                <span class="font-medium">✓ Verificación correcta</span><br>
            @else
                <span class="font-medium">✗ Error al verificar</span><br>
            @endif
            {{ $verifyMessage }}
        </div>
    @endif

    <form wire:submit.prevent="verify" class="space-y-4">
        <div>
            <label for="modal_connection_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre de la conexión</label>
            <input type="text" wire:model="connection_name" id="modal_connection_name"
                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2.5"
                   placeholder="Ej. Mi negocio">
            <x-input-error :messages="$errors->get('connection_name')" class="mt-1" />
        </div>

        <div>
            <label for="modal_phone_number_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone Number ID</label>
            <input type="text" wire:model="phone_number_id" id="modal_phone_number_id"
                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2.5 font-mono text-sm"
                   placeholder="Ej. 123456789012345">
            <x-input-error :messages="$errors->get('phone_number_id')" class="mt-1" />
        </div>

        <div>
            <label for="modal_waba_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">WhatsApp Business Account ID (WABA)</label>
            <input type="text" wire:model="waba_id" id="modal_waba_id"
                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2.5 font-mono text-sm"
                   placeholder="Ej. 987654321098765">
            <x-input-error :messages="$errors->get('waba_id')" class="mt-1" />
        </div>

        <div>
            <label for="modal_access_token" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Access Token</label>
            <input type="password" wire:model="access_token" id="modal_access_token"
                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2.5 font-mono text-sm"
                   placeholder="{{ $client_id ? 'Dejar en blanco para no cambiar' : 'Token de la app Meta' }}" autocomplete="off">
            <x-input-error :messages="$errors->get('access_token')" class="mt-1" />
            @if($client_id)
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Solo rellena si quieres actualizar el token.</p>
            @endif
        </div>

        <div class="flex flex-wrap gap-3 pt-2">
            <button type="button"
                    wire:click="verify"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                <span wire:loading.remove wire:target="verify">Verificar y guardar</span>
                <span wire:loading wire:target="verify">Verificando…</span>
            </button>
            <button type="button"
                    @click="$dispatch('close-whatsapp-modal')"
                    class="inline-flex items-center px-4 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium rounded-lg">
                Cancelar
            </button>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 pt-1">Se comprueban Phone Number ID, WABA ID y token con Meta. Solo se guarda si la verificación es correcta.</p>
    </form>
</div>
