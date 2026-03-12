<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessWhatsAppWebhookJob;
use App\Models\WhatsappConnection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Verificación GET: Meta envía hub.mode, hub.verify_token, hub.challenge.
     * Devolver el challenge si el verify_token coincide.
     */
    public function verify(Request $request): Response
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $expectedToken = config('services.meta.webhook_verify_token');

        if ($mode === 'subscribe' && $challenge && $expectedToken && hash_equals((string) $expectedToken, (string) $token)) {
            return response($challenge, 200, ['Content-Type' => 'text/plain']);
        }

        return response('Forbidden', 403);
    }

    /**
     * Eventos POST: validar firma, resolver cliente por phone_number_id, encolar proceso.
     */
    public function handle(Request $request): Response
    {
        $signature = $request->header('X-Hub-Signature-256');
        $appSecret = config('services.meta.app_secret');

        if (! $signature || ! $appSecret) {
            Log::warning('WhatsApp webhook: missing signature or app secret');
            return response('', 403);
        }

        $payload = $request->getContent();
        $expected = 'sha256=' . hash_hmac('sha256', $payload, $appSecret);

        if (! hash_equals($expected, $signature)) {
            Log::warning('WhatsApp webhook: invalid signature');
            return response('', 403);
        }

        $data = $request->all();
        if (empty($data['object']) || $data['object'] !== 'whatsapp_business_account') {
            return response('', 200);
        }

        $entries = $data['entry'] ?? [];
        if (! is_array($entries)) {
            return response('', 200);
        }

        foreach ($entries as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                $value = $change['value'] ?? [];
                $phoneNumberId = $value['metadata']['phone_number_id'] ?? null;

                if (! $phoneNumberId) {
                    continue;
                }

                $connection = WhatsappConnection::where('phone_number_id', $phoneNumberId)
                    ->where('status', WhatsappConnection::STATUS_CONNECTED)
                    ->first();

                if ($connection) {
                    ProcessWhatsAppWebhookJob::dispatch($connection, $value);
                } else {
                    Log::info('WhatsApp webhook: no connection for phone_number_id', ['phone_number_id' => $phoneNumberId]);
                }
            }
        }

        return response('', 200);
    }
}
