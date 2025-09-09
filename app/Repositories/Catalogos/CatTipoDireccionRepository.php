<?php

namespace App\Repositories\Catalogos;

use App\Interfaces\Catalogos\CatTipoDireccionRepositoryInterface;
use App\Models\Catalogos\CatTiposDireccion;

class CatTipoDireccionRepository implements CatTipoDireccionRepositoryInterface
{
    public function getAll()
    {
        return CatTiposDireccion::all();
    }

    public function getByID($id): ?CatTiposDireccion
    {
        return CatTiposDireccion::where('id_tipo_direccion', $id)->first();
    }

    public function store(array $data): CatTiposDireccion
    {
        return CatTiposDireccion::create($data);
    }

    public function update(array $data, $id): ?CatTiposDireccion
    {
        $tipo = CatTiposDireccion::where('id_tipo_direccion', $id)->first();
        if ($tipo) {
            $tipo->update($data);
        }
        return $tipo;
    }
}
