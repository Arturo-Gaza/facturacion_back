<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatPlanes extends Model
{
    use HasFactory;

    protected $table = 'cat_planes';

    protected $fillable = [
        'nombre_plan',
        'tipo_plan',
        'tipo_pago',
        'vigencia_inicio',
        'vigencia_fin',
        'precio'
    ];

    protected $casts = [
        'vigencia_inicio' => 'date',
        'vigencia_fin' => 'date',
    ];

    // Constantes para mejor legibilidad en el código
    const TIPO_PERSONAL = 'personal';
    const TIPO_EMPRESARIAL = 'empresarial';

    const PAGO_PREPAGO = 'prepago';
    const PAGO_MENSUAL = 'mensual';

    /**
     * Scope para planes empresariales
     */
    public function scopeEmpresariales($query)
    {
        return $query->where('tipo_plan', self::TIPO_EMPRESARIAL);
    }

    /**
     * Scope para planes personales
     */
    public function scopePersonales($query)
    {
        return $query->where('tipo_plan', self::TIPO_PERSONAL);
    }

    /**
     * Scope para planes prepago
     */
    public function scopePrepago($query)
    {
        return $query->where('tipo_pago', self::PAGO_PREPAGO);
    }

    // Métodos de verificación
    public function esEmpresarial()
    {
        return $this->tipo_plan === self::TIPO_EMPRESARIAL;
    }

    public function esPrepago()
    {
        return $this->tipo_pago === self::PAGO_PREPAGO;
    }

    public function esPersonal()
    {
        return $this->tipo_plan === self::TIPO_PERSONAL;
    }

    public function esMensual()
    {
        return $this->tipo_pago === self::PAGO_MENSUAL;
    }
}
