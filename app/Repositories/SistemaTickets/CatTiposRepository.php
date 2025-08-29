<?php

namespace App\Repositories\SistemaTickets;

use App\Interfaces\SistemaTickets\CatTiposRepositoryInterface;
use App\Models\SistemaTickets\CatCategorias;
use App\Models\SistemaTickets\CatTipos;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CatTiposRepository implements CatTiposRepositoryInterface
{
    public function getAll()
    {
        return CatTipos::all();
    }

    public function getByID($id): ?CatTipos
    {
        return CatTipos::where('id', $id)->first();
    }
    public function getByDpto($id)
    {
        return CatCategorias::whereHas('departamentos', function ($query) use ($id) {
            $query->where('id', $id);
        })
            ->with('tipo') // Asegura que se cargue la relación tipo
            ->get()
            ->pluck('tipo')       // Obtener solo los objetos tipo
            ->unique('id')        // Eliminar duplicados por ID
            ->values();
    }

    public function store(array $data)
    {
        return CatTipos::create($data);
    }

    public function update(array $data, $id)
    {
        return CatTipos::where('id', $id)->update($data);
    }
    public function exportar($filtro)
    {
        $query = CatTipos::where('habilitado', true); // o cualquier otro modelo

        if (!empty($filtro)) {
            $query->where(function ($q) use ($filtro) {
                $q->where('descripcion', 'ilike', "%$filtro%");
            });
        }
        $catTi = $query->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Cabeceras
        $sheet->setCellValue('A1', 'Descripcion');
        $sheet->setCellValue('B1', 'Requiere Marca o Modelo');

        // Datos
        $row = 2;
        foreach ($catTi as $tip) {
            $sheet->setCellValue("A{$row}", $tip->descripcion);
            $sheet->setCellValue("B{$row}", $tip->req_marca_modelo);
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
            'file_name' => 'CatTipos.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'base64' => $base64,
        ];
        return  $data;
    }
}
