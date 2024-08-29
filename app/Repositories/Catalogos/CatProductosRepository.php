<?php

namespace App\Repositories\Catalogos;

use App\Interfaces\Catalogos\CatProductosRepositoryInterface;
use App\Models\Catalogos\CatProductos;

class CatProductosRepository implements CatProductosRepositoryInterface
{
    public function getAll()
    {
        return CatProductos::all();
    }

    public function getByID($id): ?CatProductos
    {
        return CatProductos::where('id', $id)->first();
    }

    public function store(array $data)
    {
        return CatProductos::create($data);
    }

    public function update(array $data, $id)
    {
        return CatProductos::where('id', $id)->update($data);
    }

    public function getAllPersonalizado()
    {
        // $productos = CatProductos::select(
        //     'id',
        //     'id_cat_almacenes',
        //     'id_unidad_medida',
        //     'id_gpo_familia',
        //     'clave_producto',
        //     'email',
        //     'idRol',
        //     'habilitado',
        // )
        //     ->where('idRol', 2)->get();

        $productos = CatProductos::select(
            'cat_productos.id',
            'cat_productos.id_cat_almacenes',
            'cat_productos.id_unidad_medida',
            'cat_productos.id_gpo_familia',
            'cat_productos.clave_producto',
            'cat_productos.descripcion_producto_material',
            'cat_productos.habilitado',
            'cat_productos.habilitado',
            'cat_productos.habilitado AS asigHabilitado',
            'cat_almacenes.clave_almacen',
            'cat_unidad_medidas.clave_unidad_medida',
            'cat_gpo_familias.clave_gpo_familia'
        )
            ->join('cat_almacenes', 'cat_almacenes.id', '=', 'cat_productos.id_cat_almacenes')
            ->join('cat_unidad_medidas', 'cat_unidad_medidas.id', '=', 'cat_productos.id_unidad_medida')
            ->join('cat_gpo_familias', 'cat_gpo_familias.id', '=', 'cat_productos.id_gpo_familia')
            ->get();

        $data1 = array();
        foreach ($productos as $val) {
            $data1[] = $val;
        }

        return $data1;
    }
}
