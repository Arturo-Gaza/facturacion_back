<?php

namespace App\Interfaces;

use App\Models\CatPlanesPrepago;

interface CatPlanesPrepagoRepositoryInterface
{
    public function getAll();
    public function getById($id): ?CatPlanesPrepago;
    public function store(array $data): CatPlanesPrepago;
    public function update(array $data, $id): ?CatPlanesPrepago;
    public function activate($id): ?CatPlanesPrepago;
    public function deactivate($id): ?CatPlanesPrepago;
}