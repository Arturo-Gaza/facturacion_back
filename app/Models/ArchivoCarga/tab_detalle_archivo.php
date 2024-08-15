<?php

namespace App\Models\ArchivoCarga;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tab_detalle_archivo extends Model
{
    use HasFactory;
    protected $table = 'tab_detalle_archivos';
    protected $fillable = [
        'id_carga_cab',
        'id_almacen',
        'id_cat_prod',
        'id_unid_med',
        'id_gpo_familia',
        'Libre_utilizacion',
        'En_control_calidad',
        'Bloqueado',
        'Valor_libre_util',
        'Valor_en_insp_cal',
        'Valor_stock_bloq',
        'Cantidad_total',
        'Importe_unitario',
        'Importe_total',
        'habilitado'
    ];
}
