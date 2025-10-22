<?php

namespace App\Repositories;

use App\Interfaces\CatMontosPrepagoRepositoryInterface;
use App\Models\CatMontosPrepago;
use Exception;
use Illuminate\Support\Facades\DB;

class CatMontosPrepagoRepository implements CatMontosPrepagoRepositoryInterface
{
    public function getAll()
    {
        return CatMontosPrepago::activos()
            ->ordenarPorCreditos()
            ->get();
    }

    public function getById($id): ?CatMontosPrepago
    {
        return CatMontosPrepago::find($id);
    }

    public function store(array $data): CatMontosPrepago
    {
        DB::beginTransaction();
        try {
            $plan = CatMontosPrepago::create($data);
            DB::commit();
            return $plan;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(array $data, $id): ?CatMontosPrepago
    {
        DB::beginTransaction();
        try {
            $plan = CatMontosPrepago::find($id);
            
            if ($plan) {
                $plan->update($data);
            }
            
            DB::commit();
            return $plan;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function activate($id): ?CatMontosPrepago
    {
        DB::beginTransaction();
        try {
            $plan = CatMontosPrepago::find($id);
            
            if ($plan) {
                $plan->activar();
            }
            
            DB::commit();
            return $plan;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deactivate($id): ?CatMontosPrepago
    {
        DB::beginTransaction();
        try {
            $plan = CatMontosPrepago::find($id);
            
            if ($plan) {
                $plan->desactivar();
            }
            
            DB::commit();
            return $plan;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}