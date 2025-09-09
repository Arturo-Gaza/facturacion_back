<?php

namespace App\Interfaces\Catalogos;

use App\Models\Catalogos\CatEstatusesSat;

interface CatEstatusSatRepositoryInterface
{
    public function getAll();
    public function getByID($id): ?CatEstatusesSat;
    public function store(array $data): CatEstatusesSat;
    public function update(array $data, $id): ?CatEstatusesSat;
}
