<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatProductos extends Model
{
    use HasFactory;
    protected $table = 'cat_productos';
    protected $fillable = ['id_cat_almacenes', 'clave_producto', 
                            'descripcion_producto_material', 'id_unidad_medida', 
                            'id_gpo_familia' ];
}


