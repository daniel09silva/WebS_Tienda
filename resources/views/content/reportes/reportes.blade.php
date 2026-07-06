@extends('layouts/contentNavbarLayout')

@section('title', 'Reportes')

@section('content')
<div class="row">
    <div class="col-12 mb-4 d-flex align-items-center justify-content-between">
        <h4 class="m-0">Reportes</h4>
        <a href="{{ route('reportes.export.excel') }}" class="btn btn-outline-success">
            <i class="icon-base bx bx-file me-1"></i> Exportar Excel (mes actual)
        </a>
    </div>
</div>

<!-- Resumen mensual -->
<div class="row">
    <div class="col-md-4 mb-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="avatar flex-shrink-0 mb-3">
                    <span class="avatar-initial rounded bg-label-primary"><i class="icon-base bx bx-money icon-lg"></i></span>
                </div>
                <p class="mb-1">Ventas del mes actual</p>
                <h4 class="mb-0">S/ {{ number_format($ventasDelMes, 2) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="avatar flex-shrink-0 mb-3">
                    <span class="avatar-initial rounded bg-label-info"><i class="icon-base bx bx-box icon-lg"></i></span>
                </div>
                <p class="mb-1">Total de productos</p>
                <h4 class="mb-0">{{ $totalProductos }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="avatar flex-shrink-0 mb-3">
                    <span class="avatar-initial rounded bg-label-danger"><i class="icon-base bx bx-error icon-lg"></i></span>
                </div>
                <p class="mb-1">Productos con stock bajo</p>
                <h4 class="mb-0">{{ $stockBajoCount }}</h4>
            </div>
        </div>
    </div>
</div>
<!--/ Resumen mensual -->

<!-- Flujo de ventas por período -->
<div class="row">
    <div class="col-12 mb-6">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title m-0">Flujo de ventas por período</h5>
                <div class="btn-group">
                    <a href="{{ route('reportes', ['periodo' => 'hoy']) }}" class="btn btn-sm {{ $periodo === 'hoy' ? 'btn-primary' : 'btn-outline-primary' }}">Hoy</a>
                    <a href="{{ route('reportes', ['periodo' => 'semana']) }}" class="btn btn-sm {{ $periodo === 'semana' ? 'btn-primary' : 'btn-outline-primary' }}">Semana</a>
                    <a href="{{ route('reportes', ['periodo' => 'mes']) }}" class="btn btn-sm {{ $periodo === 'mes' ? 'btn-primary' : 'btn-outline-primary' }}">Mes</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-6">
                    <div class="col-md-6">
                        <p class="mb-1">Total vendido</p>
                        <h4 class="mb-0">S/ {{ number_format($totalVendidoPeriodo, 2) }}</h4>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1">Artículos vendidos</p>
                        <h4 class="mb-0">{{ $articulosVendidosPeriodo }}</h4>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio unitario</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($detallePeriodo as $detalle)
                                <tr>
                                    <td>{{ $detalle->producto->name ?? 'Producto eliminado' }}</td>
                                    <td>{{ $detalle->cantidad }}</td>
                                    <td>S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                                    <td>S/ {{ number_format($detalle->subtotal, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-body-secondary py-6">Sin ventas en este período.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!--/ Flujo de ventas por período -->

<!-- Ventas recientes del mes -->
<div class="row">
    <div class="col-12 mb-6">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0">Ventas recientes del mes</h5>
                <small class="text-body-secondary">Hasta 20</small>
            </div>
            <div class="table-responsive">
                <table class="table table-borderless">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Método de pago</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ventasRecientesMes as $venta)
                            <tr>
                                <td>{{ $venta->fecha->format('d/m/Y H:i') }}</td>
                                <td>S/ {{ number_format($venta->total, 2) }}</td>
                                <td>{{ $venta->metodo_pago }}</td>
                                <td>
                                    <span class="badge bg-label-{{ $venta->estado === 'PAGADO' ? 'success' : 'warning' }}">
                                        {{ $venta->estado }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-body-secondary py-6">Sin ventas registradas este mes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!--/ Ventas recientes del mes -->
@endsection
