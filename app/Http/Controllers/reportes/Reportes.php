<?php

namespace App\Http\Controllers\reportes;

use App\Exports\VentasDetalleExport;
use App\Http\Controllers\Controller;
use App\Models\DetalleVenta;
use App\Models\Product;
use App\Models\Venta;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class Reportes extends Controller
{
    public function index(Request $request)
    {
        $empresaId = optional($request->user()->profile)->empresa_id;

        $periodo = $request->query('periodo', 'mes');
        [$desde, $hasta] = $this->rangoParaPeriodo($periodo);

        $ventasQuery = fn () => Venta::query()->when($empresaId, fn ($q) => $q->where('empresa_id', $empresaId));
        $productsQuery = fn () => Product::query()->when($empresaId, fn ($q) => $q->where('empresa_id', $empresaId));

        // Resumen mensual
        $ventasDelMes = (clone $ventasQuery())
            ->pagadas()
            ->where('fecha', '>=', now()->startOfMonth())
            ->sum('total');

        $totalProductos = (clone $productsQuery())->count();
        $stockBajoCount = (clone $productsQuery())->stockBajo()->count();

        // Ventas recientes del mes (hasta 20)
        $ventasRecientesMes = (clone $ventasQuery())
            ->where('fecha', '>=', now()->startOfMonth())
            ->latest('fecha')
            ->take(20)
            ->get();

        // Flujo de ventas por período
        $totalVendidoPeriodo = (clone $ventasQuery())
            ->pagadas()
            ->whereBetween('fecha', [$desde, $hasta])
            ->sum('total');

        $ventaIdsPeriodo = (clone $ventasQuery())
            ->pagadas()
            ->whereBetween('fecha', [$desde, $hasta])
            ->pluck('id');

        $detallePeriodo = DetalleVenta::with('producto')
            ->whereIn('venta_id', $ventaIdsPeriodo)
            ->get();

        $articulosVendidosPeriodo = $detallePeriodo->sum('cantidad');

        return view('content.reportes.reportes', [
            'ventasDelMes' => $ventasDelMes,
            'totalProductos' => $totalProductos,
            'stockBajoCount' => $stockBajoCount,
            'ventasRecientesMes' => $ventasRecientesMes,
            'periodo' => $periodo,
            'totalVendidoPeriodo' => $totalVendidoPeriodo,
            'articulosVendidosPeriodo' => $articulosVendidosPeriodo,
            'detallePeriodo' => $detallePeriodo,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $empresaId = optional($request->user()->profile)->empresa_id;

        return Excel::download(
            new VentasDetalleExport(now()->startOfMonth(), now()->endOfMonth(), $empresaId),
            'ventas_' . now()->format('Y-m') . '.xlsx'
        );
    }

    private function rangoParaPeriodo(string $periodo): array
    {
        return match ($periodo) {
            'hoy' => [now()->startOfDay(), now()->endOfDay()],
            'semana' => [now()->startOfWeek(), now()->endOfWeek()],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }
}
