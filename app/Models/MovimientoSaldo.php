<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoSaldo extends Model
{
    use HasFactory;

    protected $table = 'movimientos_saldo';

    /**
     * Campos asignables
     */
    protected $fillable = [
        'usuario_id',
        'monto',
        'currency',
        'amount_cents',
        'estatus_movimiento_id',
        'saldo_resultante',
        'saldo_antes',           // <-- nuevo
        'descripcion',
        'payment_intent_id',
        'stripe_charge_id',
        'customer_id',
        'payment_method',
        'payment_method_type',   // <-- nuevo
        'card_brand',            // <-- nuevo
        'card_last4',            // <-- nuevo
        'fees_amount',           // <-- nuevo
        'fees_currency',         // <-- nuevo
        'net_amount',            // <-- nuevo
        'fees_raw',              // <-- nuevo (JSON)
        'metadata',
        'stripe_event_id',
        'webhook_payload',
        'processed_at',
        'failure_code',
        'failure_message',
        'idempotency_key',
        'refunded_amount',
        'reverted',
        'tipo',
        'id_solicitud'
    ];

    /**
     * Casts
     */
    protected $casts = [
        'monto' => 'decimal:2',
        'nuevo_monto' => 'decimal:2',
        'amount_cents' => 'integer',
        'metadata' => 'array',
        'refunded_amount' => 'decimal:2',
        'reverted' => 'boolean',
        'processed_at' => 'datetime',

        // casts nuevos
        'saldo_antes' => 'decimal:2',
        'fees_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'fees_raw' => 'array',
        'card_last4' => 'string',
        'card_brand' => 'string',
        'payment_method_type' => 'string',
        'fees_currency' => 'string',
    ];

    public function estatusMovimiento(): BelongsTo
    {
        return $this->belongsTo(EstatusMovimiento::class, 'estatus_movimiento_id');
    }
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
