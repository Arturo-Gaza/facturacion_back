<?php

namespace App\Repositories\SistemaTickets;

use App\Interfaces\SistemaTickets\CatMonedaRepositoryInterface;
use App\Models\SistemaTickets\CatMoneda;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CatMonedaRepository implements CatMonedaRepositoryInterface
{
    public function getAll()
    {
        return CatMoneda::all();
    }

    public function getByID($id): ?CatMoneda
    {
        return CatMoneda::where('id_moneda', $id)->first();
    }

    public function store(array $data)
    {
        return CatMoneda::create($data);
    }

    public function update(array $data, $id)
    {
        return CatMoneda::where('id_moneda', $id)->update($data);
    }

    public function exportar($filtro)
    {

        $query  = CatMoneda::where('habilitado', true); // o cualquier otro modelo

        if (!empty($filtro)) {
            $query->where(function ($q) use ($filtro) {
                $q->where('clave_moneda', 'ilike', "%$filtro%") // PostgreSQL -> ilike para case-insensitive
                    ->orWhere('descripcion_moneda', 'ilike', "%$filtro%");
            });
        }

        $catMoneda = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Cabeceras
        $sheet->setCellValue('A1', 'Clave');
        $sheet->setCellValue('B1', 'Descripcion');

        // Datos
        $row = 2;
        foreach ($catMoneda as $moneda) {
            $sheet->setCellValue("A{$row}", $moneda->clave_moneda);
            $sheet->setCellValue("B{$row}", $moneda->descripcion_moneda);
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
            'file_name' => 'CatMoneda.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'base64' => $base64,
        ];
        return  $data;
    }
}
