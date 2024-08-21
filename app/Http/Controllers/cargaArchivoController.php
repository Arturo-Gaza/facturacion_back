<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $records = $csv->getRecords();
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
        $records = $csv->getRecords();
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
                $columnaProductosAll[] = $record[$nombreColumna5]." - ".$record[2] ;
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

        $claveCarga = $this->obtenerNuevoId();


        foreach ($csv->getRecords() as $record) {
            DB::table('tab_archivo_completos')->insert([
                'clave_almacen' => trim($record['Almacén']),
                'material' => trim($record['Material']),
                'texto_breve_material' => mb_convert_encoding(trim($record['Texto breve de material']), 'UTF-8', 'ISO-8859-1'),
                'ume' => trim($record['UME']),
                'grupo_articulos' => mb_convert_encoding(trim($record['Grupo de artículos']), 'UTF-8', 'ISO-8859-1'),
                'libre_utilizacion' => $this->limpiarFormatoMoneda(trim($record['Libre utilización'])),
                'en_control_calidad' =>  $this->limpiarFormatoMoneda(trim($record['En control calidad'])),
                'bloqueado' =>  $this->limpiarFormatoMoneda(trim($record['Bloqueado'])),
                'valor_libre_util' => $this->limpiarFormatoMoneda(trim($record['Valor libre util.'])),
                'valor_insp_cal' =>  $this->limpiarFormatoMoneda(trim($record['Valor en insp.cal.'])),
                'valor_stock_bloq' =>  $this->limpiarFormatoMoneda(trim($record['Valor stock bloq.'])),
                'cantidad_total' =>  $this->limpiarFormatoMoneda(trim($record['Cantidad total (SAP)'])),
                'importe_unitario' => $this->limpiarFormatoMoneda(trim($record['Importe unitario'])),
                'importe_total' =>  $this->limpiarFormatoMoneda(trim($record['Importe total'])),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->insertarDatosSiNoExisten();
        return response()->json(['message' => 'Datos guardados exitosamente.'], 200);
    }
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
    private function limpiarFormatoMoneda($valor)
    {
        // Elimina signos de dólar y comas
        return preg_replace('/[^\d.]/', '', $valor);
    }
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function insertarDatosSiNoExisten()
    {
        $datos = DB::table('tab_archivo_completos')
            ->join('cat_almacenes', 'tab_archivo_completos.clave_almacen', '=', 'cat_almacenes.clave_almacen')
            ->join('cat_unidad_medidas', 'tab_archivo_completos.ume', '=', 'cat_unidad_medidas.clave_unidad_medida')
            ->join('cat_gpo_familias', 'tab_archivo_completos.grupo_articulos', '=', 'cat_gpo_familias.clave_gpo_familia')
            ->select(
                'tab_archivo_completos.material AS clave_producto',
                'tab_archivo_completos.texto_breve_material AS descripcion_producto_material',
                'cat_almacenes.id AS id_cat_almacenes',
                'cat_unidad_medidas.id AS id_unidad_medida',
                'cat_gpo_familias.id AS id_gpo_familia'
            )
            ->distinct()
            ->get();


        $datosInsertados = [];


        foreach ($datos as $dato) {
            $existe = DB::table('cat_productos')
                ->where('clave_producto', $dato->clave_producto)
                ->where('descripcion_producto_material', $dato->descripcion_producto_material)
                ->where('id_cat_almacenes', $dato->id_cat_almacenes)
                ->where('id_unidad_medida', $dato->id_unidad_medida)
                ->where('id_gpo_familia', $dato->id_gpo_familia)
                ->exists();


            if (!$existe) {
                DB::table('cat_productos')->insert([
                    'clave_producto' => $dato->clave_producto,
                    'descripcion_producto_material' => $dato->descripcion_producto_material,
                    'id_cat_almacenes' => $dato->id_cat_almacenes,
                    'id_unidad_medida' => $dato->id_unidad_medida,
                    'id_gpo_familia' => $dato->id_gpo_familia,
                    'habilitado' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);


                $datosInsertados[] = $dato;
                Log::info('Dato insertado: ', (array) $dato);
            }
        }


        return response()->json($datosInsertados);
    }
}
