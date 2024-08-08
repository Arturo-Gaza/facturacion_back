<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatGpoFamilia extends Model
{
    use HasFactory;
    protected $table = 'cat_gpo_familias';
    protected $fillable = ['clave_gpo_familia','descripcion_gpo_familia','habilitado'];
}
