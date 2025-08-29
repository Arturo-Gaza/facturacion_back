<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatGpoFamilia extends Model
{
    use HasFactory;
    protected $table = 'cat_gpo_familias';
    protected $fillable = [
        'clave_gpo_familia',
        'descripcion_gpo_familia',
        'descripcion_gpo_familia_2',
        'habilitado'
    ];

    public function Productos()
    {
        return $this->belongsTo(CatGpoFamilia::class,'id_gpo_familia');
    }
}
