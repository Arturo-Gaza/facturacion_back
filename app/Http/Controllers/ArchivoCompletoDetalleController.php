<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;
use App\Models\ArchivoCarga\tab_detalle_carga;
use Illuminate\Support\Facades\Storage;

class ArchivoCompletoDetalleController extends Controller
{
    protected $nombreArchivoCSV = 'archivoCargado.csv';

    public function detalleArchivo(Request $request, $idUser)
    {
        if ($request->hasFile('csv_file')) {
            $archivo = $request->file('csv_file');
            $nombreArchivo = $archivo->getClientOriginalName();
            $path = $archivo->storeAs('csv', $nombreArchivo);
            $file_csv = Storage::path($path);
        } elseif (Storage::exists('csv/' . $this->nombreArchivoCSV)) {
            $file_csv = Storage::path('csv/' . $this->nombreArchivoCSV);
            $nombreArchivo = basename($file_csv);
        } else {
            return response()->json(['error' => 'No se ha subido ningún archivo y no hay archivo almacenado.'], 400);
        }

        $csv = Reader::createFromPath($file_csv, 'r');
        $csv->setHeaderOffset(0);

        $encabezado = $csv->getHeader();
        $numColumnas = 14;
        if (count($encabezado) !== $numColumnas) {
            $errors = ['El archivo no tiene el número esperado de columnas.'];
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error',
                'errors' => $errors
            ], 422);
        }

        // Conteo de registros
        $registrosColumna = 1;
        $records = $csv->getRecords();
        $conteo = 0;

        foreach ($records as $record) {
            $record = array_values($record);
            if (!empty($record[$registrosColumna])) {
                $conteo++;
            }
        }

        // Comparación de la primera columna
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

        $numDatosNoEncontrados = count($datoNoEncontrado);

        // Comparación de la tercera columna
        $numColumna3 = 3;
        $columnaComparar3 = [];
        foreach ($records as $record) {
            $record = array_values($record);
            if (!empty($record[$numColumna3])) {
                $columnaComparar3[] = $record[$numColumna3];
            }
        }

        $columnaComparar3 = array_unique($columnaComparar3);
        $tableCatUME = 'cat_unidad_medidas';
        $columnCompara3 = 'clave_unidad_medida';

        $datoNoEncontrado3 = [];
        foreach ($columnaComparar3 as $value) {
            $existente = DB::table($tableCatUME)->where($columnCompara3, $value)->exists();
            if (!$existente) {
                $datoNoEncontrado3[] = $value;
            }
        }

        $numDatosNoEncontrados3 = count($datoNoEncontrado3);
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $numColumna4 = 4;
        $columnaComparar4 = [];
        foreach ($records as $record) {
            $record = array_values($record);
            if (!empty($record[$numColumna4])) {
                $columnaComparar4[] = $record[$numColumna4];
            }
        }

        $columnaComparar4 = array_unique($columnaComparar4);
        $tableCatGpo = 'cat_gpo_familias';
        $columnCompara4 = 'clave_gpo_familia';

        $datoNoEncontrado4 = [];
        foreach ($columnaComparar4 as $value) {
            $existente = DB::table($tableCatGpo)->where($columnCompara4, $value)->exists();
            if (!$existente) {
                $datoNoEncontrado4[] = $value;
            }
        }

        $numDatosNoEncontrados4 = count($datoNoEncontrado4);
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $numColumna2 = 2;
        $columnaComparar2 = [];

        foreach ($records as $record) {
            $record = array_values($record);
            if (!empty($record[$numColumna2])) {
                $columnaComparar2[] = mb_convert_encoding($record[$numColumna2], 'UTF-8', 'ISO-8859-1, UTF-8, ASCII');
            }
        }

        $columnaComparar2 = array_unique($columnaComparar2);
        $tableCatProducto = 'cat_productos';
        $columnCompara2 = 'descripcion_producto_material';

        $datoNoEncontrado2 = [];
        foreach ($columnaComparar2 as $value) {
            $value = mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1, UTF-8, ASCII');
            $existente = DB::table($tableCatProducto)->where($columnCompara2, $value)->exists();
            if (!$existente) {
                $datoNoEncontrado2[] = $value;
            }
        }

        $numDatosNoEncontrados2 = count($datoNoEncontrado2);
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $agregar = $numDatosNoEncontrados + $numDatosNoEncontrados2 + $numDatosNoEncontrados3 + $numDatosNoEncontrados4;
        $VoBo = ($conteo - $agregar) + $conteo;
        $total = $conteo - $numDatosNoEncontrados2;


        $ultimaCarga = DB::table('tab_detalle_cargas')
            ->select('cve_carga')
            ->orderBy('id', 'desc')
            ->first();

        if ($ultimaCarga) {
            $ultimoNumero = (int)substr($ultimaCarga->cve_carga, -4);
            $nuevoNumero = str_pad($ultimoNumero + 1, 4, '0', STR_PAD_LEFT);
            $nuevaCveCarga = 'AT-CA-' . $nuevoNumero;
        } else {
            $nuevaCveCarga = 'AT-CA-0001';
        }


        $detalleArchivo = new tab_detalle_carga();
        $detalleArchivo->cve_carga = $nuevaCveCarga;
        $detalleArchivo->id_usuario = $idUser;
        $detalleArchivo->nombre_archivo = $nombreArchivo;
        $detalleArchivo->Reg_Archivo = $conteo;
        $detalleArchivo->reg_vobo = $VoBo;
        $detalleArchivo->reg_excluidos = $agregar;
        $detalleArchivo->reg_incorpora = 0;
        $detalleArchivo->Reg_a_Contar = $total;
        $detalleArchivo->conteo = 0;
        $detalleArchivo->id_estatus = 1;
        $detalleArchivo->observaciones = $request->input('observaciones');
        $detalleArchivo->habilitado = $request->input('habilitado', true);

        $detalleArchivo->save();
        
        $this->procesoInsertar($request,$detalleArchivo);
        
        return response()->json(['success' => true, 'message' => 'Los datos no se insertaron en los catalogos',$numDatosNoEncontrados2,$conteo, 'data'=> $detalleArchivo]);
    }


    public function procesoInsertar(Request $request, $detalleArchivo)
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
        $records = $csv->getRecords();
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
        $records = $csv->getRecords();
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
        $records = $csv->getRecords();
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
        $this->cargarArchivoCompleto($request, $detalleArchivo);
        


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

        return 'Sin dato Almacen';
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
        return 'Sin Unidad de medida';
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
        return 'Sin grupo familia';
    }

    public function cargarArchivoCompleto(Request $request, $detalleArchivo)
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
                'message' => 'Ocurrió un error',
                'errors' => $errors
            ], 422);
        }


        foreach ($csv->getRecords() as $record) {

            $record = array_values($record);
            DB::table('tab_archivo_completos')->insert([
                'id_detalle_carga' => $detalleArchivo->id,
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
        $this->insertCatProductos();
        $this->insertarDetalleArchivos();
        return response()->json(['message' => 'Datos guardados exitosamente.'], 200);
    }

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
