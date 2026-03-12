<?php

namespace App\Jobs;

use App\Models\WhatsappConnection;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessWhatsAppWebhookJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public WhatsappConnection $connection,
        public array $value
    ) {}

    /**
     * Execute the job.
     * Procesa el payload entrante (mensajes, estados, etc.) para la conexión del cliente.
     */
    public function handle(): void
    {
        $clientId = $this->connection->client_id;

        if (! empty($this->value['messages'])) {
            foreach ($this->value['messages'] as $message) {
                Log::channel('single')->info('WhatsApp message received', [
                    'client_id' => $clientId,
                    'from' => $message['from'] ?? null,
                    'type' => $message['type'] ?? null,
                    'id' => $message['id'] ?? null,
                ]);
                // Aquí: guardar en BD, responder con bot, etc.
            }
        }

        if (! empty($this->value['statuses'])) {
            foreach ($this->value['statuses'] as $status) {
                Log::channel('single')->info('WhatsApp status update', [
                    'client_id' => $clientId,
                    'id' => $status['id'] ?? null,
                    'status' => $status['status'] ?? null,
                ]);
            }
        }
    }
}
