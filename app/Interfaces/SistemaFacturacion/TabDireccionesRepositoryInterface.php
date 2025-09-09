<?php

namespace App\Interfaces\SistemaFacturacion;

use App\Models\sistemaFacturacion\TabDirecciones;

interface TabDireccionesRepositoryInterface
{
    public function getAll();
    public function getByID($id): ?TabDirecciones;
    public function store(array $data): TabDirecciones;
    public function update(array $data, $id): ?TabDirecciones;
}
