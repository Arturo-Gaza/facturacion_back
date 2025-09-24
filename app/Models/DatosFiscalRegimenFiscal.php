<?php

namespace App\Models;

use App\Models\Catalogos\CatRegimenesFiscales;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DatosFiscalRegimenFiscal extends Model
{
    protected $table = 'datos_fiscales_regimenes_fiscales';
    
    protected $fillable = [
        'id_dato_fiscal',
        'id_regimen',
        'predeterminado'
    ];
    
    public function datoFiscal(): BelongsTo
    {
        return $this->belongsTo(DatosFiscal::class, 'id_dato_fiscal');
    }
    
    public function regimen(): BelongsTo
    {
        return $this->belongsTo(CatRegimenesFiscales::class, 'id_regimen');
    }
    
    public function usosCfdi(): HasMany
    {
        return $this->hasMany(DatosFiscalRegimenUsoCfdi::class, 'id_dato_fiscal_regimen');
    }
    
    // Obtener el uso CFDI predeterminado
    public function usoCfdiPredeterminado()
    {
        return $this->usosCfdi()->where('predeterminado', true)->first();
    }
}