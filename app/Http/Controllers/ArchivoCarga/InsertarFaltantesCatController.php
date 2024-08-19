<?php

namespace App\Http\Controllers\ArchivoCarga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

        $nombreColumna = 'Almacén';
        $columnaComparar = [];

        foreach ($records as $record) {
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

        $nombreColumna2 = 'UME'; // Nombre de la columna a comparar
        $records = $csv->getRecords();
        $columnaComparar2 = [];

        foreach ($records as $record) {
            if (!empty($record[$nombreColumna2])) {
                $columnaComparar2[] = $record[$nombreColumna2];
            }
        }

        $columnaComparar2 = array_unique($columnaComparar2);

        $tableUM = 'cat_unidad_medidas';
        $columnaCampara2 = 'descripcion_unidad_medida';

        $datoNoEncontrado2 = [];
        foreach ($columnaComparar2 as $value) {
            $existente = DB::table($tableUM)->where($columnaCampara2, $value)->exists();
            if (!$existente) {
                $datoNoEncontrado2[] = $value;
            }
        }
        /////////////////////////////////////////////////////////////////////////////////////////////////////

        $nombreColumna3 = 'Grupo de artículos';
        $columnaComparar3 = [];

        foreach ($records as $record) {
            if (!empty($record[$nombreColumna3])) {
                $columnaComparar3[] = $record[$nombreColumna3];
            }
        }

        $columnaComparar3 = array_unique($columnaComparar3);

        $tableproducto ='cat_gpo_familias';
        $columnaCampara3 = 'clave_gpo_familia';

        $datoNoEncontrado3 = [];
        foreach ($columnaComparar3 as $value) {
            $existente = DB::table($tableproducto)->where($columnaCampara3, $value)->exists();
            if (!$existente) {
                $datoNoEncontrado3[] = $value;
            }
        }

        ////////////////////////////////////////////////////////////////////////////////////////////////////

        $nombreColumna4 = 'Texto breve de material';
        $columnaComparar4 = [];

        foreach ($records as $record) {
            if (!empty($record[$nombreColumna4])) {
                $columnaComparar4[] = $record[$nombreColumna4];
            }
        }

        $columnaComparar4 = array_unique($columnaComparar4);

        $tableproducto = 'cat_materiales';
        $columnaCampara4 = 'descripcion_producto_material';

        $datoNoEncontrado4 = [];
        foreach ($columnaComparar4 as $value) {
            $existente = DB::table($tableproducto)->where($columnaCampara4, $value)->exists();
            if (!$existente) {
                $datoNoEncontrado4[] = $value;
            }
        }

        ////////////////////////////////////////////////////////////////////////////////////////////////////
        $nombreColumna5 = 'Material';
        $columnaComparar5 = [];

        foreach ($records as $record) {
            if (!empty($record[$nombreColumna5])) {
                $columnaComparar5[] = $record[$nombreColumna5];
            }
        }

        $columnaComparar5 = array_unique($columnaComparar5);

        $tableproducto = 'cat_materiales';
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
        $this->insertTabProductos($datoNoEncontrado4,$datoNoEncontrado5);

        return response()->json([
            'dtno_Almacenes' => $datoNoEncontrado,
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
            DB::table('cat_unidad_medidas')->insert([
                'clave_unidad_medida' => $dato,
                'descripcion_unidad_medida' => $this->generarUnidadMedida(), 
                'habilitado' => true, 
                'created_at' => now(), 
                'updated_at' => now(), 
            ]);
        }
        return response()->json([
            'message' => 'Los siguientes datos no se insertaron en los catálogos correspondientes.',
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
            'descripcion_gpo_familia'=> $this->generarGpoFamilia(),
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
}