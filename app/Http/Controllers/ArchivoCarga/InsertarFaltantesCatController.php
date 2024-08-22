<?php

namespace App\Http\Controllers\ArchivoCarga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;

class InsertarFaltantesCatController extends Controller
{
    protected $nombreArchivoCSV = 'archivoCargado.csv';

    public function procesoInsertar(Request $request)
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
                'message' => 'Ocurrio un error',
                'errors' => $errors
            ], 422);
        }


        $records = $csv->getRecords();

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
        ////////////////////////////////////////////////////////////////////////////////////////////

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
        /////////////////////////////////////////////////////////////////////////////////////////////////////

        $nombreColumna3 = 4;
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
        $columnaComparar4 = [];

        foreach ($records as $record) {
            $record = array_values($record);
            if (!empty($record[$nombreColumna4])) {
                $columnaComparar4[] = $record[$nombreColumna4];
            }
        }

        $columnaComparar4 = array_unique($columnaComparar4);

        $tableproducto = 'cat_productos';
        $columnaCampara4 = 'descripcion_producto_material';

        $datoNoEncontrado4 = [];
        foreach ($columnaComparar4 as $value) {
            $existente = DB::table($tableproducto)->where($columnaCampara4, $value)->exists();
            if (!$existente) {
                $datoNoEncontrado4[] = $value;
            }
        }

        ////////////////////////////////////////////////////////////////////////////////////////////////////
        $nombreColumna5 = 1;
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
        $this->insertTabProductos($datoNoEncontrado4, $datoNoEncontrado5);
        $this->insertCatProductos();

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

    private function insertTabProductos(array $productos, array $clave)
    {
        $insertData = [];

        for ($i = 0; $i < count($productos); $i++) {
            $insertData[] = [
                'clave_producto' => $clave[$i],
                'descripcion_producto_material' => $productos[$i],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('cat_materiales')->insert($insertData);
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
}
