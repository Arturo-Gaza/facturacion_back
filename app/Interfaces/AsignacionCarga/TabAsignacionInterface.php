<?php

namespace App\Interfaces\AsignacionCarga;

interface TabAsignacionInterface
{
    public function getAll();
    public function getByID($id);
    public function store(array $data);
    public function update(array $data, $id);
    public function getByIdCargaIdUser($idCarga, $idUser);
    public function getByIdCargaIdUserPer($idCarga, $idUser);
}
