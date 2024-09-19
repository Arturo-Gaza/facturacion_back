<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;
use App\Models\ArchivoCarga\tab_detalle_carga;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ArchivoCompletoDetalleController extends Controller
{
    protected $nombreArchivoXLSX = 'archivoCargado.xlsx';

    public function detalleArchivo(Request $request, $idUser)
    {
        if ($request->hasFile('csv_file')) {
            $archivo = $request->file('csv_file');
            $nombreArchivo = $archivo->getClientOriginalName();
            $path = $archivo->storeAs('xlsx', $nombreArchivo);
            $file_xlsx = Storage::path($path);
        } elseif (Storage::exists('xlsx/' . $this->nombreArchivoXLSX)) {
            $file_xlsx = Storage::path('xlsx/' . $this->nombreArchivoXLSX);
            $nombreArchivo = basename($file_xlsx);
        } else {
            return response()->json(['error' => 'No se ha subido ningún archivo y no hay archivo almacenado.'], 400);
        }

        $spreadsheet = IOFactory::load($file_xlsx);
        $sheet = $spreadsheet->getActiveSheet();

        // Extraer datos del archivo
        $conteoGeneral = 0;

        $rows = $sheet->toArray();
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

        // Conteo de registros (excluyendo el encabezado)
        $registrosColumna = 1;
        $conteo = 0;
        for ($i = 1; $i < count($rows); $i++) {
            if (!empty($rows[$i][$registrosColumna])) {
                $conteo++;
            }
        }

        // Comparar la primera columna
        $nombreColumna = 0;
        $columnaComparar = array_unique(array_column($rows, $nombreColumna, 1));

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
        $columnaComparar3 = array_unique(array_column($rows, $numColumna3, 1));

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

        // Comparación de la cuarta columna
        $numColumna4 = 4;
        $columnaComparar4 = array_unique(array_column($rows, $numColumna4, 1));

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

        // Comparar la segunda columna (ISO-8859-1)
        $numColumna2 = 2;
        $columnaComparar2 = array_unique(array_map(function ($row) use ($numColumna2) {
            return mb_convert_encoding($row[$numColumna2], 'UTF-8', 'ISO-8859-1, UTF-8, ASCII');
        }, array_slice($rows, 1)));

        $tableCatProducto = 'cat_productos';
        $columnCompara2 = 'descripcion_producto_material';

        $conteoGeneral2 = 0;
        $conteoGeneral = 0;
        $datoNoEncontrado2 = [];

        foreach ($columnaComparar2 as $value) {
            $existente = DB::table($tableCatProducto)->where($columnCompara2, $value)->exists();
            if (!$existente) {
                $datoNoEncontrado2[] = $value;
                $conteoGeneral2++;
            }
                $conteoGeneral++;
        }

        $numDatosNoEncontrados2 = count($datoNoEncontrado2);

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

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

        $conteo2 = $conteo - 1;

        if ($conteoGeneral2 <= $conteo2 && $conteoGeneral2 >= 1) {

            $incorpora = $conteoGeneral2 - 1;
            $VoBo = $conteo - $incorpora;



            $detalleArchivo = new tab_detalle_carga();
            $detalleArchivo->cve_carga = $nuevaCveCarga;
            $detalleArchivo->id_usuario = $idUser;
            $detalleArchivo->nombre_archivo = $nombreArchivo;
            $detalleArchivo->Reg_Archivo = $conteo;
            $detalleArchivo->reg_vobo =   $VoBo;
            $detalleArchivo->reg_excluidos = 0;
            $detalleArchivo->reg_incorpora =  $incorpora;
            $detalleArchivo->Reg_a_Contar = $conteo;
            $detalleArchivo->conteo = 0;
            $detalleArchivo->id_estatus = 1;
            $detalleArchivo->observaciones = $request->input('observaciones');
            $detalleArchivo->habilitado = $request->input('habilitado', true);

            $detalleArchivo->save();

            $this->procesoInsertar($request, $detalleArchivo);
        } else {
            //cuando no hay registros en los catalogos

            $VoBo = $conteoGeneral2;
            $incorpora = $conteoGeneral2 - 1;
            $total = $incorpora;

            $detalleArchivo = new tab_detalle_carga();
            $detalleArchivo->cve_carga = $nuevaCveCarga;
            $detalleArchivo->id_usuario = $idUser;
            $detalleArchivo->nombre_archivo = $nombreArchivo;
            $detalleArchivo->Reg_Archivo = $conteo;
            $detalleArchivo->reg_vobo =    0;
            $detalleArchivo->reg_excluidos = 0;
            $detalleArchivo->reg_incorpora =  $incorpora;
            $detalleArchivo->Reg_a_Contar = $total;
            $detalleArchivo->conteo = 0;
            $detalleArchivo->id_estatus = 1;
            $detalleArchivo->observaciones = $request->input('observaciones');
            $detalleArchivo->habilitado = $request->input('habilitado', true);

            $detalleArchivo->save();

            $this->procesoInsertar($request, $detalleArchivo);
        }

        return response()->json(['success' => true, 'message' => 'Los datos no se insertaron en los catalogos', $numDatosNoEncontrados2, $conteo, 'data' => $detalleArchivo]);
    }


    public function procesoInsertar(Request $request, $detalleArchivo)
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
                'message' => 'Ocurrió un error',
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
        $this->cargarArchivoCompleto($request, $detalleArchivo);



        return response()->json([
            'dtno_Almacenes' => $datoNoEncontrado,
            $datoNoEncontrado4,
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
                'message' => 'Ocurrió un error',
                'errors' => $errors
            ], 422);
        }
        // Filtrar solo las filas que tienen datos
        $filasConDatos = array_filter($rows, function ($fila) {
            // Filtra filas donde al menos una celda tenga valor no vacío
            return array_filter($fila);
        });

        // Si también quieres excluir los encabezados (primer fila):
        $filasConDatosSinEncabezado = array_slice($filasConDatos, 1);


        foreach ($filasConDatosSinEncabezado as $record) {

            $record = array_values($record);
            DB::table('tab_archivo_completos')->insert([
                'id_detalle_carga' => $detalleArchivo->id,
                'almacen' => $record[0],
                'material' => $record[1],
                'texto_breve_material' => $record[2],
                'ume' => str_replace(['$', ' '], '', $record[3]),
                'grupo_articulos' => str_replace(['$', ' '], '', $record[4]),
                'libre_utilizacion' => str_replace(['$', ' ',','], '', $record[5]),
                'en_control_calidad' =>  str_replace(['$', ' ',','], '', $record[6]),
                'bloqueado' =>  str_replace(['$', ' ',','], '', $record[7]),
                'valor_libre_util' => str_replace(['$', ' ',','], '', $record[8]),
                'valor_insp_cal' =>  str_replace(['$', ' ',','], '', $record[9]),
                'valor_stock_bloq' =>  str_replace(['$', ' ',','], '', $record[10]),
                'cantidad_total' =>  str_replace(['$', ' ',','], '', $record[11]),
                'importe_unitario' => str_replace(['$', ' ',','], '', $record[12]),
                'importe_total' =>  str_replace(['$', ' ',','], '', $record[13]),
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
