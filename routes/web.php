<?php

use App\Http\Controllers\Webhook\WhatsAppWebhookController;
use App\Http\Controllers\WhatsApp\MetaOAuthController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Route::get('webhook/whatsapp', [WhatsAppWebhookController::class, 'verify'])->name('webhook.whatsapp.verify');
Route::post('webhook/whatsapp', [WhatsAppWebhookController::class, 'handle'])->name('webhook.whatsapp.handle');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Volt::route('whatsapp/connect', 'pages.whatsapp.connect')->name('whatsapp.connect');
    Route::get('whatsapp/oauth', [MetaOAuthController::class, 'redirect'])->name('whatsapp.oauth.redirect');
    Route::get('whatsapp/oauth/callback', [MetaOAuthController::class, 'callback'])->name('whatsapp.oauth.callback');
    Route::post('whatsapp/oauth/embedded-callback', [MetaOAuthController::class, 'embeddedCallback'])->name('whatsapp.oauth.embeddedCallback');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
