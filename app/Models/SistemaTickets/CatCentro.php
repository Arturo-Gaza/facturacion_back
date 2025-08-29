<?php

namespace App\Models\SistemaTickets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatCentro extends Model
{
    use HasFactory;
    protected $table = 'cat_centro';
    protected $primaryKey = 'id_centro';
    public $incrementing = true; 
    protected $keyType = 'int';
    protected $fillable = [
        'clave_centro',
        'descripcion_centro',
        'habilitado',
    ];
}
