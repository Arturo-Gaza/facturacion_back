<?php

namespace App\Repositories\Catalogos;

use App\Http\Resources\CatProductoLiteResource;
use App\Interfaces\Catalogos\CatProductosRepositoryInterface;
use App\Models\Catalogos\CatGpoFamilia;
use App\Models\Catalogos\CatProductos;
use App\Models\Catalogos\CatUnidadMedida;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CatProductosRepository implements CatProductosRepositoryInterface
{
    public function getAll()
    {
        $productos = DB::table('cat_productos as p')
            ->leftJoin('cat_categorias as c', 'c.id', '=', 'p.id_categoria')
            ->leftJoin('cat_unidad_medidas as u', 'u.id', '=', 'p.id_unidad_medida')
            ->leftJoin('cat_moneda as m', 'm.id_moneda', '=', 'p.id_moneda')
            ->leftJoin('cat_gpo_familias as g', 'g.id', '=', 'p.id_gpo_familia')
            ->select(
                'p.id',
                'p.clave_producto',
                'p.descripcion_producto',
                'c.descripcion_categoria',
                'u.descripcion_unidad_medida',
                'm.descripcion_moneda',
                'g.descripcion_gpo_familia',
                'p.habilitado'
            )
            ->get();

        return $productos;
    }


    public function search($data)
    {
        $query = CatProductos::query()
            ->select(
                'cat_productos.id',
                'cat_productos.clave_producto',
                'cat_productos.descripcion_producto',
                'c.id as id_categoria',
                'c.descripcion_categoria as categoria',
                'u.id as id_unidad_medida',
                'u.descripcion_unidad_medida as unidad_medida',
                'm.id_moneda as id_moneda',
                'm.descripcion_moneda as moneda',
                'g.id as id_grupo_familia',
                'g.descripcion_gpo_familia as grupo_familia'
            )
            ->leftJoin('cat_categorias as c', 'c.id', '=', 'cat_productos.id_categoria')
            ->leftJoin('cat_unidad_medidas as u', 'u.id', '=', 'cat_productos.id_unidad_medida')
            ->leftJoin('cat_moneda as m', 'm.id_moneda', '=', 'cat_productos.id_moneda')
            ->leftJoin('cat_gpo_familias as g', 'g.id', '=', 'cat_productos.id_gpo_familia')
            ->where('cat_productos.habilitado', true);

        // Filtro por categoría
        if (!empty($data['categoria'])) {
            $query->where('cat_productos.id_categoria', $data['categoria']);
        }

        // Filtro por término de búsqueda (insensible a mayúsculas/minúsculas)
        if (!empty($data['termino'])) {
            $searchTerm = $data['termino'];

            if ($data['modo'] === 'clave') {
                $query->whereRaw('LOWER(cat_productos.clave_producto) LIKE ?', [strtolower($searchTerm) . '%']);
            } else {
                $query->whereRaw('LOWER(cat_productos.descripcion_producto) LIKE ?', [strtolower($searchTerm) . '%']);
            }
        }
        $articulos = $query->take(100)->get();

        // Obtener IDs únicos de unidades de medida y grupos de familia
        $unidadesIds = $articulos->pluck('id_unidad_medida')->unique()->filter()->values();
        $gruposFamiliaIds = $articulos->pluck('id_grupo_familia')->unique()->filter()->values();

        // Obtener las unidades de medida relacionadas
        $unidadesMedida = [];
        if ($unidadesIds->isNotEmpty()) {
            $unidadesMedida = CatUnidadMedida::whereIn('id', $unidadesIds)
                ->select('id', 'clave_unidad_medida', 'descripcion_unidad_medida')
                ->get();
        }

        // Obtener los grupos de familia relacionados
        $gpoFamilias = [];
        if ($gruposFamiliaIds->isNotEmpty()) {
            $gpoFamilias = CatGpoFamilia::whereIn('id', $gruposFamiliaIds)
                ->select('id', 'clave_gpo_familia', 'descripcion_gpo_familia')
                ->get();
        }



        return [
            'articuloList' => $articulos,
            'unidadMedidaList' => $unidadesMedida,
            'grupoFamiliaList' => $gpoFamilias
        ];
    }

    public function getByID($id): ?CatProductos
    {
        return CatProductos::with('categoria')->where('id', $id)->first();

    }
    public function getBygetByCategoria($id)
    {
        $query = CatProductos::query()
            ->select(
                'cat_productos.id',
                'cat_productos.clave_producto',
                'cat_productos.descripcion_producto',
                'c.id as id_categoria',
                'c.descripcion_categoria as categoria',
                'u.id as id_unidad_medida',
                'u.descripcion_unidad_medida as unidad_medida',
                'm.id_moneda as id_moneda',
                'm.descripcion_moneda as moneda',
                'g.id as id_gpo_familia',
                'g.descripcion_gpo_familia as grupo_familia'
            )
            ->leftJoin('cat_categorias as c', 'c.id', '=', 'cat_productos.id_categoria')
            ->leftJoin('cat_unidad_medidas as u', 'u.id', '=', 'cat_productos.id_unidad_medida')
            ->leftJoin('cat_moneda as m', 'm.id_moneda', '=', 'cat_productos.id_moneda')
            ->leftJoin('cat_gpo_familias as g', 'g.id', '=', 'cat_productos.id_gpo_familia')
            ->where('cat_productos.habilitado', true)
            ->where('cat_productos.id_categoria', $id);;
        return $query->get();
    }

    public function store(array $data)
    {
        return CatProductos::create($data);
    }

    public function update(array $data, $id)
    {
        return CatProductos::where('id', $id)->update($data);
    }

    public function getAllPersonalizado($idCarga)
    {

        // $productos = CatProductos::select(
        //     'cat_productos.id',
        //     'cat_productos.id_cat_almacenes',
        //     'cat_productos.id_unidad_medida',
        //     'cat_productos.id_gpo_familia',
        //     'cat_productos.clave_producto',
        //     'cat_productos.descripcion_producto_material',
        //     'cat_productos.habilitado',
        //     'cat_productos.habilitado',
        //     'cat_productos.habilitado AS asigHabilitado',
        //     'cat_almacenes.clave_almacen',
        //     'cat_unidad_medidas.clave_unidad_medida',
        //     'cat_gpo_familias.clave_gpo_familia'
        // )
        //     ->join('cat_almacenes', 'cat_almacenes.id', '=', 'cat_productos.id_cat_almacenes')
        //     ->join('cat_unidad_medidas', 'cat_unidad_medidas.id', '=', 'cat_productos.id_unidad_medida')
        //     ->join('cat_gpo_familias', 'cat_gpo_familias.id', '=', 'cat_productos.id_gpo_familia')
        //     ->get();

        $productos = DB::table('tab_detalle_archivos')
            ->join('cat_productos', 'cat_productos.id', '=', 'tab_detalle_archivos.id_cat_prod')
            ->join('cat_almacenes', 'cat_almacenes.id', '=', 'tab_detalle_archivos.id_almacen')
            ->join('cat_unidad_medidas', 'cat_unidad_medidas.id', '=', 'tab_detalle_archivos.id_unid_med')
            ->join('cat_gpo_familias', 'cat_gpo_familias.id', '=', 'tab_detalle_archivos.id_gpo_familia')
            ->where('tab_detalle_archivos.id_carga_cab', $idCarga)
            ->select(
                'tab_detalle_archivos.id_cat_prod as id',
                'tab_detalle_archivos.id_almacen as id_cat_almacenes',
                'tab_detalle_archivos.id_unid_med as id_unidad_medida',
                'tab_detalle_archivos.id_gpo_familia as id_gpo_familia',
                'cat_productos.clave_producto',
                'cat_productos.descripcion_producto_material',
                'cat_productos.habilitado AS asigHabilitado',
                'cat_almacenes.clave_almacen',
                'cat_unidad_medidas.clave_unidad_medida',
                'cat_gpo_familias.clave_gpo_familia'
            )
            ->groupBy('tab_detalle_archivos.id_cat_prod')
            ->groupBy('tab_detalle_archivos.id_almacen')
            ->groupBy('tab_detalle_archivos.id_unid_med')
            ->groupBy('tab_detalle_archivos.id_gpo_familia')
            ->groupBy('cat_productos.clave_producto')
            ->groupBy('cat_productos.descripcion_producto_material')
            ->groupBy('cat_productos.habilitado')
            ->groupBy('cat_almacenes.clave_almacen')
            ->groupBy('cat_unidad_medidas.clave_unidad_medida')
            ->groupBy('cat_gpo_familias.clave_gpo_familia')
            ->get();

        $data1 = array();
        foreach ($productos as $val) {
            $data1[] = $val;
        }

        return $data1;
    }
    public function exportar($filtro)
    {
        $query = CatProductos::where('habilitado', true); // o cualquier otro modelo

        if (!empty($filtro)) {
            $query->where(function ($q) use ($filtro) {
                $q->where('clave_producto', 'ilike', "%$filtro%")
                    ->orWhere('descripcion_producto', 'ilike', "%$filtro%");
            });
        }
        $catUni = $query->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Cabeceras
        $sheet->setCellValue('A1', 'Clave');
        $sheet->setCellValue('B1', 'Descripción');
        $sheet->setCellValue('C1', 'Unidad de Medida');
        $sheet->setCellValue('D1', 'Moneda');
        $sheet->setCellValue('E1', 'Grupo Articulo');
        $sheet->setCellValue('F1', 'Categoria');


        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Datos
        $row = 2;
        foreach ($catUni as $uni) {
            $sheet->setCellValue("A{$row}", $uni->clave_producto);
            $sheet->setCellValue("B{$row}", $uni->descripcion_producto);
            $sheet->setCellValue("C{$row}", $uni->descripcion_unidad_medida);
            $sheet->setCellValue("D{$row}", $uni->descripcion_moneda);
            $sheet->setCellValue("E{$row}", $uni->descripcion_gpo_familia);
            $sheet->setCellValue("F{$row}", $uni->descripcion_categoria);
            $row++;
        }



        // Guardar en memoria
        $writer = new Xlsx($spreadsheet);
        $tempMemory = fopen('php://memory', 'r+');
        $writer->save($tempMemory);
        rewind($tempMemory);

        $excelContent = stream_get_contents($tempMemory);
        fclose($tempMemory);

        $base64 = base64_encode($excelContent);
        $data = [
            'file_name' => 'CatProductos.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'base64' => $base64,
        ];


        return  $data;
    }
}
