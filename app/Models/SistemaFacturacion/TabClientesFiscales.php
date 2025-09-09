<?php

namespace App\Models\sistemaFacturacion;

use App\Models\Catalogos\CatEstatusesSat;
use App\Models\Catalogos\CatRegimenesFiscales;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabClientesFiscales extends Model
{
    use HasFactory;

    protected $table = 'clientes_fiscales';
    protected $primaryKey = 'id_fiscal';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_cliente',
        'nombre_razon',
        'nombre_comercial',
        'es_persona_moral',
        'rfc',
        'curp',
        'id_regimen',
        'fecha_inicio_op',
        'id_estatus_sat',
        'datos_extra',
    ];

    

    public function cliente()
    {
        return $this->belongsTo(TabClientes::class, 'id_cliente', 'id_cliente');
    }

    public function regimen()
    {
        return $this->belongsTo(CatRegimenesFiscales::class, 'id_regimen', 'id_regimen');
    }

    public function estatusSat()
    {
        return $this->belongsTo(CatEstatusesSat::class, 'id_estatus_sat', 'id_estatus_sat');
    }
}
