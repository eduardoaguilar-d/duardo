<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('whatsapp_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('phone_number_id')->unique()->comment('Meta Phone Number ID');
            $table->string('waba_id')->comment('WhatsApp Business Account ID');
            $table->text('access_token')->comment('Access token cifrado con APP_KEY (cast encrypted en modelo)');
            $table->string('verify_token')->nullable()->comment('Token para verificación GET del webhook (opcional por cliente)');
            $table->string('status')->default('pending')->comment('pending|connected|error|disconnected');
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->index(['phone_number_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_connections');
    }
};
