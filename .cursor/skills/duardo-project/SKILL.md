---
name: duardo-project
description: Contexto técnico del proyecto Duardo (Laravel 12, Livewire 3, Volt, Alpine, Tailwind v4). Usar al modificar backend, frontend, componentes Livewire/Volt, estilos, dark mode o al añadir dependencias para respetar versiones y convenciones del proyecto.
---

# Proyecto Duardo – Stack y convenciones

## Stack y versiones

| Tecnología    | Versión  | Notas |
|---------------|----------|--------|
| PHP           | ^8.2     | Requerido por Laravel 12 |
| Laravel       | ^12.0    | |
| Livewire      | ^3.6.4   | Incluye Alpine; no inicializar Alpine en `app.js` |
| Livewire Volt | ^1.7.0   | Componentes single-file en Blade |
| Tailwind CSS  | ^4.2.1   | Configuración en CSS, no en `tailwind.config.js` |
| @tailwindcss/vite | ^4.2.1 | Obligatorio para `@import 'tailwindcss'` |
| Vite          | ^7.0.7   | |
| Alpine.js     | ^3.15.8  | Gestionado por Livewire; plugins vía `alpine:init` |

Al añadir o actualizar paquetes, mantener estas versiones y no mezclar Tailwind v3 con v4.

---

## Laravel

- Rutas: `routes/web.php`, `routes/auth.php`.
- Auth y páginas de perfil definidas con **Volt** en `routes/auth.php` (`Volt::route(...)`).
- Base de datos por defecto: SQLite (`DB_CONNECTION=sqlite`).
- Layout principal: `resources/views/layouts/app.blade.php` (incluye navegación Livewire y script de dark mode).

---

## Livewire 3 y Volt

- **Componentes clásicos**: clase en `app/Livewire/`, vista en `resources/views/livewire/<nombre>.blade.php`. Ejemplo: `Counter`, `Layout\Navigation`.
- **Componentes Volt**: solo Blade en `resources/views/livewire/` con `use Livewire\Volt\Component;` y bloques `<?php ... ?>` o atributos `#[Layout(...)]`. Usados en auth (login, register, forgot-password, etc.) y formularios de perfil (update-profile-information-form, update-password-form, delete-user-form).
- En vistas: `<livewire:nombre.componente />` o `@livewire('nombre.componente')`. Navegación SPA: `wire:navigate` en enlaces.
- Siempre incluir `@livewireStyles` en `<head>` y `@livewireScripts` antes de `</body>` (o en el layout que ya los tenga).
- Directivas habituales: `wire:model`, `wire:submit`, `wire:click`, `wire:navigate`.

---

## Alpine.js

- **No** llamar a `Alpine.start()` ni importar e iniciar Alpine en `resources/js/app.js`; Livewire 3 ya lo incluye.
- Para plugins de Alpine, registrar en el evento `alpine:init` en `app.js`:

```js
document.addEventListener('alpine:init', () => {
    // Alpine.plugin(TuPlugin);
});
```

- En las vistas se puede usar `x-data`, `x-show`, `@click`, etc. sin tocar `app.js` para el core.

---

## Tailwind CSS v4

- Entrada de estilos: `resources/css/app.css`.
- **Sintaxis obligatoria** en ese archivo: `@import 'tailwindcss';` (no `@tailwind base/components/utilities`).
- Contenido/scan: definido con `@source` en `app.css` (paths a Blade, JS, vendor). No depender de `tailwind.config.js` para `content`.
- Tema y fuentes: bloque `@theme { ... }` en `app.css` (ej. `--font-sans`).
- Dark mode: variante `@custom-variant dark (&:where(.dark, .dark *));`. La clase `.dark` se pone en `<html>` (no en `tailwind.config.js`).
- En `vite.config.js` debe estar el plugin `tailwindcss()` de `@tailwindcss/vite`. En `postcss.config.js` **no** incluir el plugin `tailwindcss` (solo, por ejemplo, `autoprefixer`).
- Requiere **tailwindcss@4** en `package.json`; con `tailwindcss@3` el plugin de Vite no resuelve `tailwindcss` y falla el build.
- Plugins opcionales v4 (ej. forms): en CSS con `@plugin "@tailwindcss/forms";` si se necesita.

---

## Dark mode

- Estado: clase `.dark` en `<html>`; persistencia con `localStorage.theme` (`'dark'` | `'light'`).
- Script en layout: aplica tema al cargar según `localStorage` y `prefers-color-scheme`.
- Toggle: función global `toggleDarkMode()` en el layout (cambia clase y `localStorage`; opcionalmente intercambia íconos sol/luna por id).
- Estilos: usar utilidades `dark:` en las clases (ya cubiertas por la variante en `app.css`).
- Evitar FOUC: el script que aplica `.dark` está en `<head>` sin `defer`, para que se ejecute antes del primer paint.

---

## Vite y assets

- Entrada: `resources/css/app.css`, `resources/js/app.js`. En Blade: `@vite(['resources/css/app.css', 'resources/js/app.js'])`.
- `vite.config.js`: `laravel(...)` y `tailwindcss()` (de `@tailwindcss/vite`). No quitar el plugin de Tailwind.
- Build: `npm run build`. Dev: `npm run dev`. Comando completo del proyecto: `composer dev` (serve + queue + pail + vite).

---

## Utilidades de UI

- `[x-cloak] { display: none !important; }` en `app.css` para ocultar contenido hasta que Alpine/Livewire lo procese; usar `x-cloak` en el elemento.
- Fuente: Inter (Google Fonts) en el layout; definida también en `@theme` como `--font-sans`.

---

## Tests

- Tests de características en `tests/Feature/`.
- Componentes Volt: `Volt::test('livewire.nombre.componente')`, `$response->assertSeeVolt('livewire.nombre.componente')`.
- Ejemplos: `ProfileTest.php`, `Auth/AuthenticationTest.php`, `Auth/RegistrationTest.php`.

---

## Resumen de errores frecuentes

1. **PostCSS / "Unknown word use strict"**: Causado por usar `@import 'tailwindcss'` con Tailwind procesado por PostCSS en vez del plugin de Vite. Solución: `@tailwindcss/vite` en `vite.config.js` y quitar `tailwindcss` de `postcss.config.js`.
2. **"Can't resolve 'tailwindcss'"**: Tener `tailwindcss@3` con `@tailwindcss/vite`. Solución: `npm install tailwindcss@4 --save-dev`.
3. **Alpine duplicado o no funciona**: No inicializar Alpine en `app.js`; usar solo `alpine:init` para plugins.
4. **Dark mode no aplica**: Asegurar que el script del layout se ejecute y que los estilos usen la variante `dark:` y la clase `.dark` en `<html>`.
