<?php

namespace App\Models\Catalogos;

use App\Models\SistemaTickets\CatCategorias;
use App\Models\SistemaTickets\CatCentro;
use App\Models\SistemaTickets\CatMoneda;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatProductos extends Model
{
    use HasFactory;
    protected $table = 'cat_productos';
    protected $fillable = [
        'clave_producto',
        'descripcion_producto',
        'id_unidad_medida',
        'id_moneda',
        'id_gpo_familia',
        'id_categoria',
        'habilitado'
    ];


    public function unidadMedida()
    {
        return $this->belongsTo(CatUnidadMedida::class, 'id_unidad_medida');
    }

    public function grupoFamilia()
    {
        return $this->belongsTo(CatGpoFamilia::class, 'id_gpo_familia');
    }

    public function moneda()
    {
        return $this->belongsTo(CatMoneda::class, 'id_moneda');
    }

    public function categoria()
    {
        return $this->belongsTo(CatCategorias::class, 'id_categoria');
    }

    protected $appends = [
    'id_tipo'
];

  public function getDescripcionUnidadMedidaAttribute()
{
    return optional($this->unidadMedida)->descripcion_unidad_medida ?? null;
}
  public function getDescripcionGpoFamiliaAttribute()
{
    return optional($this->grupoFamilia)->descripcion_gpo_familia ?? null;
}

 public function getDescripcionMonedaAttribute()
{
    return optional($this->moneda)->descripcion_moneda ?? null;
}


    public function getDescripcionCategoriaAttribute()
{
    return optional($this->categoria)->descripcion_categoria ?? null;
}
    public function getIdTipoAttribute()
{
    return optional($this->categoria)->id_tipo ?? null;
}
}
