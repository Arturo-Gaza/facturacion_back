<?php

namespace App\Models\sistemaFacturacion;

use App\Models\Catalogos\CatTiposContacto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabContactos extends Model
{
    use HasFactory;

    protected $table =  'contactos';
    protected $primaryKey = 'id_contacto';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_cliente',
        'id_tipo_contacto',
        'lada',
        'valor',
        'principal'
    ];

    public function cliente()
    {
        return $this->belongsTo(TabClientes::class, 'id_cliente', 'id_cliente');
    }

    public function tipoContacto()
    {
        return $this->belongsTo(CatTiposContacto::class, 'id_tipo_contacto', 'id_tipo_contacto');
    }
}
