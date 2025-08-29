<?php

namespace App\Interfaces;

interface TabDepartamentosCategoriasRepositoryInterface
{
    public function getAll();
    public function getByDep($id);
    public function store(array $data);
    public function delete($id_departamento, $id_categoria);
}
