<?php

namespace App\Repositories\Catalogos;

use App\Interfaces\Catalogos\CatRolesRepositoryInterface;
use App\Models\Catalogos\CatRoles;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CatRolesRepository implements CatRolesRepositoryInterface
{
    public function getAll()
    {
        return CatRoles::all();
    }
        public function getMesa()
    {
        return CatRoles::where('consola',true)->get();
    }

    public function getByID($id): ?CatRoles
    {
        return CatRoles::where('id', $id)->first();
    }

    public function store(array $data)
    {
        return CatRoles::create($data);
    }

    public function update(array $data, $id)
    {
        return CatRoles::where('id', $id)->update($data);
    }

    public function exportar($filtro)
    {
        $query = CatRoles::where('habilitado', true); // o cualquier otro modelo

        if (!empty($filtro)) {
            $query->where(function ($q) use ($filtro) {
                $q->where('nombre', 'ilike', "%$filtro%");
            });
        }
        $catRol = $query->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Cabeceras
        $sheet->setCellValue('A1', 'Descripcion');
        $sheet->setCellValue('B1', 'Requiere Marca o Modelo');

        // Datos
        $row = 2;
        foreach ($catRol as $rol) {
            $sheet->setCellValue("A{$row}", $rol->nombre);
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
            'file_name' => 'CatRoles.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'base64' => $base64,
        ];
        return  $data;
    }
}
