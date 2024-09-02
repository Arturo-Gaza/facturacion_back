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
    public function storeConteoDup( $data)
    {
        TabConteo::insert($data->toArray());

    }

    public function getByIDCargaIDUser($idCarga, $idUser)
    {
        $conteo = TabConteo::select(
            'tab_conteo.id',
            'tab_conteo.id_carga',
            'tab_conteo.id_usuario',
            'tab_conteo.id_almacen',
            'tab_conteo.id_unidadmedida',
            'tab_conteo.id_grupofamilia',
            'tab_conteo.id_producto',
            'tab_conteo.codigo',
            'tab_conteo.descripcion',
            'tab_conteo.ume',
            'tab_conteo.cantidad',
            'tab_conteo.ubicacion',
            'tab_conteo.observaciones',
            'tab_conteo.habilitado',
            'tab_conteo.conteo',
            'tab_conteo.created_at',
            'tab_conteo.updated_at',
            'cat_almacenes.clave_almacen',
            'cat_unidad_medidas.clave_unidad_medida',
            'cat_gpo_familias.clave_gpo_familia'
        )
            ->join('cat_almacenes', 'cat_almacenes.id', '=', 'tab_conteo.id_almacen')
            ->join('cat_unidad_medidas', 'cat_unidad_medidas.id', '=', 'tab_conteo.id_unidadmedida')
            ->join('cat_gpo_familias', 'cat_gpo_familias.id', '=', 'tab_conteo.id_grupofamilia')
            ->where('tab_conteo.id_carga', $idCarga)
            ->where('tab_conteo.id_usuario', $idUser)
            ->get();

        $data1 = array();
        foreach ($conteo as $val) {
            $data1[] = $val;
        }

        return $data1;
    }

    public function getByIDCarga($idCarga)
    {
        $conteo = TabConteo::select(
            'tab_conteo.id',
            'tab_conteo.id_carga',
            'tab_conteo.id_usuario',
            'tab_conteo.id_almacen',
            'tab_conteo.id_unidadmedida',
            'tab_conteo.id_grupofamilia',
            'tab_conteo.id_producto',
            'tab_conteo.codigo',
            'tab_conteo.descripcion',
            'tab_conteo.ume',
            'tab_conteo.cantidad',
            'tab_conteo.ubicacion',
            'tab_conteo.observaciones',
            'tab_conteo.habilitado',
            'tab_conteo.conteo',
            'tab_conteo.created_at',
            'tab_conteo.updated_at',
            'cat_almacenes.clave_almacen',
            'cat_unidad_medidas.clave_unidad_medida',
            'cat_gpo_familias.clave_gpo_familia',
            'users.user',
            'users.name',
            'users.apellidoP',
            'users.apellidoM',
        )
            ->join('cat_almacenes', 'cat_almacenes.id', '=', 'tab_conteo.id_almacen')
            ->join('cat_unidad_medidas', 'cat_unidad_medidas.id', '=', 'tab_conteo.id_unidadmedida')
            ->join('cat_gpo_familias', 'cat_gpo_familias.id', '=', 'tab_conteo.id_grupofamilia')
            ->join('users', 'users.id', '=', 'tab_conteo.id_usuario')
            ->where('tab_conteo.id_carga', $idCarga)
            ->get();

        $data1 = array();
        foreach ($conteo as $val) {
            $data1[] = $val;
        }

        return $data1;
    }



}
