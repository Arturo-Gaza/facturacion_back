<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatEstatusesSat extends Model
{
    use HasFactory;

    protected $table = 'cat_estatuses_sat';
    protected $primaryKey = 'id_estatus_sat';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'clave',
        'descripcion',
        'habilitado'
    ];
}
