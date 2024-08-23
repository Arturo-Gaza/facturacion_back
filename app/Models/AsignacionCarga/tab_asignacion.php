<?php

namespace App\Models\AsignacionCarga;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tab_asignacion extends Model
{
    use HasFactory;
    protected $table = 'tab_asignacions';
    protected $fillable = [
            'id_carga',
            'id_usuario',
            'conteo',
            'fecha_asignacion',
            'fecha_inicio_conteo',
            'fecha_fin_conteo',
            'id_estatus',
            'habilitado',
    ];
}
