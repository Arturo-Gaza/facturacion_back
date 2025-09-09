<?php

namespace App\Repositories\Catalogos;

use App\Interfaces\Catalogos\CatRegimenesFiscaslesRepositoryInterface;
use App\Models\Catalogos\CatRegimenesFiscales;
use LDAP\Result;

class CatRegimensFiscalesRepository implements CatRegimenesFiscaslesRepositoryInterface
{    public function getAll()
    {
        return CatRegimenesFiscales::all();
    }

    public function getByID($id): ?CatRegimenesFiscales
    {
        return CatRegimenesFiscales::where('id_regimen', $id)->first();
    }

    public function store(array $data): CatRegimenesFiscales
    {
        return CatRegimenesFiscales::create($data);
    }

    public function update(array $data, $id): ?CatRegimenesFiscales
    {
        $regimen = CatRegimenesFiscales::where('id_regimen', $id)->first();
        if ($regimen) {
            $regimen->update($data);
        }
        return $regimen;
    }
}
