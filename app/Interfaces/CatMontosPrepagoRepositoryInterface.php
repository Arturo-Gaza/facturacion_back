<?php

namespace App\Interfaces;

use App\Models\CatMontosPrepago;

interface CatMontosPrepagoRepositoryInterface
{
    public function getAll();
    public function getById($id): ?CatMontosPrepago;
     public function getByPlan($id);
    public function store(array $data): CatMontosPrepago;
    public function update(array $data, $id): ?CatMontosPrepago;
    public function activate($id): ?CatMontosPrepago;
    public function deactivate($id): ?CatMontosPrepago;
}