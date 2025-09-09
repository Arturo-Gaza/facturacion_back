<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'aplica_pf',
        'aplica_pm',
        'habilitado'
    ];
}
