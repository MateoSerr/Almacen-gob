<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Salida extends Model
{
    use HasFactory;

    protected $fillable = [
        'folio',
        'producto_id',
        'cantidad',
        'fecha',
        'motivo',
        'destino',
        'observaciones',
        'entrega_nombre',
        'entrega_firma',
        'recibe_nombre',
        'recibe_firma',
        'user_id',
        'es_entrega_policia',
        'policia_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'cantidad' => 'integer',
        'es_entrega_policia' => 'boolean',
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
     * Relación con policía
     */
    public function policia(): BelongsTo
    {
        return $this->belongsTo(Policia::class);
    }
}



