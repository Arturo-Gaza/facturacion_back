<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DatosFiscalRegimenUsoCfdi extends Model
{
    protected $table = 'datos_fiscales_regimen_usos_cfdi';
    
    protected $fillable = [
        'id_dato_fiscal_regimen',
        'uso_cfdi',
        'predeterminado'
    ];
    protected $hidden = ['created_at', 'updated_at'];

    
    public function datoFiscalRegimen(): BelongsTo
    {
        return $this->belongsTo(DatosFiscalRegimenFiscal::class, 'id_dato_fiscal_regimen');
    }
    
    public function usoCfdi(): BelongsTo
    {
        return $this->belongsTo(CatUsoCfdi::class, 'uso_cfdi', 'usoCFDI');
    }


}