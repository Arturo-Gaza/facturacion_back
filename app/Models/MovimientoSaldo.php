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
        'estatus_movimiento_id', // coincide con la migración
        'saldo_resultante',
        'descripcion',
        'payment_intent_id',
        'stripe_charge_id',
        'customer_id',
        'payment_method',
        'metadata',
        'stripe_event_id',
        'webhook_payload',
        'processed_at',
        'failure_code',
        'failure_message',
        'idempotency_key',
        'refunded_amount',
        'reverted',
        'tipo'
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
    ];

    /**
     * Relaciones
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relación al estatus/ cat_estatus_movimiento
     * Ajusta el nombre del modelo si tienes otro.
     */
    public function estatusMovimiento(): BelongsTo
    {
        return $this->belongsTo(EstatusMovimiento::class, 'estatus_movimiento_id');
    }
}
