<?php

namespace App\Models\ArchivoCarga;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabObservaciones extends Model
{
    use HasFactory;
    protected $table = 'tab_observaciones';
    protected $fillable = [
        'id_detalle_carga',
        'id_usuario',
        'observacion',
        'habilitado'
    ];
}
