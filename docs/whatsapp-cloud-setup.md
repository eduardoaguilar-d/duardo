# WhatsApp Cloud API (Meta) – Requisitos y arquitectura

## 1. Qué necesitas en Meta (por cada app / entorno)

- **Cuenta Meta for Developers**: [developers.facebook.com](https://developers.facebook.com)
- **App** con producto **WhatsApp** añadido.
- **Credenciales de la app** (desde el panel de la app):
  - **App ID**
  - **App Secret** (para firmar y verificar el webhook)
- **WhatsApp > Configuración** en la app:
  - **Token de acceso** (temporal o de System User; en producción usar System User).
  - **ID del número de teléfono** (Phone Number ID) que enviará/recibirá mensajes.
  - **ID de la cuenta de WhatsApp Business** (WABA ID).

Para cada **cliente** (cuenta de negocio) que conectes en tu dashboard guardarás:

- Phone Number ID  
- WhatsApp Business Account ID (WABA ID)  
- Access Token (guardado cifrado en nuestra base de datos)  
- Opcional: Verify Token para el webhook (o uno global por app)

Referencia: [Set up webhooks - Meta](https://developers.facebook.com/docs/whatsapp/cloud-api/guides/set-up-webhooks/).

---

## 2. Webhook: una URL para todos los clientes

Meta permite **una sola URL de webhook por app**. No se puede registrar una URL distinta por cliente.

- En el panel de Meta configuras **una** URL (ej. `https://tudominio.com/webhook/whatsapp`).
- En cada POST, Meta envía en el payload `phone_number_id` (y contexto de la cuenta).
- Nosotros:
  1. Verificamos la firma con el **App Secret**.
  2. Leemos `phone_number_id` (y si hace falta WABA ID) del body.
  3. Buscamos en nuestra base qué **cliente** tiene ese `phone_number_id`.
  4. Procesamos el evento para ese cliente (cola, jobs, etc.).

Así, **un solo webhook sirve para todos los clientes**; la “separación” es por datos (phone_number_id) y por cómo guardamos las credenciales por cliente.

---

## 3. Verificación del webhook (GET)

Cuando registras la URL en Meta, Meta hace un **GET** con:

- `hub.mode=subscribe`
- `hub.verify_token=` (el valor que tú definas)
- `hub.challenge=` (número que debes devolver en la respuesta)

Debes:

1. Comprobar que `hub.verify_token` coincida con tu valor (por ejemplo el de `.env` o el guardado por cliente).
2. Responder con **200** y el cuerpo = `hub.challenge` (texto plano).

Si usas un **verify token global** (recomendado para empezar), lo pones en `.env`. Si más adelante quieres uno por cliente, se puede guardar en `whatsapp_connections.verify_token` y comprobar contra el que Meta envía (normalmente se usa uno global).

---

## 4. Eventos entrantes (POST)

- Meta envía **POST** con header `X-Hub-Signature-256: sha256=...`.
- Hay que validar la firma con **App Secret** (HMAC-SHA256) y comparación a tiempo constante.
- Responder **200** rápido (en menos de ~20 s) y procesar el cuerpo en segundo plano (colas/jobs).
- En el JSON, la estructura incluye `entry[].changes[].value`; dentro puedes encontrar `metadata.phone_number_id` y los mensajes/estados.

---

## 5. Almacenamiento seguro por cliente

- Cada **cliente** tiene (al menos) una fila en `whatsapp_connections` con:
  - `phone_number_id`, `waba_id`
  - `access_token` **cifrado** (Laravel `encrypt()` con `APP_KEY`).
- El **App Secret** no es por cliente; es de la app de Meta. Se usa solo para verificar la firma del webhook. Lo puedes guardar en `.env` como `META_APP_SECRET`.
- Opcional en `.env`: `META_WEBHOOK_VERIFY_TOKEN` para el GET de verificación.

---

## 6. Resumen de pasos (tu lado)

1. Crear app en Meta, añadir WhatsApp, obtener App ID y App Secret.
2. En la app, configurar la URL del webhook (una sola) y el Verify Token.
3. Por cada negocio/cliente: obtener Phone Number ID, WABA ID y Access Token (idealmente System User) y guardarlos en tu dashboard; el token siempre cifrado en BD.
4. Implementar el endpoint:
   - GET: verificar `hub.verify_token` y devolver `hub.challenge`.
   - POST: verificar firma, leer `phone_number_id`, resolver cliente, encolar proceso y responder 200.

Con esto tienes conectada la API oficial de WhatsApp Cloud (Meta) con un webhook único y credenciales seguras por cliente.
