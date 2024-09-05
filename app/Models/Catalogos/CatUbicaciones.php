<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatUbicaciones extends Model
{
    use HasFactory;
    protected $table = 'cat_ubicaciones';
    protected $fillable = ['clave_ubicacion','descripcion_ubicacion','habilitado'];
}
