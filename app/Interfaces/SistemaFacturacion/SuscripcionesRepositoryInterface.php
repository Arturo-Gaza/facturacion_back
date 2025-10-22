<?php

namespace App\Interfaces\SistemaFacturacion;

use App\Models\Solicitud;
use Illuminate\Http\Request;
interface SuscripcionesRepositoryInterface
{
    public function getAll();
    public function iniciar($id_user ,$id_plan);
}