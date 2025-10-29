<?php

namespace App\Interfaces\SistemaFacturacion;

use App\Models\MovimientoSaldo;

interface MovimientoSaldoRepositoryInterface
{
    public function getAll();
    public function getByID($id): ?MovimientoSaldo;
    public function getMyMovimientos($idUsr);
    public function exportExcel($idUsr);

    public function exportPdf($idUsr);
    public function store(array $data): MovimientoSaldo;
    public function update(array $data, $id): ?MovimientoSaldo;
}
