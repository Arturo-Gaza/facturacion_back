<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class subirCatalogoUbicacionesController extends Controller
{
    public function importUbicaciones()
    {
        $filePath = 'C:/Users/Arturo/Downloads/ubicaciones/Libro2Ubicacion.csv';

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'El archivo no existe.'], 404);
        }

        $handle = fopen($filePath, 'r');
        $header = fgetcsv($handle);

      
        while (($row = fgetcsv($handle, 1000, ",")) !== false) {
        
            DB::table('cat_ubicaciones')->insert([
                'clave_ubicacion' => $row[0],
                'descripcion_ubicacion' => ('Sin dato ubicación'),
                'habilitado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        fclose($handle);

        return response()->json(['success' => 'Datos insertados correctamente.']);
    }
}
