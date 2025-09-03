<?php

namespace App\Repositories\SistemaTickets;

use App\Interfaces\SistemaTickets\CatCategoriasRepositoryInterface;
use App\Models\SistemaTickets\CatCategorias;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CatCategoriasRepository implements CatCategoriasRepositoryInterface
{
    public function getAll()
    {
        return CatCategorias::with('tipo')->get();
    }

    public function getByID($id): ?CatCategorias
    {
        return CatCategorias::where('id', $id)->first();
        
    }

    public function getByIdCat($id)
    {
        return CatCategorias::where('id_tipo', $id)->get();
    }

    public function getByDpto($id)
    {
        return CatCategorias::whereHas('departamentos', function ($query) use ($id) {
            $query->where('id', $id);
        })->get();
    }

    public function store(array $data)
    {
        return CatCategorias::create($data);
    }

    public function update(array $data, $id)
    {
        return CatCategorias::where('id', $id)->update($data);
    }

    public function exportar($filtro)
    {
        $query = CatCategorias::where('habilitado', true); // o cualquier otro modelo

        if (!empty($filtro)) {
            $query->where(function ($q) use ($filtro) {
                $q->where('descripcion_categoria', 'ilike', "%$filtro%");
            });
        }
        $catCat = $query->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Cabeceras
        $sheet->setCellValue('A1', 'Descripcion');
        $sheet->setCellValue('B1', 'Tipo');

        // Datos
        $row = 2;
        foreach ($catCat as $dep) {
            $sheet->setCellValue("A{$row}", $dep->descripcion_categoria);
            $sheet->setCellValue("B{$row}", $dep->descripcion_tipo);
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
            'file_name' => 'CatCategoria.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'base64' => $base64,
        ];
        return  $data;
    }
}
