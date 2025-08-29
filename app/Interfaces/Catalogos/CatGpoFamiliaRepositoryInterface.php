<?php

namespace App\Interfaces\Catalogos;

interface CatGpoFamiliaRepositoryInterface
{
    public function getAll();
    public function getByID($id);
    public function store(array $data);
    public function search( $data);
    public function exportar( $data);
    public function update(array $data, $id);
    public function getAllPersonalizado($idCarga);
}
