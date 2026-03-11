<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();
        Session::regenerate();
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    {{-- Status de sesión --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-5">

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-purple-200 mb-1">
                Correo electrónico
            </label>
            <input
                wire:model="form.email"
                id="email"
                type="email"
                name="email"
                required
                autofocus
                autocomplete="username"
                placeholder="tu@email.com"
                class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-purple-400 outline-none transition"
                style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); focus:border-purple-400;"
            >
            <x-input-error :messages="$errors->get('form.email')" class="mt-1 text-red-300 text-xs" />
        </div>

        {{-- Password --}}
        <div>
            <div class="flex items-center justify-between mb-1">
                <label for="password" class="block text-sm font-medium text-purple-200">
                    Contraseña
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" wire:navigate
                       class="text-xs text-purple-400 hover:text-purple-200 transition">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>
            <input
                wire:model="form.password"
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
                class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-purple-400 outline-none transition"
                style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);"
            >
            <x-input-error :messages="$errors->get('form.password')" class="mt-1 text-red-300 text-xs" />
        </div>

        {{-- Recuérdame --}}
        <label for="remember" class="flex items-center gap-2 cursor-pointer">
            <input wire:model="form.remember" id="remember" type="checkbox"
                   class="w-4 h-4 rounded accent-purple-500">
            <span class="text-sm text-purple-300">Recordarme</span>
        </label>

        {{-- Botón login --}}
        <button
            type="submit"
            class="w-full py-3 px-4 rounded-xl font-semibold text-white text-sm transition-all duration-200 hover:scale-[1.02] active:scale-[0.98]"
            style="background: linear-gradient(135deg, #7c3aed, #4f46e5); box-shadow: 0 4px 15px rgba(124,58,237,0.4);"
        >
            <span wire:loading.remove>Iniciar sesión</span>
            <span wire:loading>Verificando...</span>
        </button>

        {{-- Link a registro --}}
        <p class="text-center text-sm text-purple-400">
            ¿No tienes cuenta?
            <a href="{{ route('register') }}" wire:navigate
               class="text-purple-200 font-medium hover:text-white transition">
                Regístrate gratis
            </a>
        </p>
    </form>
</div>
