<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppCloudApi
{
    protected const GRAPH_VERSION = 'v21.0';
    protected const BASE_URL = 'https://graph.facebook.com';

    /**
     * Verifica credenciales: primero lista los números del WABA; si el Phone Number ID
     * está en la lista, las credenciales son válidas. Así se evitan errores de permisos
     * al consultar el número directamente.
     */
    public static function verifyCredentials(string $phoneNumberId, string $accessToken, ?string $wabaId = null): array
    {
        // 1) Si tenemos WABA ID, intentar listar números (suele funcionar mejor con permisos estándar)
        if ($wabaId !== null && $wabaId !== '') {
            $listUrl = self::BASE_URL . '/' . self::GRAPH_VERSION . '/' . $wabaId . '/phone_numbers';
            $listResponse = Http::timeout(10)->get($listUrl, [
                'access_token' => $accessToken,
            ]);

            if ($listResponse->successful()) {
            $listData = $listResponse->json();
            $phones = $listData['data'] ?? [];
            $found = collect($phones)->firstWhere('id', $phoneNumberId);
            if ($found) {
                return [
                    'valid' => true,
                    'message' => 'Credenciales correctas. Número: ' . ($found['display_phone_number'] ?? $phoneNumberId),
                    'data' => $found,
                ];
            }
            return [
                'valid' => false,
                'message' => 'El Phone Number ID no pertenece a esta cuenta (WABA). Comprueba que ambos IDs sean de la misma app en Meta.',
            ];
            }

            // WABA proporcionado pero la lista falló (WABA incorrecto, permisos, etc.): no usar GET directo
            return [
                'valid' => false,
                'message' => self::formatMetaError($listResponse->json(), $listResponse->status(), $listResponse->body()),
            ];
        }

        // 2) Solo sin WABA: intentar GET directo al Phone Number ID
        $directUrl = self::BASE_URL . '/' . self::GRAPH_VERSION . '/' . $phoneNumberId;
        $directResponse = Http::timeout(10)->get($directUrl, [
            'access_token' => $accessToken,
        ]);

        if ($directResponse->successful()) {
            $data = $directResponse->json();
            return [
                'valid' => true,
                'message' => 'Credenciales correctas. Número: ' . ($data['display_phone_number'] ?? $phoneNumberId),
                'data' => $data,
            ];
        }

        return [
            'valid' => false,
            'message' => self::formatMetaError($directResponse->json(), $directResponse->status(), $directResponse->body()),
        ];
    }

    protected static function formatMetaError(?array $body, int $status, string $rawBody): string
    {
        $message = isset($body['error']['message']) ? (string) $body['error']['message'] : (string) $rawBody;
        $code = isset($body['error']['code']) ? $body['error']['code'] : null;

        if (stripos($message, 'Unsupported get request') !== false || stripos($message, 'does not exist') !== false || stripos($message, 'missing permissions') !== false) {
            return 'Credenciales rechazadas por Meta. Comprueba: (1) Phone Number ID y WABA ID son de la misma app en Meta for Developers. (2) El token tiene permisos whatsapp_business_management. (3) El número está asociado a tu cuenta de WhatsApp Business. Revisa la configuración en developers.facebook.com.';
        }

        if ($code === 190 || stripos($message, 'access token') !== false || stripos($message, 'expired') !== false) {
            return 'Token inválido o expirado. Genera un nuevo Access Token en Meta for Developers (recomendado: System User con permisos de WhatsApp).';
        }

        if ($code === 100 || stripos($message, 'invalid') !== false) {
            return 'ID o token incorrecto. Verifica que el Phone Number ID y el WABA ID coincidan con los de tu app en Meta.';
        }

        if ($status === 0 || stripos($message, 'Connection') !== false) {
            return 'No se pudo conectar con Meta. Revisa tu conexión a internet o intenta más tarde.';
        }

        return strlen($message) > 200 ? 'Error de la API de Meta. Revisa Phone Number ID, WABA ID y token en developers.facebook.com.' : $message;
    }
}
