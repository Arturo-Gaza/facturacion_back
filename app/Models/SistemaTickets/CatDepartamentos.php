<?php

namespace App\Models\SistemaTickets;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatDepartamentos extends Model
{
    use HasFactory;
    protected $table = 'cat_departamentos';
    protected $fillable = [
        'descripcion',
        'habillitado',
        'id_usuario_responsable_compras'
    ];
    public function categorias()
    {
        return $this->belongsToMany(CatCategorias::class, 'tab_departamentos_categorias', 'id_departamento', 'id_categoria');
    }
    public function usuarioResponsable()
    {
        return $this->belongsTo(User::class, "id_usuario_responsable_compras");
    }




}
