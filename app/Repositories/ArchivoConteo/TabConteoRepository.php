<?php

namespace App\Repositories\ArchivoConteo;

use App\Interfaces\ArchivoConteo\TabConteoRepositoryInterface;
use App\Models\ArchivoConteo\TabConteo;

class TabConteoRepository implements TabConteoRepositoryInterface
{
    public function getAll()
    {
        return TabConteo::all();
    }

    public function getByID($id)
    {
        return TabConteo::findOrFail($id);
    }

    public function store(array $data)
    {
        return TabConteo::create($data);
    }

    public function update(array $data, $id)
    {
        $tabConteo = TabConteo::findOrFail($id);
        $tabConteo->update($data);
        return $tabConteo;
    }
}
