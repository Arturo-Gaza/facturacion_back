<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Direccion extends Model
{
    protected $table = 'direcciones';
    
    protected $primaryKey = 'id_direccion';
    
    public $timestamps = true;
    
    protected $fillable = [
        'id_fiscal',
        'id_tipo_direccion',
        'calle',
        'num_exterior',
        'num_interior',
        'colonia',
        'localidad',
        'municipio',
        'estado',
        'codigo_postal',
        'pais'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relación con los datos fiscales
     */
    public function datosFiscales(): BelongsTo
    {
        return $this->belongsTo(DatosFiscal::class, 'id_fiscal');
    }
}