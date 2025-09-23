<?php

namespace App\Interfaces\Catalogos;

use App\Models\Catalogos\CatRegimenesFiscales;

interface CatRegimenesFiscaslesRepositoryInterface
{
    public function getAll();
    public function getByID($id): ?CatRegimenesFiscales;
    public function getByMoralOFisica($esPersonaMoral);
    public function store(array $data): CatRegimenesFiscales;
    public function update(array $data, $id): ?CatRegimenesFiscales;
}
