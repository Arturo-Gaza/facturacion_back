<?php

namespace App\Interfaces\SistemaTickets;

interface CatDepartamentosRepositoryInterface
{
    public function getAll();
    public function getByID($id);
    public function store(array $data);
    public function update(array $data, $id);
    public function exportar($data);
}
