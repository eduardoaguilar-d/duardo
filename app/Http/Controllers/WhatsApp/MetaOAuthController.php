<?php

namespace App\Http\Controllers\WhatsApp;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\WhatsappConnection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaOAuthController extends Controller
{
    protected const GRAPH_VERSION = 'v21.0';
    protected const OAUTH_DIALOG = 'https://www.facebook.com/' . self::GRAPH_VERSION . '/dialog/oauth';
    protected const TOKEN_URL = 'https://graph.facebook.com/' . self::GRAPH_VERSION . '/oauth/access_token';

    /**
     * Scopes OAuth. Para listar negocios/WABAs (GET /me/businesses) Meta exige business_management.
     * Si Meta devuelve "Invalid Scopes: business_management", añade el permiso en la app:
     * Meta for Developers → tu app → Inicio de sesión para empresas → Configuración (o Crear configuración desde plantilla "WhatsApp Embedded Signup").
     */
    protected static function scopes(): string
    {
        return implode(',', [
            'public_profile',
            'business_management',
            'whatsapp_business_management',
            'whatsapp_business_messaging',
        ]);
    }

    /**
     * Redirige al usuario a Meta para iniciar sesión (proveedor de tecnología).
     */
    public function redirect(Request $request): RedirectResponse
    {
        $appId = config('services.meta.app_id');
        if (! $appId) {
            return redirect()->route('dashboard')->with('status', 'Falta configurar META_APP_ID en .env. Obtén el ID de tu app en Meta for Developers → tu app → Configuración → Básica.');
        }

        $redirectUri = config('services.meta.oauth_redirect_uri');
        if (! $redirectUri) {
            return redirect()->route('dashboard')->with('status', 'Falta configurar META_OAUTH_REDIRECT_URI en .env');
        }

        $state = bin2hex(random_bytes(16));
        session()->put('meta_oauth_state', $state);

        $configId = config('services.meta.embedded_signup_configuration_id');
        $params = [
            'client_id' => config('services.meta.app_id'),
            'redirect_uri' => $redirectUri,
            'state' => $state,
            'response_type' => 'code',
        ];
        if ($configId) {
            $params['config_id'] = (string) $configId;
            $params['override_default_response_type'] = 'true';
        } else {
            $params['scope'] = self::scopes();
        }

        return redirect(self::OAUTH_DIALOG . '?' . http_build_query($params));
    }

    /**
     * Callback de Meta: intercambia code por token, obtiene WABA y números, guarda y redirige.
     */
    public function callback(Request $request): RedirectResponse
    {
        $redirectUri = config('services.meta.oauth_redirect_uri');
        $savedState = session()->pull('meta_oauth_state');

        if ($request->has('error')) {
            return redirect()->route('dashboard')->with('status', 'Meta rechazó el acceso: ' . ($request->get('error_description', $request->get('error'))));
        }

        if (! $savedState || ! hash_equals($savedState, (string) $request->get('state'))) {
            return redirect()->route('dashboard')->with('status', 'Estado OAuth inválido. Intenta de nuevo.');
        }

        $code = $request->get('code');
        if (! $code) {
            return redirect()->route('dashboard')->with('status', 'No se recibió código de autorización.');
        }

        $tokenResponse = Http::get(self::TOKEN_URL, [
            'client_id' => config('services.meta.app_id'),
            'client_secret' => config('services.meta.app_secret'),
            'redirect_uri' => $redirectUri,
            'code' => $code,
        ]);

        Log::channel('single')->info('Meta OAuth: token exchange', [
            'success' => $tokenResponse->successful(),
            'status' => $tokenResponse->status(),
            'body_keys' => $tokenResponse->successful() ? array_keys($tokenResponse->json() ?? []) : null,
            'error' => $tokenResponse->successful() ? null : $tokenResponse->json(),
        ]);

        if (! $tokenResponse->successful()) {
            $err = $tokenResponse->json('error.message', $tokenResponse->body());
            return redirect()->route('dashboard')->with('status', 'Error al obtener token: ' . $err);
        }

        $accessToken = $tokenResponse->json('access_token');
        if (! $accessToken) {
            Log::warning('Meta OAuth: response OK but no access_token in body', ['body' => $tokenResponse->json()]);
            return redirect()->route('dashboard')->with('status', 'Meta no devolvió access token.');
        }

        $wabaAndPhone = $this->fetchFirstWabaAndPhone($accessToken);
        if (! $wabaAndPhone) {
            Log::warning('Meta OAuth: no WABA/phone found. Si los logs muestran "Missing Permission", la app necesita el permiso business_management (Inicio de sesión para empresas con esa configuración) o usar Embedded Signup.');
            return redirect()->route('dashboard')->with('status', 'No se pudo listar tu cuenta de WhatsApp. Si en el log aparece "Missing Permission", en Meta for Developers añade el permiso business_management en Inicio de sesión para empresas (o usa la plantilla "Registro insertado de WhatsApp" / Embedded Signup). Revisa storage/logs/laravel.log.');
        }

        $user = $request->user();
        $client = $user->clients()->firstOrCreate(
            [],
            ['name' => $wabaAndPhone['waba_name'] ?? 'WhatsApp Business']
        );
        $client->update(['name' => $wabaAndPhone['waba_name'] ?? $client->name]);

        $client->whatsappConnection()->updateOrCreate(
            ['client_id' => $client->id],
            [
                'phone_number_id' => $wabaAndPhone['phone_number_id'],
                'waba_id' => $wabaAndPhone['waba_id'],
                'access_token' => $accessToken,
                'status' => WhatsappConnection::STATUS_CONNECTED,
                'last_error' => null,
            ]
        );

        session()->flash('status', 'WhatsApp conectado correctamente con Meta.');
        return redirect()->route('dashboard');
    }

    /**
     * Callback para Embedded Signup (SDK): recibe code (+ opcional waba_id, phone_number_id) y canjea por token.
     */
    public function embeddedCallback(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
            'waba_id' => 'nullable|string',
            'phone_number_id' => 'nullable|string',
        ]);

        $code = $request->input('code');
        $wabaId = $request->input('waba_id');
        $phoneNumberId = $request->input('phone_number_id');

        // Embedded Signup: algunos entornos requieren redirect_uri vacío en el canje
        $tokenResponse = Http::get(self::TOKEN_URL, [
            'client_id' => config('services.meta.app_id'),
            'client_secret' => config('services.meta.app_secret'),
            'redirect_uri' => '', // SDK Embedded Signup
            'code' => $code,
        ]);

        if (! $tokenResponse->successful()) {
            Log::warning('Meta Embedded Signup: token exchange failed', ['body' => $tokenResponse->json(), 'status' => $tokenResponse->status()]);
            return response()->json([
                'success' => false,
                'message' => $tokenResponse->json('error.message', 'Error al canjear el código por token.'),
            ], 400);
        }

        $accessToken = $tokenResponse->json('access_token');
        if (! $accessToken) {
            return response()->json(['success' => false, 'message' => 'Meta no devolvió access token.'], 400);
        }

        $wabaAndPhone = null;
        if ($wabaId && $phoneNumberId) {
            $wabaAndPhone = [
                'waba_id' => $wabaId,
                'phone_number_id' => $phoneNumberId,
                'waba_name' => 'WhatsApp Business',
            ];
        }
        if (! $wabaAndPhone) {
            $wabaAndPhone = $this->fetchFirstWabaAndPhone($accessToken);
        }
        if (! $wabaAndPhone) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró WABA/número. Envía waba_id y phone_number_id desde el evento message del SDK si los tienes.',
            ], 400);
        }

        $user = $request->user();
        $client = $user->clients()->firstOrCreate(
            [],
            ['name' => $wabaAndPhone['waba_name'] ?? 'WhatsApp Business']
        );
        $client->update(['name' => $wabaAndPhone['waba_name'] ?? $client->name]);

        $client->whatsappConnection()->updateOrCreate(
            ['client_id' => $client->id],
            [
                'phone_number_id' => $wabaAndPhone['phone_number_id'],
                'waba_id' => $wabaAndPhone['waba_id'],
                'access_token' => $accessToken,
                'status' => WhatsappConnection::STATUS_CONNECTED,
                'last_error' => null,
            ]
        );

        return response()->json(['success' => true, 'message' => 'WhatsApp conectado correctamente.']);
    }

    /**
     * Obtiene el primer WABA y su primer número. Prueba:
     * 1) me?fields=businesses{owned_whatsapp_business_accounts{phone_numbers}}
     * 2) me/businesses → client_whatsapp_business_accounts → phone_numbers
     */
    protected function fetchFirstWabaAndPhone(string $accessToken): ?array
    {
        $base = 'https://graph.facebook.com/' . self::GRAPH_VERSION;

        // Estrategia 1: me con businesses y owned_whatsapp_business_accounts (puede requerir business_management)
        $me = Http::withToken($accessToken)->get($base . '/me', [
            'fields' => 'id,name,businesses{id,name,owned_whatsapp_business_accounts{id,name,phone_numbers}}',
        ]);
        $meBusinesses = $me->json('businesses.data', []);
        Log::channel('single')->info('Meta OAuth: GET /me (businesses+owned_whatsapp)', [
            'status' => $me->status(),
            'success' => $me->successful(),
            'has_businesses' => $me->successful() && [] !== $meBusinesses,
            'businesses_count' => $me->successful() ? count($meBusinesses) : 0,
            'error' => $me->successful() ? null : $me->json(),
        ]);
        if ($me->successful()) {
            foreach ($meBusinesses as $biz) {
                $wabas = $biz['owned_whatsapp_business_accounts']['data'] ?? [];
                foreach ($wabas as $waba) {
                    $phones = $waba['phone_numbers']['data'] ?? [];
                    if (! empty($phones)) {
                        $phone = $phones[0];
                        return [
                            'waba_id' => $waba['id'],
                            'waba_name' => $waba['name'] ?? $biz['name'],
                            'phone_number_id' => $phone['id'],
                            'display_phone_number' => $phone['display_phone_number'] ?? null,
                        ];
                    }
                }
            }
        }

        // Estrategia 2: me/businesses → client_whatsapp_business_accounts → phone_numbers
        $businessesRes = Http::withToken($accessToken)->get($base . '/me/businesses', [
            'fields' => 'id,name',
        ]);
        Log::channel('single')->info('Meta OAuth: GET /me/businesses', [
            'status' => $businessesRes->status(),
            'success' => $businessesRes->successful(),
            'data_count' => $businessesRes->successful() ? count($businessesRes->json('data', [])) : 0,
            'error' => $businessesRes->successful() ? null : $businessesRes->json(),
        ]);

        if (! $businessesRes->successful()) {
            return null;
        }

        $businesses = $businessesRes->json('data', []);
        foreach ($businesses as $biz) {
            $wabasRes = Http::withToken($accessToken)->get($base . '/' . $biz['id'] . '/client_whatsapp_business_accounts', [
                'fields' => 'id,name',
            ]);
            Log::channel('single')->info('Meta OAuth: GET /{business}/client_whatsapp_business_accounts', [
                'business_id' => $biz['id'],
                'status' => $wabasRes->status(),
                'success' => $wabasRes->successful(),
                'waba_count' => $wabasRes->successful() ? count($wabasRes->json('data', [])) : 0,
                'error' => $wabasRes->successful() ? null : $wabasRes->json(),
            ]);
            if (! $wabasRes->successful() || empty($wabasRes->json('data'))) {
                continue;
            }
            $waba = $wabasRes->json('data.0');
            $phonesRes = Http::withToken($accessToken)->get($base . '/' . $waba['id'] . '/phone_numbers');
            Log::channel('single')->info('Meta OAuth: GET /{waba}/phone_numbers', [
                'waba_id' => $waba['id'],
                'status' => $phonesRes->status(),
                'success' => $phonesRes->successful(),
                'phone_count' => $phonesRes->successful() ? count($phonesRes->json('data', [])) : 0,
                'error' => $phonesRes->successful() ? null : $phonesRes->json(),
            ]);
            if ($phonesRes->successful() && ! empty($phonesRes->json('data'))) {
                $phone = $phonesRes->json('data.0');
                return [
                    'waba_id' => $waba['id'],
                    'waba_name' => $waba['name'] ?? $biz['name'],
                    'phone_number_id' => $phone['id'],
                    'display_phone_number' => $phone['display_phone_number'] ?? null,
                ];
            }
        }

        return null;
    }
}
