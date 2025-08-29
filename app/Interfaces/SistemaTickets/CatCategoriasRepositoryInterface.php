<?php

namespace App\Interfaces\SistemaTickets;

interface CatCategoriasRepositoryInterface
{
    public function getAll();
    public function getByID($id);
    public function getByDpto($id);
    public function store(array $data);
    public function update(array $data, $id);
    public function exportar($filtro);
    public function getByIdCat($id);
}
