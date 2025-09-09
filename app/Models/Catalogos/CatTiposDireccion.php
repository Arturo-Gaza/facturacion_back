<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatTiposDireccion extends Model
{
    use HasFactory;

    protected $table = 'cat_tipos_direccion';
    protected $primaryKey = 'id_tipo_direccion';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'clave',
        'descripcion',
        'habilitado'
    ];
}
