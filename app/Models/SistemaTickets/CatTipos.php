<?php

namespace App\Models\SistemaTickets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatTipos extends Model
{
    use HasFactory;
    protected $table = 'cat_tipos';
    protected $fillable = [
        'descripcion',
        'req_marca_modelo',
        'habilitado'
    ];

    public function Categorias()
    {
        return $this->belongsTo(CatCategorias::class, 'id_tipo');
    }
}
