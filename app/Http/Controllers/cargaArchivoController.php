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

        /////////////////////////////////////////////////////////////////////////////////////
       ////////////////////////////////////////////////////////////////////////////////////
      
        // Retorna resultados
        return response()->json([
            'Numero de registros' => $conteo
            
        ]);
    }
}
