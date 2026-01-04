<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Entrada extends Model
{
    use HasFactory;

    protected $fillable = [
        'folio',
        'producto_id',
        'cantidad',
        'fecha',
        'proveedor',
        'numero_factura',
        'precio_unitario',
        'total',
        'observaciones',
        'imagen',
        'entrega_nombre',
        'entrega_firma',
        'recibe_nombre',
        'recibe_firma',
        'user_id',
        'oficio_entrada_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'precio_unitario' => 'decimal:2',
        'total' => 'decimal:2',
        'cantidad' => 'integer',
    ];

    /**
     * Relación con producto
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Relación con usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con oficio de entrada
     */
    public function oficioEntrada(): BelongsTo
    {
        return $this->belongsTo(OficioEntrada::class, 'oficio_entrada_id');
    }
}



