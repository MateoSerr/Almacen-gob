<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'unidad_medida',
        'precio_unitario',
        'stock_actual',
        'stock_minimo',
        'stock_maximo',
        'categoria',
        'observaciones',
        'activo',
        'es_uniforme',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'stock_actual' => 'integer',
        'stock_minimo' => 'integer',
        'stock_maximo' => 'integer',
        'activo' => 'boolean',
        'es_uniforme' => 'boolean',
    ];

    /**
     * Relación con entradas
     */
    public function entradas(): HasMany
    {
        return $this->hasMany(Entrada::class);
    }

    /**
     * Relación con salidas
     */
    public function salidas(): HasMany
    {
        return $this->hasMany(Salida::class);
    }

    /**
     * Verifica si el producto está pronto a acabar (stock < 100)
     */
    public function getProntoAcabarAttribute(): bool
    {
        return $this->stock_actual < 100;
    }

    /**
     * Verifica si el producto está por debajo del stock mínimo
     */
    public function getBajoStockMinimoAttribute(): bool
    {
        return $this->stock_actual < $this->stock_minimo;
    }
}



