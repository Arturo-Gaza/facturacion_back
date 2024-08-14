<?php

namespace App\Repositories\ArchivoCarga;

use App\Interfaces\ArchivoCarga\TabArchivoCargaRepositoryInterface;
use App\Models\ArchivoCarga\tab_detalle_carga;

class TabDetalleCargasRerpository implements TabArchivoCargaRepositoryInterface
{
    public function getAll()
    {
        return tab_detalle_carga::all();
    }

    public function getByID($id): ?tab_detalle_carga
    {
        return tab_detalle_carga::where('id', $id)->first();
    }

    public function store(array $data)
    {
        return tab_detalle_carga::create($data);
    }

    public function update(array $data, $id)
    {
        return tab_detalle_carga::where('id',$id)->update($data);
    }
}
