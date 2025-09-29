<?php

namespace App\Models;

use App\Models\Catalogos\CatRegimenesFiscales;
use App\Models\Catalogos\CatUsoCfdi;
use App\Models\CatUsoCfdi as ModelsCatUsoCfdi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DatosFiscal extends Model
{
    protected $table = 'datos_fiscales';

    public static $snakeAttributes = false;

    
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
        'idCIF',
        'lugar_emision',
        'fecha_emision',
        'fecha_ult_cambio_op',
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

    protected $hidden = ['created_at', 'updated_at'];

    // Relación muchos a muchos con regimenes fiscales
    public function regimenesFiscales()
    {
        return $this->hasMany(DatosFiscalRegimenFiscal::class, 'id_dato_fiscal');
    }
    
public function domicilioFiscal()
{
    return $this->hasOne(Direccion::class, 'id_fiscal')
                ->where('id_tipo_direccion', 1); // 1 = domicilio fiscal
}
    

    
    // Obtener todos los usos CFDI a través de los regímenes (método de conveniencia)
    public function getUsosCfdiAttribute()
    {
        return ModelsCatUsoCfdi::whereHas('datosFiscalesRegimenes', function($query) {
            $query->whereIn('id_dato_fiscal_regimen', 
                $this->regimenesFiscales()->pluck('id')
            );
        })->get();
    }

    /**
     * Obtener el régimen fiscal predeterminado (método de conveniencia).
     */
    public function getRegimenPredeterminadoAttribute()
    {
        return $this->regimenPredeterminado->regimen ?? null;
    }

    /**
     * Obtener el uso CFDI predeterminado (método de conveniencia).
     */
    public function getUsoCfdiPredeterminadoAttribute()
    {
        return $this->usoCfdiPredeterminado ?? ($this->regimenPredeterminado->usoCfdiPredeterminado() ?? null);
    }

    public function direcciones(): HasMany
    {
        return $this->hasMany(Direccion::class, 'id_fiscal');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }

}