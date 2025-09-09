<?php

namespace App\Models\sistemaFacturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabClientes extends Model
{
    use HasFactory;

    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
            'usuario',
            'password',
            'email',
            'habilitado'
    ];
}
