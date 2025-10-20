<?php

namespace App\Repositories;

use App\Interfaces\CatPlanesPrepagoRepositoryInterface;
use App\Models\CatPlanesPrepago;
use Exception;
use Illuminate\Support\Facades\DB;

class CatPlanesPrepagoRepository implements CatPlanesPrepagoRepositoryInterface
{
    public function getAll()
    {
        return CatPlanesPrepago::activos()
            ->ordenarPorCreditos()
            ->get();
    }

    public function getById($id): ?CatPlanesPrepago
    {
        return CatPlanesPrepago::find($id);
    }

    public function store(array $data): CatPlanesPrepago
    {
        DB::beginTransaction();
        try {
            $plan = CatPlanesPrepago::create($data);
            DB::commit();
            return $plan;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(array $data, $id): ?CatPlanesPrepago
    {
        DB::beginTransaction();
        try {
            $plan = CatPlanesPrepago::find($id);
            
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

    public function activate($id): ?CatPlanesPrepago
    {
        DB::beginTransaction();
        try {
            $plan = CatPlanesPrepago::find($id);
            
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

    public function deactivate($id): ?CatPlanesPrepago
    {
        DB::beginTransaction();
        try {
            $plan = CatPlanesPrepago::find($id);
            
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