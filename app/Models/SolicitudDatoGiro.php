<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SolicitudDatoGiro extends Model
{
    use HasFactory;

    protected $table = 'solicitud_dato_giro';

    protected $fillable = [
        'id_solicitud',
        'id_dato_por_giro',
        'valor'
    ];

    public function dato()
    {
        return $this->belongsTo(CatDatosPorGiro::class, 'id_dato_por_giro');
    }

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud');
    }
}
