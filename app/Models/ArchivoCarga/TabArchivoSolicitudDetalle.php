<?php

namespace App\Models\ArchivoCarga;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabArchivoSolicitudDetalle extends Model
{
    use HasFactory;
    protected $table = 'tab_archivos_solicitud_detalle';
    protected $fillable = [
        'id_solicitud_detalle',
        'nombre',
        'archivo',
        'habilitado'
    ];

protected $hidden = ['archivo'];

}
