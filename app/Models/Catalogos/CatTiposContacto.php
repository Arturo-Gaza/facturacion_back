<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatTiposContacto extends Model
{
    use HasFactory;

    protected $table = 'cat_tipos_contacto';
    protected $primaryKey = 'id_tipo_contacto';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'clave',
        'descripcion',
        'habilitado'
    ];
}
