<?php

namespace App\Http\Controllers\reportes;

use App\Exports\VentasDetalleExport;
use App\Http\Controllers\Controller;
use App\Models\DetalleVenta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class DashboardExport extends Controller
{
    public function excel(Request $request)
    {
        [$desde, $hasta, $empresaId] = $this->rango($request);

        return Excel::download(
            new VentasDetalleExport($desde, $hasta, $empresaId),
            "ventas_{$desde->format('Y-m-d')}_a_{$hasta->format('Y-m-d')}.xlsx"
        );
    }

    public function pdf(Request $request)
    {
        [$desde, $hasta, $empresaId] = $this->rango($request);

        $detalles = (new VentasDetalleExport($desde, $hasta, $empresaId))->collection();
        $total = $detalles->sum(fn (DetalleVenta $d) => (float) $d->subtotal);

        $pdf = Pdf::loadView('exports.ventas-pdf', [
            'detalles' => $detalles,
            'total' => $total,
            'desde' => $desde,
            'hasta' => $hasta,
        ])->setPaper('a4');

        return $pdf->download("ventas_{$desde->format('Y-m-d')}_a_{$hasta->format('Y-m-d')}.pdf");
    }

    private function rango(Request $request): array
    {
        $desde = $request->query('from')
            ? Carbon::parse($request->query('from'))->startOfDay()
            : now()->startOfMonth();

        $hasta = $request->query('to')
            ? Carbon::parse($request->query('to'))->endOfDay()
            : now()->endOfDay();

        $empresaId = optional($request->user()->profile)->empresa_id;

        return [$desde, $hasta, $empresaId];
    }
}
