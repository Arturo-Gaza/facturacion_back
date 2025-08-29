<?php

namespace App\Interfaces\SistemaTickets;

interface CatCentroRepositoryInterface
{
    public function getAll();
    public function getByID($id);
    public function store(array $data);
    public function update(array $data, $id);
}
