<?php

namespace App\Repositories\ArchivoCarga;

use App\Interfaces\ArchivoCarga\TabArchivoDetalleRepositoryInterface;
use App\Models\ArchivoCarga\tab_detalle_archivo;

class TabArchivoDetalleRepository implements TabArchivoDetalleRepositoryInterface
{
    public function getAll()
    {
        return tab_detalle_archivo::all();
    }

    public function getByID($id): ?tab_detalle_archivo
    {
        return tab_detalle_archivo::where('id', $id)->first();
    }

    public function store(array $data)
    {
        return tab_detalle_archivo::create($data);
    }

    public function update(array $data, $id)
    {
        return tab_detalle_archivo::where('id', $id)->update($data);
    }
}
