<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatMotivoRechazo extends Model
{
       protected $fillable = [
        'descripcion',
        'activo',
        'validar_por_IA',
        'detalle'
    ];
}
