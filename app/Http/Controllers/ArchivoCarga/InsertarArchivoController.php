<?php

namespace App\Http\Controllers\ArchivoCarga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class InsertarArchivoController extends Controller
{
    protected $nombreArchivoCSV = 'archivoCargado.xlsx';

    public function insertarArchivo(Request $request)
    {
        if ($request->hasFile('csv_file')) {
            $archivo = $request->file('csv_file');
            $nombreArchivo = $archivo->getClientOriginalName();
            $path = $archivo->storeAs('xlsx', $nombreArchivo);
            $file_xlsx = Storage::path($path);
        } elseif (Storage::exists('xlsx/' . $this->nombreArchivoCSV)) {
            $file_xlsx = Storage::path('xlsx/' . $this->nombreArchivoCSV);
            $nombreArchivo = basename($file_xlsx);
        } else {
            return response()->json(['error' => 'No se ha subido ningún archivo y no hay archivo almacenado.'], 400);
        }

        $spreadsheet = IOFactory::load($file_xlsx);
        $sheet = $spreadsheet->getActiveSheet();

        // Extraer datos del archivo
        $conteoGeneral = 0;

        $rows = $sheet->toArray();

        $idCarga = '3';


        foreach ($rows as $record) {
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
