<?php
// app/Models/CatUsoCfdi.php
// app/Models/CatUsoCfdi.php

namespace App\Models;

use App\Models\Catalogos\CatRegimenesFiscales;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CatUsoCfdi extends Model
{
    protected $table = 'cat_usos_cfdi';
    protected $primaryKey = 'usoCFDI';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'usoCFDI',
        'descripcion',
        'aplica_persona_fisica',
        'aplica_persona_moral',
        'fecha_inicio_vigencia',
        'fecha_fin_vigencia'
    ];

    protected $casts = [
        'aplica_persona_fisica' => 'boolean',
        'aplica_persona_moral' => 'boolean',
        'fecha_inicio_vigencia' => 'date',
        'fecha_fin_vigencia' => 'date'
    ];

    public function regimenesFiscales(): BelongsToMany
    {
        return $this->belongsToMany(
            CatRegimenesFiscales::class,
            'regimen_uso_cfdi',
            'usoCFDI',          // Foreign key en la tabla pivot para usoCFDI
            'id_regimen'        // Foreign key en la tabla pivot para el régimen
        );
    }
}