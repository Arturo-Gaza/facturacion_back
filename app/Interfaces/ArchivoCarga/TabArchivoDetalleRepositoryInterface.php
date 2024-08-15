<?php

namespace App\Interfaces\ArchivoCarga;

interface TabArchivoDetalleRepositoryInterface
{
    public function getAll();
    public function getByID($id);
    public function store(array $data);
    public function update(array $data, $id);
}
