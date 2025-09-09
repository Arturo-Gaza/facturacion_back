<?php

namespace App\Repositories\Catalogos;

use App\Interfaces\Catalogos\CatTiposContactosRepositoryInterface;
use App\Models\Catalogos\CatTiposContacto;

class CatTiposContactosRepository implements CatTiposContactosRepositoryInterface
{
    public function getAll()
    {
        return CatTiposContacto::all();
    }

    public function getByID($id): ?CatTiposContacto
    {
        return CatTiposContacto::where('id_tipo_contacto', $id)->first();
    }

    public function store(array $data): CatTiposContacto
    {
        return CatTiposContacto::create($data);
    }

    public function update(array $data, $id): ?CatTiposContacto
    {
        $tipo = CatTiposContacto::where('id_tipo_contacto', $id)->first();
        if ($tipo) {
            $tipo->update($data);
        }
        return $tipo;
    }
}
