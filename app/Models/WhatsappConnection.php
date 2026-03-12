<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappConnection extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONNECTED = 'connected';
    public const STATUS_ERROR = 'error';
    public const STATUS_DISCONNECTED = 'disconnected';

    protected $fillable = [
        'phone_number_id',
        'waba_id',
        'access_token',
        'verify_token',
        'status',
        'last_error',
    ];

    protected $hidden = [
        'access_token',
    ];

    protected $casts = [
        'access_token' => 'encrypted',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function isConnected(): bool
    {
        return $this->status === self::STATUS_CONNECTED;
    }
}
