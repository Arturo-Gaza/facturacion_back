<?php

namespace App\Interfaces\SistemaTickets;

interface TabObesrvacionesSolicitudRepositoryInterface
{
    public function getAll();
    public function getByID($id);
    public function getBySolicitudID($id);
    public function store(array $data);
    public function update(array $data, $id);
}
