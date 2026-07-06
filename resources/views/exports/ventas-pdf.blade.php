<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #222; }
        h2 { margin-bottom: 2px; }
        p.subtitle { margin-top: 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 4px 6px; text-align: left; }
        th { background-color: #f0f0f0; }
        td.num, th.num { text-align: right; }
        tfoot td { font-weight: bold; background-color: #f7f7f7; }
    </style>
</head>
<body>
    <h2>Reporte de ventas</h2>
    <p class="subtitle">Desde {{ $desde->format('d/m/Y') }} hasta {{ $hasta->format('d/m/Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>ID Venta</th>
                <th>Producto</th>
                <th class="num">Cant</th>
                <th class="num">P.Unit</th>
                <th class="num">Subtotal</th>
                <th>Método Pago</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($detalles as $detalle)
                <tr>
                    <td>{{ $detalle->venta->fecha->format('d/m/Y H:i') }}</td>
                    <td>{{ $detalle->venta_id }}</td>
                    <td>{{ $detalle->producto->name ?? 'Producto eliminado' }}</td>
                    <td class="num">{{ $detalle->cantidad }}</td>
                    <td class="num">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td class="num">S/ {{ number_format($detalle->subtotal, 2) }}</td>
                    <td>{{ $detalle->venta->metodo_pago }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Sin ventas en este período.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5"></td>
                <td class="num">S/ {{ number_format($total, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
