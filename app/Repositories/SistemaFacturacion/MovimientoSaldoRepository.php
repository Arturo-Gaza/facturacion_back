<?php

namespace App\Repositories\SistemaFacturacion;


use App\Interfaces\SistemaFacturacion\MovimientoSaldoRepositoryInterface;
use App\Models\MovimientoSaldo;
use App\Models\User;
use Stripe\Stripe;
use Barryvdh\DomPDF\Facade\Pdf;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;


class MovimientoSaldoRepository implements MovimientoSaldoRepositoryInterface
{
    public function getAll()
    {
        return MovimientoSaldo::get();
    }

    public function getByID($id): ?MovimientoSaldo
    {
        return MovimientoSaldo::find($id);
    }

    public function exportExcel($idUsr)
    {
        $movimientos = $this->getMyMovimientos($idUsr);
        if ($movimientos->isEmpty()) {
            return response()->json(['error' => 'No hay movimientos para exportar'], 404);
        }

        $fileName = 'movimientos_' . $idUsr . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        $writer = WriterEntityFactory::createXLSXWriter();
        $tempFile = sys_get_temp_dir() . '/' . $fileName;
        $writer->openToFile($tempFile);

        // encabezados
        $headerRow = WriterEntityFactory::createRowFromArray([
            'ID',
            'Monto',
            'Moneda',
            'Descripción',
            'Saldo Anterior',
            'Saldo Resultante',
            'Tipo',
            'Estatus',
            'Método de Pago',
            'Fecha Creación',
            'Fecha Procesado',
            'Últimos 4 dígitos',
            'Marca Tarjeta',
            'Tipo Método Pago'
        ]);
        $writer->addRow($headerRow);

        foreach ($movimientos as $mov) {
            $get = fn($k) => is_array($mov) ? ($mov[$k] ?? '') : ($mov->$k ?? '');
            $row = [
                $get('id'),
                $get('monto'),
                $get('currency'),
                $get('descripcion'),
                $get('saldo_antes'),
                $get('saldo_resultante'),
                $get('tipo'),
                $get('estatus'),
                $get('payment_method'),
                $get('fecha_creacion'),
                $get('fecha_procesado'),
                $get('tarjeta'),
                $get('card_brand'),
                $get('payment_method_type'),
            ];
            $writer->addRow(WriterEntityFactory::createRowFromArray($row));
        }

        $writer->close();
        return $tempFile;
    }

    // Exportar a PDF
    public function exportPdf($idUsr)
    {
        $movimientos = $this->getMyMovimientos($idUsr);

        $movimientos = $movimientos->map(function ($mov) {
            $convert = fn($val) => is_string($val) ? mb_convert_encoding($val, 'UTF-8', 'UTF-8') : $val;

            if (is_array($mov)) {
                return array_map($convert, $mov);
            } else {
                // si es Eloquent Model
                foreach ($mov->getAttributes() as $key => $val) {
                    $mov->$key = $convert($val);
                }
                return $mov;
            }
        });
        // Si no hay movimientos, retornar un error o mensaje
        if ($movimientos->isEmpty()) {
            return response()->json(['error' => 'No hay movimientos para exportar'], 404);
        }

        // Generar nombre del archivo
        $fileName = 'movimientos_' . $idUsr . '_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        $data = [
            'movimientos' => $movimientos,
            'titulo' => 'Reporte de Movimientos',
            'fechaGeneracion' => now()->format('d/m/Y H:i:s'),
            'totalMovimientos' => $movimientos->count(),
            'totalMonto' => $movimientos->sum('monto')
        ];



        $pdf = PDF::loadView('exports.movimientos-pdf', $data)
            ->setPaper('a4', 'landscape')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);


        return ["pdf" => $pdf, "fileName" => $fileName];
    }

    public function getMyMovimientos($idUsr)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $movimientosCollection = MovimientoSaldo::where('usuario_id', $idUsr)
            ->with(['estatusMovimiento' => function ($query) {
                $query->select('id', 'nombre');
            }])
            ->get();

        $user = User::find($idUsr);
        $saldo = $user->saldo;

        $result = $movimientosCollection->map(function ($movimiento) use (&$paymentMethodCache, &$intentCache, &$balanceTxCache) {
            // base
            $item = [
                'id' => $movimiento->id,
                'monto' => $movimiento->monto,
                'currency' => $movimiento->currency,
                'descripcion' => $movimiento->descripcion,
                'saldo_antes' => $movimiento->saldo_antes,
                'saldo_resultante' => $movimiento->saldo_resultante,
                'tipo' => $movimiento->tipo,
                'estatus' => $movimiento->estatusMovimiento->nombre ?? null,
                'payment_method' => $movimiento->payment_method,
                'fecha_creacion' => $movimiento->created_at ? $movimiento->created_at->format('Y-m-d H:i:s') : null,
                'fecha_procesado' => $movimiento->processed_at ? $movimiento->processed_at->format('Y-m-d H:i:s') : null,
                'tarjeta' => $movimiento->card_last4,
                'card_brand' => $movimiento->card_brand,
                'payment_method_type' => $movimiento->payment_method_type,
            ];


            return $item;
        });
        $data = [
            "movimientos" => $result,
            "saldo_resultante" => $saldo
        ];
        return $data;
    }

    public function store(array $data): MovimientoSaldo
    {
        return MovimientoSaldo::create($data);
    }

    public function update(array $data, $id): ?MovimientoSaldo
    {
        $movimiento = MovimientoSaldo::find($id);
        if ($movimiento) {
            $movimiento->update($data);
        }
        return $movimiento;
    }
}
