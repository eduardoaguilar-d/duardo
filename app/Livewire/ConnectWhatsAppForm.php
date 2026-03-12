<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\WhatsappConnection;
use App\Services\WhatsAppCloudApi;
use Livewire\Component;

class ConnectWhatsAppForm extends Component
{
    public string $connection_name = '';
    public string $phone_number_id = '';
    public string $waba_id = '';
    public string $access_token = '';
    public ?int $client_id = null;

    /** Resultado de la última verificación: 'success' | 'error' | null */
    public ?string $verifyStatus = null;
    public string $verifyMessage = '';

    public function mount(): void
    {
        $user = auth()->user();
        $this->connection_name = $user->clients()->first()?->name ?? '';
        $clientWithConn = $user->clients()->with('whatsappConnection')->get()->first(fn ($c) => $c->whatsappConnection);
        if ($clientWithConn && $clientWithConn->whatsappConnection) {
            $conn = $clientWithConn->whatsappConnection;
            $this->client_id = $conn->client_id;
            $this->connection_name = $conn->client->name;
            $this->phone_number_id = $conn->phone_number_id;
            $this->waba_id = $conn->waba_id;
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

    /**
     * Verifica las credenciales contra la API de Meta. Si son válidas, guarda automáticamente y redirige.
     */
    public function verify(): void
    {
        $this->verifyStatus = null;
        $this->verifyMessage = '';

        $this->validate([
            'connection_name' => ['required', 'string', 'max:255'],
            'phone_number_id' => ['required', 'string', 'max:255'],
            'waba_id' => ['required', 'string', 'max:255'],
            'access_token' => [$this->client_id ? 'nullable' : 'required', 'string'],
        ], [], [
            'connection_name' => 'Nombre de la conexión',
            'phone_number_id' => 'Phone Number ID',
            'waba_id' => 'WABA ID',
            'access_token' => 'Access Token',
        ]);

        $tokenToUse = $this->access_token;
        if ($this->client_id && $tokenToUse === '') {
            $conn = auth()->user()->clients()->findOrFail($this->client_id)->whatsappConnection;
            $tokenToUse = $conn?->access_token ?? '';
        }
        if ($tokenToUse === '') {
            $this->verifyStatus = 'error';
            $this->verifyMessage = 'El Access Token es obligatorio para verificar.';
            return;
        }

        $result = WhatsAppCloudApi::verifyCredentials(
            $this->phone_number_id,
            $tokenToUse,
            $this->waba_id
        );

        if (! $result['valid']) {
            $this->verifyStatus = 'error';
            $this->verifyMessage = $result['message'];
            return;
        }

        $this->persistConnection();
        session()->flash('status', 'WhatsApp verificado y guardado correctamente.');
        $this->redirect(route('dashboard'), navigate: true);
    }

    public function save(): void
    {
        $this->validate();

        $tokenToUse = $this->access_token;
        if ($this->client_id && $tokenToUse === '') {
            $conn = auth()->user()->clients()->findOrFail($this->client_id)->whatsappConnection;
            $tokenToUse = $conn?->access_token ?? '';
        }

        if ($tokenToUse !== '') {
            $result = WhatsAppCloudApi::verifyCredentials(
                $this->phone_number_id,
                $tokenToUse,
                $this->waba_id
            );
            if (! $result['valid']) {
                $this->verifyStatus = 'error';
                $this->verifyMessage = $result['message'];
                return;
            }
        }

        $this->verifyStatus = null;
        $this->verifyMessage = '';
        $this->persistConnection();
        session()->flash('status', 'WhatsApp conectado correctamente.');
        $this->redirect(route('dashboard'), navigate: true);
    }

    protected function persistConnection(): void
    {
        $user = auth()->user();

        if ($this->client_id) {
            $client = $user->clients()->findOrFail($this->client_id);
            $client->update(['name' => $this->connection_name]);
            $conn = $client->whatsappConnection;
            if (! $conn) {
                $conn = $client->whatsappConnection()->make([
                    'phone_number_id' => $this->phone_number_id,
                    'waba_id' => $this->waba_id,
                    'status' => WhatsappConnection::STATUS_CONNECTED,
                ]);
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
    }

    public function render()
    {
        return view('livewire.connect-whatsapp-form');
    }
}
