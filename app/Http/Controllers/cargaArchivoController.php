<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class cargaArchivoController extends Controller
{
    protected $nombreArchivoCSV = 'archivoCargado.xlsx';

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
        $file_xlsx = $request->file('csv_file')->getRealPath();
        $spreadsheet = IOFactory::load($file_xlsx);
        $sheet = $spreadsheet->getActiveSheet();

        $rows = $sheet->toArray();
        $encabezado = $rows[0];
        if (count($encabezado) > 14) {
            // Truncar el encabezado y cada fila a las primeras 14 columnas
            $encabezado = array_slice($encabezado, 0, 14);
        }
        $numColumnas = 14;
        if (count($encabezado) !== $numColumnas) {
            $errors = ['El archivo no tiene el número esperado de columnas.'];
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error',
                'errors' => $errors
            ], 422);
        }

        // Detectar celdas vacías
        $records = array_slice($rows, 1); // Excluir encabezado
        $celdasVacias = [];

        foreach ($records as $filaIndex => $fila) {
            foreach ($fila as $colIndex => $celda) {
                // Consideramos celda vacía si es null o una cadena vacía
                if ($celda === null || $celda === '') {
                    $columnaNombre = $encabezado[$colIndex] ?? "Columna " . ($colIndex + 1);
                    $celdasVacias[] = [
                        'fila' => $filaIndex + 2,
                        'columna' => $columnaNombre,
                    ];
                }
            }
        }
        // Contar registros en una columna

        $registrosColumna = 0;
        $records = array_slice($rows, 1);

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
        if (count($celdasVacias) > 0) {
            $errors = ['Hay celdas vacias', $celdasVacias];
            return response()->json([
                'success' => false,
                'message' => 'El archivo contiene celdas vacías.',
                'errors' => $errors,
            ], 422);
        }

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

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function fileNameExist()
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
    }


    public function archivoRepetido(Request $nameArchivo)
    {
        $nameArchivo = $nameArchivo->name;
        $tabledetalle = 'tab_detalle_cargas';
        $columnaCampara3 = 'nombre_archivo';

        $existente = DB::table($tabledetalle)->where($columnaCampara3, $nameArchivo)->exists();
        if (!$existente) {
            return response()->json(['message' => 'El nombre archivo no existe'], 201);
        } else {
            return response()->json(['message' => 'El nombre archivo ya existe'], 422);
        }
    }
}
