<?php

namespace App\Http\Controllers\ArchivoCarga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;

class InsertarArchivoController extends Controller
{
    protected $nombreArchivoCSV = 'archivoCargado.csv';

    public function insertarArchivo(Request $request)
    {
         if ($request->hasFile('csv_file')) {
            
            $path = $request->file('csv_file')->storeAs('csv', $this->nombreArchivoCSV);

      
            $file_csv = Storage::path($path);
        } elseif (Storage::exists('csv/' . $this->nombreArchivoCSV)) {
            
            $file_csv = Storage::path('csv/' . $this->nombreArchivoCSV);
        } else {
            return response()->json(['error' => 'No hay archivo almacenado.'], 400);
        }

        
        $csv = Reader::createFromPath($file_csv, 'r');
        $csv->setHeaderOffset(0);

        $idCarga = '3';

        
        foreach ($csv->getRecords() as $record) {
            DB::table('tab_archivo_completos')->insert([
                'id_detalle_carga' => $idCarga,
                'almacen' => trim($record['Almacen']),
                'material' => trim($record['Material']),
                'texto_breve_material' => mb_convert_encoding(trim($record['Texto breve de material']), 'UTF-8', 'ISO-8859-1'),
                'ume' => trim($record['UME']),
                'grupo_articulos' => mb_convert_encoding(trim($record['GPO']), 'UTF-8', 'ISO-8859-1'),
                'libre_utilizacion' => mb_convert_encoding(trim($record['Libre utilizacion']), 'UTF-8', 'ISO-8859-1'),
                'en_control_calidad' => mb_convert_encoding(trim($record['En control calidad']), 'UTF-8', 'ISO-8859-1'),
                'bloqueado' => mb_convert_encoding(trim($record['Bloqueado']), 'UTF-8', 'ISO-8859-1'),
                'valor_libre_util' => trim($record['Valor libre util.']),
                'valor_insp_cal' => trim($record['Valor en insp.cal.']),
                'valor_stock_bloq' => trim($record['Valor stock bloq.']),
                'cantidad_total' => trim($record['Cantidad total']),
                'importe_unitario' => trim($record['Importe unitario']),
                'importe_total' => trim($record['Importe total']),
            ]);
        }

        return response()->json(['message' => 'Datos guardados exitosamente.'], 200);
    
    }
}
