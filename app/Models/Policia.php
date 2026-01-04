<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Policia extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_placa',
        'numero_empleado',
        'nombre_completo',
        'rango',
        'area',
        'activo',
        'observaciones',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Relación con salidas
     */
    public function salidas(): HasMany
    {
        return $this->hasMany(Salida::class);
    }

    /**
     * Scope para policías activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
