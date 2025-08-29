<?php

namespace App\Models\SistemaTickets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatCategorias extends Model
{
    use HasFactory;
    protected $table = 'cat_categorias';
    protected $fillable = [
        'descripcion_categoria',
        'id_tipo',
        'habilitado'
    ];
    public function departamentos()
    {
        return $this->belongsToMany(CatDepartamentos::class, 'tab_departamentos_categorias', 'id_categoria', 'id_departamento');
    }

    public function tipo()
    {
        return $this->belongsTo(CatTipos::class, "id_tipo");
    }
    protected $appends = ['descripcion_tipo'];


    public function getDescripcionTipoAttribute()
    {
        return optional($this->tipo)->descripcion ?? null;
    }
    public function producto()
    {
        return $this->belongsTo(CatCategorias::class, "id_categoria");
    }
}
