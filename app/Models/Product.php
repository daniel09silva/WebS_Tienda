<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'products';

    protected $keyType = 'string';

    public $incrementing = false;

    const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'barcode',
        'description',
        'price',
        'stock_quantity',
        'alerta_stock_bajo',
        'category_id',
        'empresa_id',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'alerta_stock_bajo' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function detalleVentas(): HasMany
    {
        return $this->hasMany(DetalleVenta::class, 'producto_id');
    }

    public function scopeStockBajo($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'alerta_stock_bajo');
    }
}
