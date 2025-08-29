<?php

namespace App\Models\SistemaTickets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatEstatusSolicitud extends Model
{
    use HasFactory;
     protected $table = 'cat_estatus_solicitud';
     protected $fillable = [
         'descripcion_estatus_solicitud',
         'mandarCorreo'
     ];
}
