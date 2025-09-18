<?php

namespace App\Interfaces\SistemaFacturacion;

use App\Models\Solicitud;
use Illuminate\Http\Request;
interface SolicitudRepositoryInterface
{
    public function getAll();
    public function getByID($id): ?Solicitud;
    public function store(Request $data): Solicitud;
    public function update(array $data, $id): ?Solicitud;
}