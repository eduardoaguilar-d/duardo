<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        event(new Registered($user = User::create($validated)));
        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <h2 class="text-2xl font-bold text-white mb-1">Crear cuenta</h2>
    <p class="text-purple-400 text-sm mb-6">Únete a Duardo hoy. Es gratis.</p>

    <form wire:submit="register" class="space-y-4">

        {{-- Nombre --}}
        <div>
            <label for="name" class="block text-sm font-medium text-purple-200 mb-1">Nombre completo</label>
            <input
                wire:model="name"
                id="name"
                type="text"
                name="name"
                required
                autofocus
                autocomplete="name"
                placeholder="Eduardo García"
                class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-purple-400 outline-none transition"
                style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);"
            >
            <x-input-error :messages="$errors->get('name')" class="mt-1 text-red-300 text-xs" />
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-purple-200 mb-1">Correo electrónico</label>
            <input
                wire:model="email"
                id="email"
                type="email"
                name="email"
                required
                autocomplete="username"
                placeholder="tu@email.com"
                class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-purple-400 outline-none transition"
                style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);"
            >
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-red-300 text-xs" />
        </div>

        {{-- Contraseña --}}
        <div>
            <label for="password" class="block text-sm font-medium text-purple-200 mb-1">Contraseña</label>
            <input
                wire:model="password"
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="Mínimo 8 caracteres"
                class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-purple-400 outline-none transition"
                style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);"
            >
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-red-300 text-xs" />
        </div>

        {{-- Confirmar contraseña --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-purple-200 mb-1">Confirmar contraseña</label>
            <input
                wire:model="password_confirmation"
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Repite tu contraseña"
                class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-purple-400 outline-none transition"
                style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);"
            >
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-red-300 text-xs" />
        </div>

        {{-- Botón registrar --}}
        <button
            type="submit"
            class="w-full py-3 px-4 rounded-xl font-semibold text-white text-sm transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] mt-2"
            style="background: linear-gradient(135deg, #7c3aed, #4f46e5); box-shadow: 0 4px 15px rgba(124,58,237,0.4);"
        >
            <span wire:loading.remove>Crear mi cuenta</span>
            <span wire:loading>Creando cuenta...</span>
        </button>

        {{-- Link a login --}}
        <p class="text-center text-sm text-purple-400">
            ¿Ya tienes cuenta?
            <a href="{{ route('login') }}" wire:navigate
               class="text-purple-200 font-medium hover:text-white transition">
                Inicia sesión
            </a>
        </p>
    </form>
</div>
