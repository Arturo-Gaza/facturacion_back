<?php

namespace App\Repositories\Catalogos;

use App\Interfaces\Catalogos\CatUnidadMedidasRepositoryInterface;
use App\Models\Catalogos\CatUnidadMedida;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CatUnidadMedidasRepository implements CatUnidadMedidasRepositoryInterface
{
    public function getAll()
    {
        return CatUnidadMedida::all();
    }

    public function getByID($id): ?CatUnidadMedida
    {
        return CatUnidadMedida::where('id', $id)->first();
    }

    public function store(array $data)
    {
        return CatUnidadMedida::create($data);
    }

    public function update(array $data, $id)
    {
        return CatUnidadMedida::where('id', $id)->update($data);
    }
    public function exportar($filtro)
    {
        $query = CatUnidadMedida::where('habilitado', true); // o cualquier otro modelo

        if (!empty($filtro)) {
            $query->where(function ($q) use ($filtro) {
                $q->where('descripcion_unidad_medida', 'ilike', "%$filtro%")
                    ->orWhere('clave_unidad_medida', 'ilike', "%$filtro%");
            });
        }
        $catUni = $query->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Cabeceras
        $sheet->setCellValue('A1', 'Clave');
        $sheet->setCellValue('B1', 'Descripción');

        // Datos
        $row = 2;
        foreach ($catUni as $uni) {
            $sheet->setCellValue("A{$row}", $uni->clave_unidad_medida);
            $sheet->setCellValue("B{$row}", $uni->descripcion_unidad_medida);
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
            'file_name' => 'CatUnidadMedida.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'base64' => $base64,
        ];
        return  $data;
    }
}
