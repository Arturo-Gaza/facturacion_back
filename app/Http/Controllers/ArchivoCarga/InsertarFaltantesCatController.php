<?php

namespace App\Http\Controllers\ArchivoCarga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class InsertarFaltantesCatController extends Controller
{
    protected $nombreArchivoCSV = 'archivoCargado.xlsx';

    public function procesoInsertar(Request $request)
    {

        $file_xlsx = $request->file('csv_file')->getRealPath();
        $spreadsheet = IOFactory::load($file_xlsx);
        $sheet = $spreadsheet->getActiveSheet();

        $rows = $sheet->toArray();
        $encabezado = $rows[0];
        $numColumnas = 14;
        if (count($encabezado) !== $numColumnas) {
            $errors = ['El archivo no tiene el número esperado de columnas.'];
            return response()->json([
                'success' => false,
                'message' => 'Ocurrio un error',
                'errors' => $errors
            ], 422);
        }

        $nombreColumna = 0;
        $records = array_slice($rows, 1);
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
        ////////////////////////////////////////////////////////////////////////////////////////////

        $nombreColumna2 = 3;
        $records = array_slice($rows, 1);
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
        /////////////////////////////////////////////////////////////////////////////////////////////////////

        $nombreColumna3 = 4;
        $records = array_slice($rows, 1);
        $columnaComparar3 = [];

        foreach ($records as $record) {
            $record = array_values($record);
            if (!empty($record[$nombreColumna3])) {
                $columnaComparar3[] = $record[$nombreColumna3];
            }
        }

        $columnaComparar3 = array_unique($columnaComparar3);

        $tableproducto = 'cat_gpo_familias';
        $columnaCampara3 = 'clave_gpo_familia';

        $datoNoEncontrado3 = [];
        foreach ($columnaComparar3 as $value) {
            $existente = DB::table($tableproducto)->where($columnaCampara3, $value)->exists();
            if (!$existente) {
                $datoNoEncontrado3[] = $value;
            }
        }

        ////////////////////////////////////////////////////////////////////////////////////////////////////

        $nombreColumna4 = 2;
        $records = array_slice($rows, 1);
        $columnaComparar4 = [];

        foreach ($records as $record) {
            $record = array_values($record);
            if (!empty($record[$nombreColumna4])) {
                $columnaComparar4[] = $record[$nombreColumna4];
            }
        }

        $columnaComparar4 = array_unique($columnaComparar4);

        $tableproducto1 = 'cat_productos';
        $columnaCampara4 = 'descripcion_producto_material';

        $datoNoEncontrado4 = [];
        foreach ($columnaComparar4 as $value) {
            $existente = DB::table($tableproducto1)->where($columnaCampara4, $value)->exists();
            if (!$existente) {
                $datoNoEncontrado4[] = $value;
            }
        }

        ////////////////////////////////////////////////////////////////////////////////////////////////////
        $nombreColumna5 = 1;
        $records = array_slice($rows, 1);
        $columnaComparar5 = [];

        foreach ($records as $record) {
            $record = array_values($record);
            if (!empty($record[$nombreColumna5])) {
                $columnaComparar5[] = $record[$nombreColumna5];
            }
        }

        $columnaComparar5 = array_unique($columnaComparar5);

        $tableproducto = 'cat_productos';
        $columnaCampara5 = 'clave_producto';

        $datoNoEncontrado5 = [];
        foreach ($columnaComparar5 as $value) {
            $existente = DB::table($tableproducto)->where($columnaCampara5, $value)->exists();
            if (!$existente) {
                $datoNoEncontrado5[] = $value;
            }
        }
        ///////////////////////////////////////////////////////////////////////////////////////////////////

        $this->insertTabAlmacenes($datoNoEncontrado);
        $this->insertTabUniMedidas($datoNoEncontrado2);
        $this->insetarTabGrupoFam($datoNoEncontrado3);
        //$this->insertTabProductos($datoNoEncontrado4, $datoNoEncontrado5);


        return response()->json([
            'dtno_Almacenes' => $datoNoEncontrado, $datoNoEncontrado4,
            'success' => true,
            'message' => 'Los siguientes datos se insertaron en los catálogos correspondientes.',
        ]);
    }

    private function insertTabAlmacenes(array $datos)
    {
        foreach ($datos as $dato) {
            DB::table('cat_almacenes')->insert([
                'clave_almacen' => $dato,
                'descripcion_almacen' => $this->generateClaveAlmacen(),
                'habilitado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return response()->json([
            'message' => 'Los siguientes datos no se insertaron en los catálogos correspondientes.',
        ]);
    }

    private function generateClaveAlmacen()
    {

        return 'Almacen';
    }

    private function insertTabUniMedidas(array $datos)
    {
        foreach ($datos as $dato) {
            $exists = DB::table('cat_unidad_medidas')
                ->where('clave_unidad_medida', $dato)
                ->exists();

            if (!$exists) {
                DB::table('cat_unidad_medidas')->insert([
                    'clave_unidad_medida' => $dato,
                    'descripcion_unidad_medida' => $this->generarUnidadMedida(),
                    'habilitado' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return response()->json([
            'message' => 'Los datos se han procesado correctamente.',
        ]);
    }

    private function generarUnidadMedida()
    {
        return 'Unidad de medida';
    }

    private function insetarTabGrupoFam(array $datos)
    {
        foreach ($datos as $dato) {
            DB::table('cat_gpo_familias')->insert([
                'clave_gpo_familia' => $dato,
                'descripcion_gpo_familia' => $this->generarGpoFamilia(),
                'habilitado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return response()->json([
            'message' => 'Los siguientes datos no se insertaron en los catálogos correspondientes.',
        ]);
    }

    private function generarGpoFamilia()
    {
        return 'Grupo familia';
    }
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function obtenerNuevoId()
    {
        $ultimoId = DB::table('tab_detalle_cargas')
            ->orderBy('id', 'desc')
            ->value('id');

        return $ultimoId;
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function cargarArchivoCompleto(Request $request)
    {

        $file_xlsx = $request->file('csv_file')->getRealPath();
        $spreadsheet = IOFactory::load($file_xlsx);
        $sheet = $spreadsheet->getActiveSheet();

        $rows = $sheet->toArray();
        $records = array_slice($rows, 1);
        $encabezado = $rows[0];
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

        foreach ($records as $record) {

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

    ///////////////////////////////////////////////////////Insertar productos///////////////////////////////////////////////
    public function insertCatProductos()
    {
        $data = DB::table('tab_archivo_completos')
            ->join('cat_almacenes', 'tab_archivo_completos.almacen', '=', 'cat_almacenes.clave_almacen')
            ->join('cat_unidad_medidas', 'tab_archivo_completos.ume', '=', 'cat_unidad_medidas.clave_unidad_medida')
            ->join('cat_gpo_familias', 'tab_archivo_completos.grupo_articulos', '=', 'cat_gpo_familias.clave_gpo_familia')
            ->select(
                'tab_archivo_completos.material',
                'tab_archivo_completos.texto_breve_material',
                'cat_almacenes.id AS id_almacen',
                'cat_unidad_medidas.id AS id_unidad_medidas',
                'cat_gpo_familias.id AS id_gpo_familias'
            )
            ->get();

        foreach ($data as $row) {
            $exists = DB::table('cat_productos')
                ->where('clave_producto', $row->material)
                ->where('descripcion_producto_material', $row->texto_breve_material)
                ->exists();

            if (!$exists) {
                DB::table('cat_productos')->insert([
                    'clave_producto' => $row->material,
                    'descripcion_producto_material' => $row->texto_breve_material,
                    'id_cat_almacenes' => $row->id_almacen,
                    'id_unidad_medida' => $row->id_unidad_medidas,
                    'id_gpo_familia' => $row->id_gpo_familias,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'habilitado' => true,
                ]);
            }
        }

        return response()->json([
            'message' => 'Datos insertados correctamente en la tabla cat_productos.',
            'success' => true,
        ]);
    }
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
        DB::table('tab_archivo_completos')->delete();
    }
}
