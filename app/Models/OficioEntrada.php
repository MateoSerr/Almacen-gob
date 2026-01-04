<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OficioEntrada extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla en la base de datos
     */
    protected $table = 'oficios_entrada';

    protected $fillable = [
        'numero_oficio',
        'folio_completo',
        'fecha_oficio',
        'descripcion_material',
        'fecha_recepcion',
        'proveedor_nombre',
        'numero_factura',
        'importe_total',
        'importe_total_letra',
        'user_id',
    ];

    protected $casts = [
        'fecha_oficio' => 'date',
        'fecha_recepcion' => 'date',
        'importe_total' => 'decimal:2',
    ];

    /**
     * Relación con usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con entradas
     */
    public function entradas(): HasMany
    {
        return $this->hasMany(Entrada::class, 'oficio_entrada_id');
    }

    /**
     * Obtener la clave de ruta para el modelo
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }
}


