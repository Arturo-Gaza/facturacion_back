<?php
// app/Models/Catalogos/CatRegimenesFiscales.php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\CatUsoCfdi;

class CatRegimenesFiscales extends Model
{
    use HasFactory;

    protected $table = 'cat_regimenes_fiscales';
    protected $primaryKey = 'id_regimen';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'clave',
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

    public function usosCfdi(): BelongsToMany
    {
        return $this->belongsToMany(
            CatUsoCfdi::class,
            'regimen_uso_cfdi',
            'id_regimen',       // Foreign key en la tabla pivot para el régimen
            'usoCFDI'           // Foreign key en la tabla pivot para usoCFDI
        );
    }
    
}