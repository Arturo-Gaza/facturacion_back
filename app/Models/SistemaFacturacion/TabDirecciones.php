<?php

namespace App\Models\sistemaFacturacion;

use App\Models\Catalogos\CatTiposDireccion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabDirecciones extends Model
{
    use HasFactory;

     protected $table = 'direcciones';
    protected $primaryKey = 'id_direccion';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_cliente',
        'id_tipo_direccion',
        'calle',
        'num_exterior',
        'num_interior',
        'colonia',
        'localidad',
        'municipio',
        'estado',
        'codigo_postal',
        'pais'
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(TabClientes::class, 'id_cliente', 'id_cliente');
    }

    public function tipoDireccion()
    {
        return $this->belongsTo(CatTiposDireccion::class, 'id_tipo_direccion', 'id_tipo_direccion');
    }
}
