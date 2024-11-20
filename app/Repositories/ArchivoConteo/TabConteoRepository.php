<?php

namespace App\Repositories\ArchivoConteo;

use App\Interfaces\ArchivoConteo\TabConteoRepositoryInterface;
use App\Models\ArchivoConteo\TabConteo;
use Illuminate\Support\Facades\DB;

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
    public function storeConteoDup($data)
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
            'tab_conteo.id_ubicacion',
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
            'tab_conteo.id_ubicacion',
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

    public function reporteGeneral($idCarga, $conteo)
    {
        $products = DB::table('tab_conteo')
            ->join('users', 'tab_conteo.id_usuario', '=', 'users.id')
            ->join('cat_productos', 'tab_conteo.id_producto', '=', 'cat_productos.id')
            ->select(
                'tab_conteo.id_producto',
                'cat_productos.clave_producto',
                'cat_productos.descripcion_producto_material',
                'tab_conteo.id_grupofamilia',
                'tab_conteo.ubicacion',
                'tab_conteo.id_usuario',
                'users.name as usuario_nombre',
                DB::raw('tab_conteo.cantidad::NUMERIC(10, 3) as cantidad'),
                'tab_conteo.observaciones'
            )
            ->where('tab_conteo.id_carga', $idCarga)  // Condición WHERE
            ->where('tab_conteo.conteo', $conteo)     // Condición WHERE
            ->get();
            $products->transform(function ($item) {
                $item->cantidad = (float) $item->cantidad;  // Convertir a número
                return $item;
            });

        return response()->json($products);
    }

    public function reporteDiferencias($idCarga, $conteo)
    {
        $sapTotals = DB::table('tab_detalle_archivos')
            ->select(
                'id_cat_prod',
                DB::raw('CAST(cantidad_total AS DECIMAL(12, 3)) AS total_sap'),
                DB::raw('CAST(importe_unitario AS DECIMAL(12, 3)) AS importe_unitario'),
                DB::raw('CAST(importe_total AS DECIMAL(12, 3)) AS importe_total')
            )->where('id_carga_cab',$idCarga)
            ->groupBy('id_cat_prod', 'importe_unitario', 'importe_total', 'cantidad_total');

        $fisicoTotals = DB::table('tab_conteo')
            ->select(
                'id_producto',
                DB::raw('SUM(cantidad) AS total_fisico')
            )
            ->where('id_carga', $idCarga)
            ->where('conteo', $conteo)
            ->groupBy('id_producto');

        $query = DB::table('tab_conteo as a')
            ->join('cat_gpo_familias as b', 'a.id_grupofamilia', '=', 'b.id')
            ->joinSub($sapTotals, 'sap', function ($join) {
                $join->on('a.id_producto', '=', 'sap.id_cat_prod');
            })
            ->joinSub($fisicoTotals, 'fisico', function ($join) {
                $join->on('a.id_producto', '=', 'fisico.id_producto');
            })
            ->select(
                DB::raw('DISTINCT ON (a.id_producto) a.id_producto'),
                'a.codigo',
                'a.descripcion',
                'a.ume',
                'b.clave_gpo_familia',
                DB::raw('CAST(sap.total_sap AS DECIMAL(12, 3)) AS "SAP"'),
                DB::raw('CAST(fisico.total_fisico AS DECIMAL(12, 3)) AS "Fisico"'),
                DB::raw('CAST((fisico.total_fisico - sap.total_sap) AS DECIMAL(12, 3)) AS "DiferenciaCantidad"'),
                DB::raw('CAST(sap.importe_unitario AS DECIMAL(12, 2)) AS "importe_unitario"'),
                DB::raw('CAST((sap.importe_unitario * (fisico.total_fisico - sap.total_sap)) AS DECIMAL(12, 2)) AS "DiferenciaMoneda"'),
                DB::raw('CAST(sap.importe_total AS DECIMAL(12,2)) AS "ImporteTotal"')
            )
            ->where('a.id_carga', $idCarga)
            ->where('conteo', $conteo)
            ->groupBy(
                'a.id_producto',
                'a.codigo',
                'a.descripcion',
                'a.ume',
                'b.clave_gpo_familia',
                'sap.total_sap',
                'fisico.total_fisico',
                'sap.importe_unitario',
                'sap.importe_total'
            )->get();
            return response()->json($query);
    }

    public function getConteoByIdCarga($idCarga)
    {
        $products = DB::table('tab_conteo')
            ->select(
                'tab_conteo.conteo',
            )
            ->where('tab_conteo.id_carga', $idCarga)
            ->groupBy(
                'tab_conteo.id_carga',
                'tab_conteo.conteo'
            )
            ->get();

        return response()->json($products);
    }

    public function reporteConcentrado($idCarga, $numconteo)
    {
        $conteo = TabConteo::select('id_producto', 'codigo', 'descripcion', DB::raw('SUM(cantidad) as Total'))
            ->where('id_carga', $idCarga)
            ->where('conteo', $numconteo)
            ->groupBy('id_producto', 'codigo', 'descripcion')
            ->get();

            $conteo->transform(function ($item) {
                $item->Total = (float) $item->Total;  // Convertir a número
                return $item;
            });
        return response()->json($conteo);
    }

    public function reporteDiferenciasComparativoAnual($idCarga, $conteo1,$conteo2,$conteo3)
    {
        $sapTotals = DB::table('tab_detalle_archivos')
        ->select(
            'id_cat_prod',
            DB::raw('CAST(cantidad_total AS DECIMAL(12, 3)) AS total_sap'),
            DB::raw('CAST(importe_unitario AS DECIMAL(12, 3)) AS importe_unitario'),
            DB::raw('CAST(importe_total AS DECIMAL(12, 3)) AS importe_total')
        )
        ->where('id_carga_cab', $idCarga)
        ->groupBy('id_cat_prod', 'importe_unitario', 'importe_total', 'cantidad_total');

        // Subconsulta para conteo 1
        $conteo1Totals = DB::table('tab_conteo')
        ->select(
            'id_producto',
            DB::raw('SUM(cantidad) AS total_conteo1')
        )
        ->where('id_carga', $idCarga)
        ->where('conteo', $conteo1)
        ->groupBy('id_producto');

        // Subconsulta para conteo 2
        $conteo2Totals = DB::table('tab_conteo')
        ->select(
            'id_producto',
            DB::raw('SUM(cantidad) AS total_conteo2')
        )
        ->where('id_carga', $idCarga)
        ->where('conteo', $conteo2)
        ->groupBy('id_producto');

        // Subconsulta para conteo 3
        $conteo3Totals = DB::table('tab_conteo')
        ->select(
            'id_producto',
            DB::raw('SUM(cantidad) AS total_conteo3')
        )
        ->where('id_carga', $idCarga)
        ->where('conteo', $conteo3)
        ->groupBy('id_producto');

        // Unimos todos los datos
        $query = DB::table('tab_conteo as a')
            ->join('cat_gpo_familias as b', 'a.id_grupofamilia', '=', 'b.id')
            ->joinSub($sapTotals, 'sap', function ($join) {
                $join->on('a.id_producto', '=', 'sap.id_cat_prod');
            })
            ->leftJoinSub($conteo1Totals, 'conteo1', function ($join) {
                $join->on('a.id_producto', '=', 'conteo1.id_producto');
            })
            ->leftJoinSub($conteo2Totals, 'conteo2', function ($join) {
                $join->on('a.id_producto', '=', 'conteo2.id_producto');
            })
            ->leftJoinSub($conteo3Totals, 'conteo3', function ($join) {
                $join->on('a.id_producto', '=', 'conteo3.id_producto');
            })
            ->select(
                'a.id_producto',
                'a.codigo',
                'a.descripcion',
                'a.ume as unidad_medida',
                'b.clave_gpo_familia as grupo_articulos',
                DB::raw('CAST(sap.total_sap AS DECIMAL(12, 3)) AS SAP'),
                DB::raw('COALESCE(conteo1.total_conteo1, 0) AS Fisico_Conteo_1'),
                DB::raw('COALESCE(conteo2.total_conteo2, 0) AS Fisico_Conteo_2'),
                DB::raw('COALESCE(conteo3.total_conteo3, 0) AS Fisico_Conteo_3'),
                DB::raw('(COALESCE(conteo2.total_conteo2, 0) - COALESCE(conteo1.total_conteo1, 0)) AS Diferencia'),
                DB::raw('(COALESCE(conteo3.total_conteo3, 0) - COALESCE(sap.total_sap, 0)) AS Diferencia_Fisica_C3'),
                DB::raw('CAST(sap.importe_unitario AS DECIMAL(12, 2)) AS Importe_Unitario'),
                //DB::raw('CAST(sap.importe_unitario * COALESCE(conteo1.total_conteo1, 0) AS DECIMAL(12, 2)) AS Diferencia_Pesos_Conteo_1'),
                //DB::raw('CAST(sap.importe_unitario * COALESCE(conteo2.total_conteo2, 0) AS DECIMAL(12, 2)) AS Diferencia_Pesos_Conteo_2'),
                //DB::raw('CAST(sap.importe_unitario * COALESCE(conteo3.total_conteo3, 0) AS DECIMAL(12, 2)) AS Diferencia_Pesos_Conteo_3'),
                DB::raw('CAST((sap.importe_unitario * (COALESCE(conteo1.total_conteo1, 0) - COALESCE(sap.total_sap, 0))) AS DECIMAL(12, 2)) AS diferencia_moneda_c1'),
                DB::raw('CAST((sap.importe_unitario * (COALESCE(conteo2.total_conteo2, 0) - COALESCE(sap.total_sap, 0))) AS DECIMAL(12, 2)) AS diferencia_moneda_c2'),
                DB::raw('CAST((sap.importe_unitario * (COALESCE(conteo3.total_conteo3,0) - COALESCE(sap.total_sap,0))) AS DECIMAL(12, 2)) AS diferencia_moneda_c3'),
                DB::raw('CAST(sap.importe_total AS DECIMAL(12, 2)) AS Importe_Total')
            )
            ->where('a.id_carga', $idCarga)
            ->groupBy(
                'a.id_producto',
                'a.codigo',
                'a.descripcion',
                'a.ume',
                'b.clave_gpo_familia',
                'sap.total_sap',
                'sap.importe_unitario',
                'sap.importe_total',
                'conteo1.total_conteo1',
                'conteo2.total_conteo2',
                'conteo3.total_conteo3'
            )
            ->get();
        return response()->json($query);
    }
}
