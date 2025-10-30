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
        'email_facturacion_id',
        'email_facturacion_text',
        'habilitado'
    ];

    protected $casts = [
        'es_persona_moral' => 'boolean',
        'datos_extra' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected $appends = ['predeterminado']; // Agregar el campo a la serialización

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
        public function domicilioPersonal()
    {
        return $this->hasOne(Direccion::class, 'id_fiscal')
            ->where('id_tipo_direccion', 2); // 1 = domicilio fiscal
    }

    public function getPredeterminadoAttribute()
    {
        // Consulta directa sin cargar la relación completa
        $usuario = User::find($this->id_usuario);

        if ($usuario) {
            return $this->id == $usuario->datos_fiscales_principal ||
                $this->id == $usuario->datos_fiscales_personal;
        }

        return false;
    }

    // Obtener todos los usos CFDI a través de los regímenes (método de conveniencia)
    public function getUsosCfdiAttribute()
    {
        return ModelsCatUsoCfdi::whereHas('datosFiscalesRegimenes', function ($query) {
            $query->whereIn(
                'id_dato_fiscal_regimen',
                $this->regimenesFiscales()->pluck('id')
            );
        })->get();
    }

    /**
     * Obtener el régimen fiscal predeterminado (método de conveniencia).
     */
    public function getRegimenPredeterminadoAttribute()
    {
        // Buscar el régimen marcado como predeterminado en datos_fiscales_regimenes_fiscales
        $regimenFiscal = $this->regimenesFiscales()
                            ->where('predeterminado', true)
                            ->with('regimen') // Cargar la relación con cat_regimenes_fiscales
                            ->first();
        
        return $regimenFiscal->regimen ?? null;
    }

    /**
     * Obtener el uso CFDI predeterminado (método de conveniencia).
     */
    public function getUsoCfdiPredeterminadoAttribute()
    {
        // Primero buscar el régimen predeterminado
        $regimenPredeterminado = $this->regimenesFiscales()
                                    ->where('predeterminado', true)
                                    ->first();

        if ($regimenPredeterminado) {
            // Buscar el uso CFDI predeterminado en datos_fiscales_regimen_usos_cfdi
            $usoCfdi = $regimenPredeterminado->usosCfdi()
                                            ->where('predeterminado', true)
                                            ->with('usoCfdi') // Cargar la relación con cat_usos_cfdi
                                            ->first();
            
            return $usoCfdi->usoCfdi ?? null;
        }

        return null;
    }

    public function direcciones(): HasMany
    {
        return $this->hasMany(Direccion::class, 'id_fiscal');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }
    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'id_receptor');
    }
}
