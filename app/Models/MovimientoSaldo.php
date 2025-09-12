<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoSaldo extends Model
{
    use HasFactory;

    protected $table = 'movimientos_saldo';

    protected $fillable = [
        'usuario_id',
        'monto',
        'tipo_movimiento_id',
        'nuevo_monto',
        'factura_id',
        'descripcion'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'nuevo_monto' => 'decimal:2',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tipoMovimiento(): BelongsTo
    {
        return $this->belongsTo(EstatusMovimiento::class, 'tipo_movimiento_id');
    }

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }
}