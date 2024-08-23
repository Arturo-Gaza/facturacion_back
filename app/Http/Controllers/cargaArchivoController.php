<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;

class cargaArchivoController extends Controller
{
    protected $nombreArchivoCSV = 'archivoCargado.csv';

    public function processCsv(Request $request)
    {
        if (!$request->hasFile('csv_file')) {
            $errors = ['No se ha subido ningún archivo.'];
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error',
                'errors' => $errors
            ], 422);
        }

        // Ruta al archivo
        $file_csv = $request->file('csv_file')->getRealPath();

        $handle = fopen($file_csv, 'r');

        stream_filter_append($handle, 'convert.iconv.ISO-8859-1/UTF-8');

        $csv = Reader::createFromStream($handle);
        $csv->setHeaderOffset(0);

        // Verificar el número de columnas
        $encabezado = $csv->getHeader();
        $numColumnas = 14;
        if (count($encabezado) !== $numColumnas) {
            $errors = ['El archivo no tiene el número esperado de columnas.'];
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error',
                'errors' => $errors
            ], 422);
        }

        // Contar registros en una columna

        $registrosColumna = 0;
        $records = $csv->getRecords();

        $conteo = 0;

        foreach ($records as $record) {
            $record = array_values($record);
            if (!empty($record[$registrosColumna])) {
                $conteo++;
            }
        }

        //////////////////////////////////////////////////////////////////////////////////////////////////////////

        $nombreColumna = 0;
        $columnaComparar = [];

        foreach ($records as $record) {
            $record = array_values($record);
            if (!empty($record[$nombreColumna])) {
                $columnaComparar[] = $record[$nombreColumna];
            }
        }

        $columnaComparar = array_unique($columnaComparar);
        $tableName = 'cat_almacenes';
        $columnCompara = 'clave_almacen';

        $datoNoEncontrado = [];
        foreach ($columnaComparar as $value) {
            $existente = DB::table($tableName)->where($columnCompara, $value)->exists();
            if (!$existente) {
                $datoNoEncontrado[] = $value;
            }
        }

        /////////////////////////////////////////////////////////////////////////////////////////////////////////

        $nombreColumna2 = 3;
        $columnaComparar2 = [];

        foreach ($records as $record) {
            $record = array_values($record);
            if (!empty($record[$nombreColumna2])) {
                $columnaComparar2[] = $record[$nombreColumna2];
            }
        }

        $columnaComparar2 = array_unique($columnaComparar2);

        $tableUM = 'cat_unidad_medidas';
        $columnaCampara2 = 'clave_unidad_medida';

        $datoNoEncontrado2 = [];
        foreach ($columnaComparar2 as $value) {
            $existente = DB::table($tableUM)->where($columnaCampara2, $value)->exists();
            if (!$existente) {
                $datoNoEncontrado2[] = $value;
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////////////////
        $nombreColumna4 = 4;
        $columnaComparar4 = [];

        foreach ($records as $record) {
            $record = array_values($record);
            if (!empty($record[$nombreColumna4])) {
                $columnaComparar4[] = $record[$nombreColumna4];
            }
        }

        $columnaComparar4 = array_unique($columnaComparar4);

        $tablefamilia = 'cat_gpo_familias';
        $columnaCampara4 = 'clave_gpo_familia';

        $datoNoEncontrado4 = [];
        foreach ($columnaComparar4 as $value) {
            $existente = DB::table($tablefamilia)->where($columnaCampara4, $value)->exists();
            if (!$existente) {
                $datoNoEncontrado4[] = $value;
            }
        }

        ////////////////////////////////////////////////////////////////////////////////////////////////////

        $nombreColumna3 = 2;
        $columnaComparar3 = [];

        foreach ($records as $record) {
            $record = array_values($record);
            if (!empty($record[$nombreColumna3])) {
                $columnaComparar3[] = $record[$nombreColumna3];
            }
        }

        $columnaComparar3 = array_unique($columnaComparar3);

        $tableproducto = 'cat_productos';
        $columnaCampara3 = 'descripcion_producto_material';

        $datoNoEncontrado3 = [];
        foreach ($columnaComparar3 as $value) {
            $existente = DB::table($tableproducto)->where($columnaCampara3, $value)->exists();
            if (!$existente) {
                $datoNoEncontrado3[] = $value;
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////////////
        $nombreColumna5 = 1;
        $columnaComparar5 = [];
        $columnaProductosAll = [];

        foreach ($records as $record) {
            $record = array_values($record);
            if (!empty($record[$nombreColumna5])) {
                $columnaComparar5[] = $record[$nombreColumna5];
                $columnaProductosAll[] = $record[$nombreColumna5] . " - " . $record[2];
            }
        }

        $columnaComparar5 = array_unique($columnaComparar5);

        $tableproductocve = 'cat_productos';
        $columnaCampara5 = 'clave_producto';

        $datoNoEncontrado5 = [];
        foreach ($columnaComparar5 as $value) {
            $existente = DB::table($tableproductocve)->where($columnaCampara5, $value)->exists();
            if (!$existente) {
                $datoNoEncontrado5[] = $value;
            }
        }
        //////////////////////////////////////////////////////////////////////////////////////////////////
        // Retorna resultados
        return response()->json([
            'num_registros' => $conteo,
            'dtno_Almacenes' => $datoNoEncontrado,
            'dtno_Unidades_medida' => $datoNoEncontrado2,
            'dtno_Productos' => $datoNoEncontrado3,
            'dtno_Grupo_articulos' => $datoNoEncontrado4,
            'dtno_Clave_Material' => $datoNoEncontrado5,
            'dtno_ProductosAll' => $columnaProductosAll,
            'success' => true,
            'message' => 'Los siguientes datos no se encuentran en los catálogos correspondientes.',
        ]);
    }
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function obtenerNuevoId()
    {
        $ultimoId = DB::table('tab_detalle_cargas')
            ->orderBy('id', 'desc')
            ->value('id');

        return $ultimoId;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function cargarArchivoCompleto(Request $request)
    {

        $file_csv = $request->file('csv_file')->getRealPath();

        $handle = fopen($file_csv, 'r');

        stream_filter_append($handle, 'convert.iconv.ISO-8859-1/UTF-8');

        $csv = Reader::createFromStream($handle);
        $csv->setHeaderOffset(0);


        $encabezado = $csv->getHeader();
        $numColumnas = 14;
        if (count($encabezado) !== $numColumnas) {
            $errors = ['El archivo no tiene el número esperado de columnas.'];
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error',
                'errors' => $errors
            ], 422);
        }

        $claveCarga = $this->obtenerNuevoId();

        foreach ($csv->getRecords() as $record) {

            $record = array_values($record);
            DB::table('tab_archivo_completos')->insert([
                'id_detalle_carga' => $claveCarga,
                'almacen' => $record[0],
                'material' => $record[1],
                'texto_breve_material' => $record[2],
                'ume' => $record[3],
                'grupo_articulos' => $record[4],
                'libre_utilizacion' => $record[5],
                'en_control_calidad' =>  $record[6],
                'bloqueado' =>  $record[7],
                'valor_libre_util' => $record[8],
                'valor_insp_cal' =>  $record[9],
                'valor_stock_bloq' =>  $record[10],
                'cantidad_total' =>  $record[11],
                'importe_unitario' => $record[12],
                'importe_total' =>  $record[13],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
       $this->insertarDetalleArchivos();
        return response()->json(['message' => 'Datos guardados exitosamente.'], 200);
    }
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function insertarDetalleArchivos()
    {
        $datos = DB::table('tab_archivo_completos')
            ->join('cat_almacenes', 'tab_archivo_completos.almacen', '=', 'cat_almacenes.clave_almacen')
            ->join('cat_unidad_medidas', 'tab_archivo_completos.ume', '=', 'cat_unidad_medidas.clave_unidad_medida')
            ->join('cat_productos', 'tab_archivo_completos.texto_breve_material', '=', 'cat_productos.descripcion_producto_material')
            ->join('cat_gpo_familias', 'tab_archivo_completos.grupo_articulos', '=', 'cat_gpo_familias.clave_gpo_familia')
            ->select(
                'tab_archivo_completos.id_detalle_carga as id_carga_cab',
                'cat_almacenes.id as id_almacen',
                'cat_productos.id as id_cat_prod',
                'cat_unidad_medidas.id as id_unid_med',
                'cat_gpo_familias.id as id_gpo_familia',
                'tab_archivo_completos.libre_utilizacion as libre_utilizacion',
                'tab_archivo_completos.en_control_calidad as en_control_calidad',
                'tab_archivo_completos.bloqueado as bloqueado',
                'tab_archivo_completos.valor_libre_util as valor_libre_util',
                'tab_archivo_completos.valor_insp_cal as valor_insp_cal',
                'tab_archivo_completos.valor_stock_bloq as valor_stock_bloq',
                'tab_archivo_completos.cantidad_total as cantidad_total',
                'tab_archivo_completos.importe_unitario as importe_unitario',
                'tab_archivo_completos.importe_total as importe_total',
                DB::raw('true as habilitado')
            )
            ->get();

        $datosArray = $datos->map(function ($item) {
            return (array) $item;
        })->toArray();

        DB::table('tab_detalle_archivos')->insert($datosArray);

       // DB::table('tab_archivo_completos')->delete();

    }
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
