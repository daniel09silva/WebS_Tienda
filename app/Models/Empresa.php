<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    protected $table = 'empresas';

    protected $keyType = 'string';

    public $incrementing = false;

    const UPDATED_AT = null;

    protected $fillable = [
        'nombre',
        'ruc',
        'plan',
    ];

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }
}
