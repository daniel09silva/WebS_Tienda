<?php

namespace App\Exports;

use App\Models\DetalleVenta;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VentasDetalleExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private Carbon $desde,
        private Carbon $hasta,
        private ?string $empresaId = null,
    ) {
    }

    public function collection(): Collection
    {
        return DetalleVenta::query()
            ->with(['venta', 'producto'])
            ->whereHas('venta', function ($query) {
                $query->pagadas()
                    ->whereBetween('fecha', [$this->desde, $this->hasta])
                    ->when($this->empresaId, fn ($q) => $q->where('empresa_id', $this->empresaId));
            })
            ->get()
            ->sortBy(fn (DetalleVenta $detalle) => $detalle->venta->fecha)
            ->values();
    }

    public function headings(): array
    {
        return ['Fecha', 'ID Venta', 'Producto', 'Cant', 'P.Unit', 'Subtotal', 'Método Pago'];
    }

    public function map($detalle): array
    {
        return [
            $detalle->venta->fecha->format('d/m/Y H:i'),
            $detalle->venta_id,
            $detalle->producto->name ?? 'Producto eliminado',
            $detalle->cantidad,
            number_format($detalle->precio_unitario, 2),
            number_format($detalle->subtotal, 2),
            $detalle->venta->metodo_pago,
        ];
    }
}
