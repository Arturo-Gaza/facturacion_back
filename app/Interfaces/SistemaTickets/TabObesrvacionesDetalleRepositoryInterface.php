<?php

namespace App\Interfaces\SistemaTickets;

interface TabObesrvacionesDetalleRepositoryInterface
{
    public function getAll();
    public function getByID($id);
    public function getByIdDetalle($id);
    public function store(array $data);
    public function update(array $data, $id);
}
