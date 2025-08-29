<?php

namespace App\Repositories;

use App\Interfaces\TabDepartamentosCategoriasRepositoryInterface;
use App\Models\SistemaTickets\TabDepartamentosCategorias;

class TabDepartamentosCategoriasRepository implements TabDepartamentosCategoriasRepositoryInterface
{
    public function getAll()
    {
        return TabDepartamentosCategorias::all();
    }
    public function getByDep($id)
    {
        return TabDepartamentosCategorias::where('id_departamento', $id)->get();
    }



    public function store(array $data)
    {
        return TabDepartamentosCategorias::create($data);
    }

    public function delete($id_departamento, $id_categoria)
    {
        return TabDepartamentosCategorias::where('id_departamento', $id_departamento)
            ->where('id_categoria', $id_categoria)
            ->delete();
    }
}
