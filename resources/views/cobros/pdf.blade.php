@php
    // "productos" se guarda como JSON; se normaliza por si el modelo no lo convierte a array.
    $productos = is_array($cobro->productos)
        ? $cobro->productos
        : (json_decode($cobro->productos ?? '[]', true) ?: []);

    $fecha = $cobro->created_at->timezone('America/Montevideo')->format('d/m/Y H:i');
    $numero = str_pad($cobro->id, 6, '0', STR_PAD_LEFT);

    if ($cobro->tipo_registro === 'venta') {
        $estadoManoObra = 'No corresponde';
    } elseif ($cobro->mano_de_obra === 'si') {
        $estadoManoObra = 'Realizada';
    } else {
        $estadoManoObra = 'No realizada (' . ($cobro->motivo_no_realizado ?: 'N/A') . ')';
    }

    $subtotalProductos = array_sum(array_map(fn ($p) => (float) $p['subtotal'], $productos));
    $montoManual = (float) ($cobro->monto_mano_obra ?? 0);
    $etiquetaManual = $cobro->tipo_registro === 'venta' ? 'Servicio / monto adicional' : 'Mano de obra';
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo #{{ $numero }}</title>
    <style>
        body { font-family: Helvetica, sans-serif; color: #333333; margin: 0; padding: 20px; }
        .recibo { border: 2px solid #065f46; border-radius: 12px; padding: 26px 26px 20px 26px; }
        .titulo { text-align: center; color: #065f46; font-size: 22px; font-weight: bold; margin: 0; }
        .subtitulo { text-align: center; font-size: 13px; margin: 14px 0 18px 0; }
        .linea { border: 0; border-top: 2px solid #065f46; margin: 0 0 6px 0; }
        table { width: 100%; border-collapse: collapse; }
        .datos td { padding: 11px 12px; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
        .datos td.etiqueta { width: 40%; font-weight: bold; }

        .seccion { color: #065f46; font-size: 13px; font-weight: bold; text-transform: uppercase; margin: 22px 0 6px 0; }
        .productos { table-layout: fixed; }
        .productos th { background: #f1f5f9; padding: 8px 10px; font-size: 12px; color: #475569; text-align: left; }
        .productos td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; font-size: 13px; text-align: left; }
        /* Más específicos que ".productos th": si no, el encabezado quedaba a la izquierda
           y los valores a la derecha, y las columnas se veían desalineadas. */
        .productos .centro { text-align: center; }
        .productos .derecha { text-align: right; }
        .productos .col-producto { width: 38%; }
        .productos .col-cant { width: 14%; }
        .productos .col-precio { width: 24%; }
        .productos .col-subtotal { width: 24%; }

        .resumen { margin-top: 12px; }
        .resumen td { padding: 3px 10px; font-size: 13px; text-align: right; }
        .resumen td.valor { width: 110px; }

        .total { text-align: right; color: #065f46; font-size: 17px; font-weight: bold; margin-top: 18px; padding-right: 10px; }
        .gracias { text-align: center; color: #6b7280; font-size: 11px; margin-top: 34px; }
    </style>
</head>
<body>
    <div class="recibo">
        <p class="titulo">RECIBO DE PAGO</p>
        <p class="subtitulo">Fecha: {{ $fecha }} | N&deg; #{{ $numero }}</p>
        <hr class="linea">

        <table class="datos">
            <tr><td class="etiqueta">Cliente:</td><td>{{ $cobro->nombre_cliente }}</td></tr>
            <tr><td class="etiqueta">Tel&eacute;fono:</td><td>{{ $cobro->telefono }}</td></tr>
            <tr><td class="etiqueta">Concepto:</td><td>{{ $cobro->concepto }}</td></tr>
            <tr><td class="etiqueta">Mano de Obra:</td><td>{{ $estadoManoObra }}</td></tr>
        </table>

        @if (count($productos) > 0)
            <p class="seccion">Productos</p>
            <table class="productos">
                <thead>
                    <tr>
                        <th class="col-producto">Producto</th>
                        <th class="col-cant centro">Cant.</th>
                        <th class="col-precio derecha">Precio unitario</th>
                        <th class="col-subtotal derecha">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productos as $producto)
                        <tr>
                            <td>{{ $producto['nombre_item'] }}</td>
                            <td class="centro">{{ $producto['cantidad'] }}</td>
                            <td class="derecha">${{ number_format((float) $producto['precio_unitario'], 2) }}</td>
                            <td class="derecha">${{ number_format((float) $producto['subtotal'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if (count($productos) > 0 || $montoManual > 0)
            <table class="resumen">
                @if (count($productos) > 0)
                    <tr>
                        <td>Subtotal productos</td>
                        <td class="valor">${{ number_format($subtotalProductos, 2) }}</td>
                    </tr>
                @endif
                @if ($montoManual > 0)
                    <tr>
                        <td>{{ $etiquetaManual }}</td>
                        <td class="valor">${{ number_format($montoManual, 2) }}</td>
                    </tr>
                @endif
            </table>
        @endif

        <p class="total">Monto Total: ${{ number_format((float) $cobro->monto, 2) }}</p>

        <p class="gracias">Gracias por su preferencia</p>
    </div>
</body>
</html>
