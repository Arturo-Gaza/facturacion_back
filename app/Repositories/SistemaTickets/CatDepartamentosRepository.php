<?php

namespace App\Repositories\SistemaTickets;

use App\Interfaces\SistemaTickets\CatDepartamentosRepositoryInterface;
use App\Models\SistemaTickets\CatDepartamentos;
use App\Models\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CatDepartamentosRepository implements CatDepartamentosRepositoryInterface
{
    public function getAll()
    {
        return CatDepartamentos::all();
    }

    public function getByID($id): ?CatDepartamentos
    {
        return CatDepartamentos::where('id', $id)->first();
    }

    public function store(array $data)
    {
        return CatDepartamentos::create($data);
    }

    public function update(array $data, $id)
    {
        return CatDepartamentos::where('id', $id)->update($data);
    }
    public function exportar($filtro)
    {
        $query = CatDepartamentos::where('habilitado', true); // o cualquier otro modelo

        if (!empty($filtro)) {
            $query->where(function ($q) use ($filtro) {
                $q->where('descripcion', 'ilike', "%$filtro%");
            });
        }
 $catDp = $query->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Cabeceras
        $sheet->setCellValue('A1', 'Descripcion');
        $sheet->setCellValue('B1', 'Usuario Responsable de compras');

        // Datos
        $row = 2;
        foreach ($catDp as $dep) {
            $usr = User::find($dep->id_usuario_responsable_compras);
            $sheet->setCellValue("A{$row}", $dep->descripcion);
            $sheet->setCellValue("B{$row}", $usr->name . " " . $usr->apellidoP . " " . $usr->apellidoM);
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
            'file_name' => 'CatDepartamento.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'base64' => $base64,
        ];
        return  $data;
    }
}
