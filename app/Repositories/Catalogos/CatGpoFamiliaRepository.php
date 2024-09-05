<?php

namespace App\Repositories\Catalogos;

use App\Interfaces\Catalogos\CatGpoFamiliaRepositoryInterface;
use App\Models\Catalogos\CatGpoFamilia;
use Illuminate\Support\Facades\DB;

class CatGpoFamiliaRepository implements CatGpoFamiliaRepositoryInterface
{

    public function getAll()
    {
        return CatGpoFamilia::all();
    }

    public function getAllPersonalizado($idCarga)
    {
        $productos = DB::table('tab_detalle_archivos')

            ->join('cat_gpo_familias', 'cat_gpo_familias.id', '=', 'tab_detalle_archivos.id_gpo_familia')
            ->where('tab_detalle_archivos.id_carga_cab', $idCarga)
            ->select(
                'cat_gpo_familias.id',
                'cat_gpo_familias.clave_gpo_familia'
            )
            ->groupBy('cat_gpo_familias.id')
            ->get();

        $data1 = array();
        foreach ($productos as $val) {
            $data1[] = $val;
        }

        return $data1;
    }

    public function getByID($id): ?CatGpoFamilia
    {
        return CatGpoFamilia::where('id', $id)->first();
    }

    public function store(array $data)
    {
        return CatGpoFamilia::create($data);
    }

    public function update(array $data, $id)
    {
        return CatGpoFamilia::where('id', $id)->update($data);
    }
}
