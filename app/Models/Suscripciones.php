<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Suscripciones extends Model
{
    use HasFactory;

    protected $table = 'suscripciones';

    protected $fillable = [
        'usuario_id',
        'id_plan',
        'fecha_inicio',
        'fecha_vencimiento',
        'estado',
        'perfiles_utilizados',
        'facturas_realizadas',
        'rfc_realizados'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_vencimiento' => 'date',
        'perfiles_utilizados' => 'integer',
        'facturas_realizadas' => 'integer'
    ];

    const ESTADO_ACTIVA = 'activa';
    const ESTADO_VENCIDA = 'vencida';
    const ESTADO_CANCELADA = 'cancelada';

    public function getFechaVencimientoAttribute($vlue)
    {
        $val= Carbon::parse($vlue)->format('d-M-Y H:i');
        return $val;
    }
    /**
     * Relación con el usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relación con el plan
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(CatPlanes::class, 'id_plan');
    }

    /**
     * Scope para suscripciones activas
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVA);
    }

    /**
     * Scope para suscripciones vigentes (activas y no vencidas)
     */
    public function scopeVigentes($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVA)
            ->where('fecha_vencimiento', '>=', now());
    }

    /**
     * Verificar si la suscripción está vigente
     */
    public function estaVigente(): bool
    {
        return $this->estado === self::ESTADO_ACTIVA &&
            ($this->fecha_vencimiento === null || $this->fecha_vencimiento >= now());
    }

    /**
     * Verificar si la suscripción está vencida
     */
    public function estaVencida(): bool
    {
        return $this->estado === self::ESTADO_ACTIVA &&
            $this->fecha_vencimiento < now();
    }


}
