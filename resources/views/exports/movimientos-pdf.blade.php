<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Movimientos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #2E86AB;
        }
        .info {
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #2E86AB;
            color: white;
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        td {
            padding: 6px;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .positive {
            color: #28a745;
        }
        .negative {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Movimientos</h1>
        <div class="info">
            <strong>Fecha de generación:</strong> {{ $fechaGeneracion }}<br>
            <strong>Total de movimientos:</strong> {{ $totalMovimientos }}<br>
            <strong>Monto total:</strong> ${{ number_format($totalMonto, 2) }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Monto</th>
                <th>Moneda</th>
                <th>Descripción</th>
                <th>Saldo Anterior</th>
                <th>Saldo Resultante</th>
                <th>Tipo</th>
                <th>Estatus</th>
                <th>Fecha Creación</th>
                <th>Tarjeta</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movimientos as $movimiento)
            <tr>
                <td>{{ $movimiento['id'] }}</td>
                <td class="text-right {{ $movimiento['monto'] >= 0 ? 'positive' : 'negative' }}">
                    ${{ number_format($movimiento['monto'], 2) }}
                </td>
                <td>{{ $movimiento['currency'] }}</td>
                <td>{{ $movimiento['descripcion'] }}</td>
                <td class="text-right">${{ number_format($movimiento['saldo_antes'], 2) }}</td>
                <td class="text-right">${{ number_format($movimiento['saldo_resultante'], 2) }}</td>
                <td>{{ $movimiento['tipo'] }}</td>
                <td>{{ $movimiento['estatus'] }}</td>
                <td>{{ $movimiento['fecha_creacion'] }}</td>
                <td class="text-center">
                    @if($movimiento['tarjeta'])
                        **** {{ $movimiento['tarjeta'] }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generado el {{ $fechaGeneracion }} | Página {PAGENO} de {nbpg}
    </div>
</body>
</html>