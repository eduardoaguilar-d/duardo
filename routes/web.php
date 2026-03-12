<?php

use App\Http\Controllers\Webhook\WhatsAppWebhookController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Route::get('webhook/whatsapp', [WhatsAppWebhookController::class, 'verify'])->name('webhook.whatsapp.verify');
Route::post('webhook/whatsapp', [WhatsAppWebhookController::class, 'handle'])->name('webhook.whatsapp.handle');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Volt::route('whatsapp/connect', 'pages.whatsapp.connect')->name('whatsapp.connect');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
