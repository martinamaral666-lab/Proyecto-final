<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Pago</title>
    <style>
        body { font-family: sans-serif; padding: 20px; color: #333; }
        .box { border: 2px solid #065f46; border-radius: 8px; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #065f46; padding-bottom: 10px; }
        .title { font-size: 20px; font-weight: bold; color: #065f46; margin: 0; }
        .table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        .table th, .table td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .total { font-size: 18px; font-weight: bold; color: #065f46; text-align: right; margin-top: 20px; }
        .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>

    <div class="box">
        <div class="header">
            <h1 class="title">RECIBO DE PAGO</h1>
           <p>Fecha: {{ $cobro->created_at->timezone('America/Montevideo')->format('d/m/Y H:i') }}
             | N° #{{ str_pad($cobro->id, 6, '0', STR_PAD_LEFT) }}</p>
        </div>

        <table class="table">
            <tr>
                <th>Cliente:</th>
                <td>{{ $cobro->nombre_cliente }}</td>
            </tr>
            <tr>
                <th>Teléfono:</th>
                <td>{{ $cobro->telefono }}</td>
            </tr>
            <tr>
                <th>Concepto:</th>
                <td>{{ $cobro->concepto }}</td>
            </tr>
            <tr>
                <th>Mano de Obra:</th>
                <td>
                    @if($cobro->mano_de_obra === 'si')
                        Realizada
                    @else
                        No realizada (Motivo: {{ $cobro->motivo_no_realizado ?? 'N/A' }})
                    @endif
                </td>
            </tr>
        </table>

        <div class="total">
            Monto Total: ${{ number_format($cobro->monto, 2) }}
        </div>

        <div class="footer">
            <p>Gracias por su preferencia</p>
        </div>
    </div>

</body>
</html>
