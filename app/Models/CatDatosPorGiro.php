<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatDatosPorGiro extends Model
{
    use HasFactory;

    protected $table = 'cat_datos_por_giro';

    protected $fillable = [
        'id_giro',
        'nombre_dato_giro',
        'label',
        'tipo',
        'requerido'
    ];

    protected $casts = [
        'requerido' => 'boolean',
    ];

    public function giro()
    {
        return $this->belongsTo(CatGiro::class, 'id_giro');
    }

    public function valoresSolicitud()
    {
        return $this->hasMany(SolicitudDatoGiro::class, 'id_dato_por_giro');
    }
}
