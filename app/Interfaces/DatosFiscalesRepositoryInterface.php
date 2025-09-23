<?php

namespace App\Interfaces;

use App\Models\DatosFiscal;

interface DatosFiscalesRepositoryInterface
{
    public function getAll();
    public function getByID($id): ?DatosFiscal;
    public function store(array $data): DatosFiscal;
    public function storeConDomicilio(array $data,array $direccion );
     public function storeCompleto(array $data,array $direccion,array $regimenesData );
    public function update(array $data, $id): ?DatosFiscal;
}