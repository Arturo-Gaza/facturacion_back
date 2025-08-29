<?php

namespace App\Repositories\Catalogos;

use App\Interfaces\Catalogos\CatGpoFamiliaRepositoryInterface;
use App\Models\Catalogos\CatGpoFamilia;
use App\Models\Catalogos\CatProductos;
use App\Models\Catalogos\CatUnidadMedida;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CatGpoFamiliaRepository implements CatGpoFamiliaRepositoryInterface
{

    public function getAll()
    {
        return CatGpoFamilia::all();
    }

    public function getAllPersonalizado($idCarga)
    {
        $productos = DB::table('tab_detalle_archivos')

            ->join('cat_gpo_familias', 'cat_gpo_familias.id', '=', 'tab_detalle_archivos.id_gpo_familia')
            ->where('tab_detalle_archivos.id_carga_cab', $idCarga)
            ->select(
                'cat_gpo_familias.id',
                'cat_gpo_familias.clave_gpo_familia'
            )
            ->groupBy('cat_gpo_familias.id')
            ->get();

        $data1 = array();
        foreach ($productos as $val) {
            $data1[] = $val;
        }

        return $data1;
    }

    public function getByID($id): ?CatGpoFamilia
    {
        return CatGpoFamilia::where('id', $id)->first();
    }

    public function store(array $data)
    {
        return CatGpoFamilia::create($data);
    }

    public function update(array $data, $id)
    {
        return CatGpoFamilia::where('id', $id)->update($data);
    }

    public function search($data)
    {
        // Consulta principal para obtener los artículos
        $articulosQuery = CatProductos::query()
            ->select(
                'cat_productos.id',
                'cat_productos.clave_producto',
                'cat_productos.descripcion_producto',
                'cat_productos.id_unidad_medida',
                'cat_productos.id_gpo_familia',
                'c.descripcion_categoria as categoria',
                'u.descripcion_unidad_medida as unidad_medida_desc',
                'm.descripcion_moneda as moneda',
                'g.descripcion_gpo_familia as grupo_familia_desc'
            )
            ->leftJoin('cat_categorias as c', 'c.id', '=', 'cat_productos.id_categoria')
            ->leftJoin('cat_unidad_medidas as u', 'u.id', '=', 'cat_productos.id_unidad_medida')
            ->leftJoin('cat_moneda as m', 'm.id_moneda', '=', 'cat_productos.id_moneda')
            ->leftJoin('cat_gpo_familias as g', 'g.id', '=', 'cat_productos.id_gpo_familia')
            ->where('cat_productos.habilitado', true);

        // Filtro por término de búsqueda en grupo familia
        if (!empty($data['termino'])) {
            $searchTerm = mb_strtolower($data['termino'], 'UTF-8');

            $articulosQuery->where(function ($query) use ($searchTerm) {
                $query->whereRaw('LOWER(g.clave_gpo_familia) LIKE ?', [$searchTerm . '%'])
                    ->orWhereRaw('LOWER(g.descripcion_gpo_familia) LIKE ?', [$searchTerm . '%']);
            });
        }

        // Obtener los artículos
        $articulos = $articulosQuery->get();

        // Obtener IDs únicos de unidades de medida y grupos de familia
        $unidadesIds = $articulos->pluck('id_unidad_medida')->unique()->filter()->values();
        $gruposFamiliaIds = $articulos->pluck('id_gpo_familia')->unique()->filter()->values();

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
            'unidadesMedida' => $unidadesMedida,
            'gpoFamilias' => $gpoFamilias
        ];
    }

    public function exportar($filtro)
    {
        $query = CatGpoFamilia::where('habilitado', true); // o cualquier otro modelo

        if (!empty($filtro)) {
            $query->where(function ($q) use ($filtro) {
                $q->where('clave_gpo_familia', 'ilike', "%$filtro%")
                    ->orWhere('descripcion_gpo_familia', 'ilike', "%$filtro%")
                    ->orWhere('descripcion_gpo_familia_2', 'ilike', "%$filtro%");
            });
        }
        $catUni = $query->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Cabeceras
        $sheet->setCellValue('A1', 'Clave');
        $sheet->setCellValue('B1', 'Descripción');
        $sheet->setCellValue('C1', 'Descripción 2');

        // Datos
        $row = 2;
        foreach ($catUni as $uni) {
            $sheet->setCellValue("A{$row}", $uni->clave_gpo_familia);
            $sheet->setCellValue("B{$row}", $uni->descripcion_gpo_familia);
            $sheet->setCellValue("C{$row}", $uni->descripcion_gpo_familia_2);
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
            'file_name' => 'CatGpoArticulos.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'base64' => $base64,
        ];
        return  $data;
    }
}
