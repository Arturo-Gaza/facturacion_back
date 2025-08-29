<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatUnidadMedida extends Model
{
    use HasFactory;
    protected $table = 'cat_unidad_medidas';
    protected $fillable = ['clave_unidad_medida','descripcion_unidad_medida','habilitado'];

    public function Productos()
{
    return $this->belongsTo(CatProductos::class, 'id_unidad_medida');
}
}
