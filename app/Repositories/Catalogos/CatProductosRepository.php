<?php

namespace App\Repositories\Catalogos;

use App\Interfaces\Catalogos\CatProductosRepositoryInterface;
use App\Models\Catalogos\CatProductos;
use Illuminate\Support\Facades\DB;

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

    public function getAllPersonalizado($idCarga)
    {

        // $productos = CatProductos::select(
        //     'cat_productos.id',
        //     'cat_productos.id_cat_almacenes',
        //     'cat_productos.id_unidad_medida',
        //     'cat_productos.id_gpo_familia',
        //     'cat_productos.clave_producto',
        //     'cat_productos.descripcion_producto_material',
        //     'cat_productos.habilitado',
        //     'cat_productos.habilitado',
        //     'cat_productos.habilitado AS asigHabilitado',
        //     'cat_almacenes.clave_almacen',
        //     'cat_unidad_medidas.clave_unidad_medida',
        //     'cat_gpo_familias.clave_gpo_familia'
        // )
        //     ->join('cat_almacenes', 'cat_almacenes.id', '=', 'cat_productos.id_cat_almacenes')
        //     ->join('cat_unidad_medidas', 'cat_unidad_medidas.id', '=', 'cat_productos.id_unidad_medida')
        //     ->join('cat_gpo_familias', 'cat_gpo_familias.id', '=', 'cat_productos.id_gpo_familia')
        //     ->get();

        $productos = DB::table('tab_detalle_archivos')
            ->join('cat_productos', 'cat_productos.id', '=', 'tab_detalle_archivos.id_cat_prod')
            ->join('cat_almacenes', 'cat_almacenes.id', '=', 'tab_detalle_archivos.id_almacen')
            ->join('cat_unidad_medidas', 'cat_unidad_medidas.id', '=', 'tab_detalle_archivos.id_unid_med')
            ->join('cat_gpo_familias', 'cat_gpo_familias.id', '=', 'tab_detalle_archivos.id_gpo_familia')
            ->where('tab_detalle_archivos.id_carga_cab', $idCarga)
            ->select(
                'tab_detalle_archivos.id_cat_prod as id',
                'tab_detalle_archivos.id_almacen as id_cat_almacenes',
                'tab_detalle_archivos.id_unid_med as id_unidad_medida',
                'tab_detalle_archivos.id_gpo_familia as id_gpo_familia',
                'cat_productos.clave_producto',
                'cat_productos.descripcion_producto_material',
                'cat_productos.habilitado AS asigHabilitado',
                'cat_almacenes.clave_almacen',
                'cat_unidad_medidas.clave_unidad_medida',
                'cat_gpo_familias.clave_gpo_familia'
            )
            ->groupBy('tab_detalle_archivos.id_cat_prod')
            ->groupBy('tab_detalle_archivos.id_almacen')
            ->groupBy('tab_detalle_archivos.id_unid_med')
            ->groupBy('tab_detalle_archivos.id_gpo_familia')
            ->groupBy('cat_productos.clave_producto')
            ->groupBy('cat_productos.descripcion_producto_material')
            ->groupBy('cat_productos.habilitado')
            ->groupBy('cat_almacenes.clave_almacen')
            ->groupBy('cat_unidad_medidas.clave_unidad_medida')
            ->groupBy('cat_gpo_familias.clave_gpo_familia')
            ->get();

        $data1 = array();
        foreach ($productos as $val) {
            $data1[] = $val;
        }

        return $data1;
    }
}
