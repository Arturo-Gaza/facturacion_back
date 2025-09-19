<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DatosFiscal extends Model
{
    protected $table = 'datos_fiscales';
    
    protected $primaryKey = 'id';
    
    public $timestamps = true;
    
    protected $fillable = [
        'id_usuario',
        'nombre_razon',
        'primer_apellido',
        'segundo_apellido',
        'nombre_comercial',
        'es_persona_moral',
        'rfc',
        'curp',
        'id_regimen',
        'fecha_inicio_op',
        'id_estatus_sat',
        'datos_extra',
        'email_facturacion_id'
    ];

    protected $casts = [
        'es_persona_moral' => 'boolean',
        'fecha_inicio_op' => 'date',
        'datos_extra' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relación con las direcciones
     */
    public function direcciones(): HasMany
    {
        return $this->hasMany(Direccion::class, 'id_fiscal');
    }
}