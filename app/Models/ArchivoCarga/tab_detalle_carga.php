<?php

namespace App\Models\ArchivoCarga;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tab_detalle_carga extends Model
{
    use HasFactory;
    protected $table = 'tab_detalle_cargas';
    protected $fillable = [
        'cve_carga',
        'fecha_asignacion',
        'fecha_inicio_conteo',
        'fecha_fin_conteo',
        'conteo',
        'nombre_archivo',
        'id_usuario',
        'Reg_Archivo',
        'Reg_a_Contar',
        'reg_vobo',
        'reg_excluidos',
        'reg_incorpora',
        'id_estatus',
        'observaciones',
        'habilitado'
    ];
}
