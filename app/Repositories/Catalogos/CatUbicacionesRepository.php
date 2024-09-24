<?php

namespace App\Repositories\Catalogos;

use App\Interfaces\Catalogos\CatUbicacionesRepositoryInterface;
use App\Models\Catalogos\CatUbicaciones;

class CatUbicacionesRepository implements CatUbicacionesRepositoryInterface
{
    public function getAll()
    {
        return CatUbicaciones::all();
    }

    public function getAllPaginate($data)
    {
        // Construir la consulta
        $query = CatUbicaciones::query();
        $query->whereRaw('LOWER(clave_ubicacion) LIKE ?', ['%' . strtolower($data->clave_ubicacion) . '%'])
        ->orderBy('clave_ubicacion');

        // Paginar los resultados
        return $query->paginate($data->pageSize);
    }

    public function getByID($id): ?CatUbicaciones
    {
        return CatUbicaciones::where('id', $id)->first();
    }

    public function store(array $data)
    {
        return CatUbicaciones::create($data);
    }

    public function update(array $data, $id)
    {
        return CatUbicaciones::where('id',$id)->update($data);
    }
}
