<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatAlmacenes extends Model
{
    use HasFactory;
    protected $table = 'cat_almacenes';
    protected $fillable = ['clave_almacen','descripcion_almacen','habilitado'];
}
