<?php

namespace App\Models\SistemaTickets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabDepartamentosCategorias extends Model
{
    use HasFactory;
    public $timestamps = false;
      // ⬇️ Importante: indicamos que no hay incremento automático
    public $incrementing = false;

    // ⬇️ Laravel no soporta claves compuestas nativamente, así que la dejamos en null
    protected $primaryKey = null;
    protected $table = 'tab_departamentos_categorias';
    protected $fillable = [
        'id_departamento',
        'id_categoria',
    ];


    public function departamento(){
        return $this->belongsTo(CatDepartamentos::class,"id_departamento");
    }

    public function categoria()
    {
        return $this->belongsTo(CatCategorias::class, "id_categoria");
    }
}
