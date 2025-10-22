<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatMontosPrepago extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cat_montos_prepago';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'creditos',
        'monto',
        'activo',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'creditos' => 'integer',
        'monto' => 'decimal:2',
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope para planes activos.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para planes inactivos.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactivos($query)
    {
        return $query->where('activo', false);
    }

    /**
     * Scope para ordenar por créditos de forma ascendente.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdenarPorCreditos($query)
    {
        return $query->orderBy('creditos', 'asc');
    }

    /**
     * Scope para ordenar por monto de forma ascendente.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdenarPorMonto($query)
    {
        return $query->orderBy('monto', 'asc');
    }

    /**
     * Calcular el costo por crédito del plan.
     *
     * @return float
     */
    public function getCostoPorCreditoAttribute()
    {
        if ($this->creditos > 0) {
            return $this->monto / $this->creditos;
        }
        
        return 0;
    }

    /**
     * Formatear el monto como moneda.
     *
     * @return string
     */
    public function getMontoFormateadoAttribute()
    {
        return '$ ' . number_format($this->monto, 2) . ' MXN';
    }

    /**
     * Formatear el costo por crédito como moneda.
     *
     * @return string
     */
    public function getCostoPorCreditoFormateadoAttribute()
    {
        return '$ ' . number_format($this->costo_por_credito, 2) . ' MXN';
    }

    /**
     * Verificar si el plan es profesional (más de 100 créditos).
     *
     * @return bool
     */
    public function getEsProfesionalAttribute()
    {
        return $this->creditos >= 100;
    }

    /**
     * Activar el plan.
     *
     * @return void
     */
    public function activar()
    {
        $this->update(['activo' => true]);
    }

    /**
     * Desactivar el plan.
     *
     * @return void
     */
    public function desactivar()
    {
        $this->update(['activo' => false]);
    }
}