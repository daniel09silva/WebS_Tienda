<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Venta;
use Illuminate\Http\Request;

class Analytics extends Controller
{
    public function index(Request $request)
    {
        $empresaId = optional($request->user()->profile)->empresa_id;

        $year = (int) $request->query('year', now()->year);
        $years = range(now()->year, now()->year - 4);

        $ventasQuery = fn () => Venta::query()->when($empresaId, fn ($q) => $q->where('empresa_id', $empresaId));
        $productsQuery = fn () => Product::query()->when($empresaId, fn ($q) => $q->where('empresa_id', $empresaId));

        $ventasDelMes = (clone $ventasQuery())
            ->pagadas()
            ->where('fecha', '>=', now()->startOfMonth())
            ->sum('total');

        $ventasPorMes = (clone $ventasQuery())
            ->pagadas()
            ->whereYear('fecha', $year)
            ->selectRaw('extract(month from fecha) as mes, sum(total) as total')
            ->groupBy('mes')
            ->pluck('total', 'mes');

        $serieAnual = collect(range(1, 12))
            ->map(fn ($mes) => (float) ($ventasPorMes[$mes] ?? 0))
            ->values();

        $ventasRecientes = (clone $ventasQuery())
            ->latest('fecha')
            ->take(10)
            ->get();

        $totalProductos = (clone $productsQuery())->count();

        $stockBajo = (clone $productsQuery())
            ->stockBajo()
            ->orderBy('stock_quantity')
            ->get();

        return view('content.dashboard.dashboards-analytics', [
            'ventasDelMes' => $ventasDelMes,
            'mesActual' => now()->translatedFormat('F Y'),
            'serieAnual' => $serieAnual,
            'year' => $year,
            'years' => $years,
            'ventasRecientes' => $ventasRecientes,
            'totalProductos' => $totalProductos,
            'stockBajo' => $stockBajo,
        ]);
    }
}
