<?php

namespace App\Interfaces\ArchivoConteo;

interface TabConteoRepositoryInterface
{
    public function getAll();
    public function getByID($id);
    public function store(array $data);
    public function update(array $data, $id);
    public function getByIDCargaIDUser($idCarga, $idUser);
    public function storeConteoDup($data);
}
