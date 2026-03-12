<?php

use App\Models\Client;
use App\Models\WhatsappConnection;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app-shell')] class extends Component
{
    public string $connection_name = '';
    public string $phone_number_id = '';
    public string $waba_id = '';
    public string $access_token = '';
    public ?int $client_id = null;

    public function mount(): void
    {
        $this->connection_name = auth()->user()->clients()->first()?->name ?? '';
        $conn = auth()->user()->clients()->with('whatsappConnection')->get()->first(fn ($c) => $c->whatsappConnection)?->whatsappConnection;
        if ($conn) {
            $this->client_id = $conn->client_id;
            $this->connection_name = $conn->client->name;
            $this->phone_number_id = $conn->phone_number_id;
            $this->waba_id = $conn->waba_id;
            $this->access_token = ''; // nunca rellenar token por seguridad
        }
    }

    protected function rules(): array
    {
        return [
            'connection_name' => ['required', 'string', 'max:255'],
            'phone_number_id' => ['required', 'string', 'max:255'],
            'waba_id' => ['required', 'string', 'max:255'],
            'access_token' => [$this->client_id ? 'nullable' : 'required', 'string'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $user = auth()->user();

        if ($this->client_id) {
            $client = $user->clients()->findOrFail($this->client_id);
            $client->update(['name' => $this->connection_name]);
            $conn = $client->whatsappConnection;
            if (! $conn) {
                $conn = $client->whatsappConnection()->make([]);
                $conn->phone_number_id = $this->phone_number_id;
                $conn->waba_id = $this->waba_id;
                $conn->status = WhatsappConnection::STATUS_CONNECTED;
            }
            if (filled($this->access_token)) {
                $conn->access_token = $this->access_token;
                $conn->last_error = null;
            }
            $conn->phone_number_id = $this->phone_number_id;
            $conn->waba_id = $this->waba_id;
            $conn->status = WhatsappConnection::STATUS_CONNECTED;
            $conn->save();
        } else {
            $client = $user->clients()->create(['name' => $this->connection_name]);
            $client->whatsappConnection()->create([
                'phone_number_id' => $this->phone_number_id,
                'waba_id' => $this->waba_id,
                'access_token' => $this->access_token,
                'status' => WhatsappConnection::STATUS_CONNECTED,
            ]);
        }

        session()->flash('status', 'WhatsApp conectado correctamente.');
        $this->redirect(route('dashboard'), navigate: true);
    }
}; ?>

<div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Conectar WhatsApp</h1>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 border border-gray-100 dark:border-gray-800 shadow-sm">
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-6">
                Usa las credenciales de tu app en
                <a href="https://developers.facebook.com" target="_blank" rel="noopener" class="text-indigo-600 dark:text-indigo-400 hover:underline">Meta for Developers</a>.
                Necesitas el <strong>Phone Number ID</strong>, el <strong>WhatsApp Business Account ID (WABA)</strong> y un <strong>Access Token</strong>.
            </p>

            <form wire:submit="save" class="space-y-5">
                <div>
                    <label for="connection_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre de la conexión</label>
                    <input type="text" wire:model="connection_name" id="connection_name"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2.5"
                           placeholder="Ej. Mi negocio">
                    <x-input-error :messages="$errors->get('connection_name')" class="mt-1" />
                </div>

                <div>
                    <label for="phone_number_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone Number ID</label>
                    <input type="text" wire:model="phone_number_id" id="phone_number_id"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2.5 font-mono text-sm"
                           placeholder="Ej. 123456789012345">
                    <x-input-error :messages="$errors->get('phone_number_id')" class="mt-1" />
                </div>

                <div>
                    <label for="waba_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">WhatsApp Business Account ID (WABA)</label>
                    <input type="text" wire:model="waba_id" id="waba_id"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2.5 font-mono text-sm"
                           placeholder="Ej. 987654321098765">
                    <x-input-error :messages="$errors->get('waba_id')" class="mt-1" />
                </div>

                <div>
                    <label for="access_token" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Access Token</label>
                    <input type="password" wire:model="access_token" id="access_token"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2.5 font-mono text-sm"
                           placeholder="{{ $client_id ? 'Dejar en blanco para no cambiar' : 'Token de la app Meta' }}" autocomplete="off">
                    <x-input-error :messages="$errors->get('access_token')" class="mt-1" />
                    @if($client_id)
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Solo rellena si quieres actualizar el token.</p>
                    @endif
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ $client_id ? 'Actualizar conexión' : 'Conectar' }}
                    </button>
                    <a href="{{ route('dashboard') }}" wire:navigate
                       class="inline-flex items-center px-4 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium rounded-lg">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
