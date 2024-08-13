<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;

class importarArchivoController extends Controller
{
    public function processCsv(Request $request)
    
        {
    // Ruta al archivo 
    $file_csv = $request->file('csv_file')->getRealPath();

    // Leer el archivo 
    $csv = Reader::createFromPath($file_csv, 'r');
    $csv->setHeaderOffset(0); // Si el CSV tiene encabezado

    // Verificar el número de columnas 
    $encabezado = $csv->getHeader();
    $numColumnas = 3; // Número esperado de columnas
    if (count($encabezado) !== $numColumnas) {
        return response()->json(['error' => 'El archivo no tiene el número esperado de columnas.']);
    }

    // Contar registros en una columna 
    $registrosColumna = 'nombre'; // Nombre de la columna a contar
    $records = $csv->getRecords();

    $conteo = 0;
    foreach ($records as $record) {
        if (!empty($record[$registrosColumna])) {
            $conteo++;
        }
    }

    // Extraer los valores de la columna que deseas comparar
    $nombreColumna = 'nombre'; // Nombre de la columna a comparar
    $records = $csv->getRecords();
    $columnaComparar = [];

    foreach ($records as $record) {
        if (!empty($record[$nombreColumna])) {
            $columnaComparar[] = $record[$nombreColumna];
        }
    }

    // Evitar duplicados
    $columnaComparar = array_unique($columnaComparar);

    $nombreColumna2 = 'apellido'; // Nombre de la columna a comparar
    $records = $csv->getRecords();
    $columnaComparar2 = [];

    foreach ($records as $record) {
        if (!empty($record[$nombreColumna2])) {
            $columnaComparar2[] = $record[$nombreColumna2 ];
        }
    }

    $columnaComparar2 = array_unique($columnaComparar2);

    // Comparar con la base de datos
$tableName = 'tab_empleados'; // Nombre de la tabla en la base de datos
$columnCompara = 'nombre'; // Nombre de la columna en la base de datos

//segunda tabla
$tableApellido = 'tab_empleados';
$columnaCampara2 = 'apellidoP';

$datoNoEncontrado = [];
foreach ($columnaComparar as $value) {
    $existente = DB::table($tableName)->where($columnCompara, $value)->exists();
    if (!$existente) {
        $datoNoEncontrado[] = $value;
    }
}

$datoNoEncontrado2 = [];
foreach ($columnaComparar2 as $value) {
    $existente = DB::table($tableApellido)->where($columnaCampara2, $value)->exists();
    if (!$existente) {
        $datoNoEncontrado2[] = $value;
    }
}

// Retornar resultados
return response()->json([
    'Este dato no se encuentra en la base' => $datoNoEncontrado,
    'Este dato no se encuentra ' => $datoNoEncontrado2,
    'Numero de columnas' => count($encabezado),
    'Numero de registros' => $conteo,
]);
}

}