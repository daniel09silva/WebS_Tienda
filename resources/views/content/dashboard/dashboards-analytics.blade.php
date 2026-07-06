@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('vendor-style')
@vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
@endsection

@section('vendor-script')
@vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
@endsection

@section('page-script')
<script>
  window.dashboardVentasAnuales = @json($serieAnual);
  window.dashboardYear = {{ $year }};
</script>
@vite('resources/assets/js/dashboards-analytics.js')
@endsection

@section('content')
<div class="row">
    <!-- Ventas del mes -->
    <div class="col-xxl-4 col-lg-5 mb-6">
        <div class="card h-100">
            <div class="card-body d-flex flex-column justify-content-between h-100">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="icon-base bx bx-money icon-lg"></i>
                            </span>
                        </div>
                        <span class="badge bg-label-primary text-capitalize">{{ $mesActual }}</span>
                    </div>
                    <p class="mb-1">Ventas del mes</p>
                    <h3 class="card-title mb-1">S/ {{ number_format($ventasDelMes, 2) }}</h3>
                    <small class="text-body-secondary">Suma de ventas pagadas desde el 1° del mes</small>
                </div>
                <a href="{{ route('reportes') }}" class="btn btn-sm btn-outline-primary mt-6">Ver reportes</a>
            </div>
        </div>
    </div>
    <!--/ Ventas del mes -->

    <!-- Gráfico anual de barras -->
    <div class="col-xxl-8 col-lg-7 mb-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="card-title mb-0">
                    <h5 class="m-0 me-2">Ventas por mes — {{ $year }}</h5>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ $year }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @foreach ($years as $y)
                            <li><a class="dropdown-item @if($y === $year) active @endif" href="{{ route('dashboard-analytics', ['year' => $y]) }}">{{ $y }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div id="totalRevenueChart" class="px-3"></div>
        </div>
    </div>
    <!--/ Gráfico anual de barras -->
</div>

<div class="row">
    <!-- Ventas recientes -->
    <div class="col-md-7 mb-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0 me-2">Ventas recientes</h5>
                <small class="text-body-secondary">Últimas 10</small>
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
                        @forelse ($ventasRecientes as $venta)
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
                                <td colspan="4" class="text-center text-body-secondary py-6">Sin ventas registradas todavía.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!--/ Ventas recientes -->

    <!-- Stock bajo -->
    <div class="col-md-5 mb-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0 me-2">Stock bajo</h5>
                <span class="badge bg-label-danger">{{ $stockBajo->count() }}</span>
            </div>
            <div class="card-body">
                <ul class="p-0 m-0">
                    @forelse ($stockBajo as $producto)
                        <li class="d-flex align-items-center mb-5">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-danger"><i class="icon-base bx bx-error"></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">{{ $producto->name }}</h6>
                                    <small>Alerta: {{ $producto->alerta_stock_bajo }} unid.</small>
                                </div>
                                <div class="user-progress">
                                    <h6 class="mb-0 text-danger">{{ $producto->stock_quantity }}</h6>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="text-center text-body-secondary py-6">Ningún producto por debajo del umbral de alerta.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
    <!--/ Stock bajo -->
</div>

<!-- Exportar ventas (rango libre) -->
<div class="row">
    <div class="col-12 mb-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title m-0">Exportar ventas</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('dashboard.export.excel') }}" method="GET" class="row g-3 align-items-end" target="_blank">
                    <div class="col-auto">
                        <label class="form-label" for="export-desde">Desde</label>
                        <input type="date" id="export-desde" name="from" class="form-control" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                    </div>
                    <div class="col-auto">
                        <label class="form-label" for="export-hasta">Hasta</label>
                        <input type="date" id="export-hasta" name="to" class="form-control" value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-outline-success">
                            <i class="icon-base bx bx-file me-1"></i> Excel (.xlsx)
                        </button>
                    </div>
                    <div class="col-auto">
                        <button type="submit" formaction="{{ route('dashboard.export.pdf') }}" class="btn btn-outline-danger">
                            <i class="icon-base bx bxs-file-pdf me-1"></i> PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
