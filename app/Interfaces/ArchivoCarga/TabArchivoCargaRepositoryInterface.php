<?php

namespace App\Interfaces\ArchivoCarga;

interface TabArchivoCargaRepositoryInterface
{
    public function getAll();
    public function getByID($id);
    public function store(array $data);
    public function update(array $data, $id);
}
