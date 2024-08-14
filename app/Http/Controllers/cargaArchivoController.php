<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;

class cargaArchivoController extends Controller
{
    public function processCsv(Request $request)
    {
        // Verifica si se ha enviado un archivo CSV
        if (!$request->hasFile('csv_file')) {
            return response()->json(['error' => 'No se ha subido ningún archivo.'], 400);
        }

        // Ruta al archivo
        $file_csv = $request->file('csv_file')->getRealPath();

        // Leer el archivo
        $csv = Reader::createFromPath($file_csv, 'r');
        $csv->setHeaderOffset(0); 

        // Verificar el número de columnas
        $encabezado = $csv->getHeader();
        $numColumnas = 14; 
        if (count($encabezado) !== $numColumnas) {
            return response()->json(['error' => 'El archivo no tiene el número esperado de columnas.'], 400);
        }

         // Contar registros en una columna 
         $registrosColumna = 'Almacen'; // Nombre de la columna a contar
         $records = $csv->getRecords();

        $conteo = 0;
        foreach ($records as $record) {
            if (!empty($record[$registrosColumna])) {
                $conteo++;
            }
        }
        //////////////////////////////////////////////////////////////////////////////////////////////////////////

        $nombreColumna = 'Almacen'; 
        $records = $csv->getRecords();
        $columnaComparar = [];

        foreach ($records as $record) {
            if (!empty($record[$nombreColumna])) {
                $columnaComparar[] = $record[$nombreColumna];
            }
        }

        $columnaComparar = array_unique($columnaComparar);
        $tableName = 'cat_almacenes'; 
        $columnCompara = 'descripcion_almacen'; 

        $datoNoEncontrado = [];
        foreach ($columnaComparar as $value) {
            $existente = DB::table($tableName)->where($columnCompara, $value)->exists();
            if (!$existente) {
                $datoNoEncontrado[] = $value;
            }
        }

        /////////////////////////////////////////////////////////////////////////////////////////////////////////

        $nombreColumna2 = 'UME'; // Nombre de la columna a comparar
        $records = $csv->getRecords();
        $columnaComparar2 = [];

        foreach ($records as $record) {
            if (!empty($record[$nombreColumna2])) {
                $columnaComparar2[] = $record[$nombreColumna2 ];
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

        ////////////////////////////////////////////////////////////////////////////////////////////////////

        $nombreColumna3 = 'Texto breve de material'; // Nombre de la columna a comparar
        $records = $csv->getRecords();
        $columnaComparar3 = [];

        foreach ($records as $record) {
            if (!empty($record[$nombreColumna3])) {
                $columnaComparar3[] = mb_convert_encoding($record[$nombreColumna3], 'UTF-8', 'ISO-8859-1');
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

        ////////////////////////////////////////////////////////////////////////////////////////////////////
  
        ///////////////////////////////////////////////////////////////////////////////////////////////////
      
        // Retorna resultados
        return response()->json([
            'Numero de registros' => $conteo,
            'Este dato no se encuentra en la base' => $datoNoEncontrado,
            'Este dato no se encuentra ' => $datoNoEncontrado2,
            'Dato no encotrado' => $datoNoEncontrado3
        ]);
    }
}
