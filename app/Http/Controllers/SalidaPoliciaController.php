<?php

namespace App\Http\Controllers;

use App\Models\Salida;
use App\Models\Producto;
use App\Models\Policia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalidaPoliciaController extends Controller
{
    /**
     * Mostrar listado de salidas a policías
     */
    public function index(Request $request)
    {
        // Primero, corregir todas las salidas que tienen policia_id pero no están marcadas como entrega
        Salida::whereNotNull('policia_id')
            ->where('es_entrega_policia', false)
            ->update(['es_entrega_policia' => true]);
        
        // Limpiar cualquier mensaje de error previo relacionado con entregas
        if (session('error') && str_contains(session('error'), 'no es una entrega a policía')) {
            session()->forget('error');
        }
        
        // Mostrar TODAS las salidas que tienen policia_id O es_entrega_policia = true
        // Esto asegura que se muestren todas las entregas, incluso si es_entrega_policia no está marcado
        $query = Salida::with(['producto', 'policia', 'usuario'])
            ->where(function($q) {
                $q->where('es_entrega_policia', true)
                  ->orWhereNotNull('policia_id');
            });

        // Filtros
        if ($request->filled('policia_id')) {
            $query->where('policia_id', $request->policia_id);
        }

        if ($request->filled('producto_id')) {
            $query->where('producto_id', $request->producto_id);
        }

        if ($request->filled('fecha_desde')) {
            $query->where('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }

        $salidas = $query->latest('fecha')->paginate(20);
        
        // Obtener todos los policías que tienen entregas (activos o inactivos)
        $policiasConEntregas = Policia::whereHas('salidas', function($q) {
            $q->where(function($sq) {
                $sq->where('es_entrega_policia', true)
                   ->orWhereNotNull('policia_id');
            });
        })->orderBy('nombre_completo')->get();
        
        // También incluir policías activos para el filtro
        $policias = Policia::activos()->orderBy('nombre_completo')->get()->merge($policiasConEntregas)->unique('id');
        
        $productos = Producto::where('activo', true)
            ->where('es_uniforme', true)
            ->orderBy('nombre')
            ->get();

        return view('salidas-policia.index', compact('salidas', 'policias', 'productos'));
    }

    /**
     * Mostrar formulario para crear salida a policía
     */
    public function create()
    {
        $productos = Producto::where('activo', true)
            ->where('es_uniforme', true)
            ->orderBy('nombre')
            ->get();

        $policias = Policia::activos()->orderBy('nombre_completo')->get();

        return view('salidas-policia.create', compact('productos', 'policias'));
    }

    /**
     * Guardar nueva salida a policía
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'policia_id' => 'required|exists:policias,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
            'fecha' => 'required|date',
            'motivo' => 'nullable|string|max:255',
            'destino' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
            'entrega_nombre' => 'nullable|string|max:255',
            'entrega_firma' => 'nullable|string',
            'recibe_nombre' => 'nullable|string|max:255',
            'recibe_firma' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['es_entrega_policia'] = true;

        $salida = null;
        DB::transaction(function () use ($validated, &$salida) {
            // Verificar que el producto sea uniforme
            $producto = Producto::findOrFail($validated['producto_id']);
            
            if (!$producto->es_uniforme) {
                throw new \Exception('Este producto no está marcado como uniforme/equipo para policías.');
            }

            // Verificar stock disponible
            if ($producto->stock_actual < $validated['cantidad']) {
                throw new \Exception('No hay suficiente stock disponible.');
            }

            // VALIDACIÓN CRÍTICA: Verificar que no se haya entregado este producto a este policía antes
            $entregaExistente = Salida::where('es_entrega_policia', true)
                ->where('producto_id', $validated['producto_id'])
                ->where('policia_id', $validated['policia_id'])
                ->first();

            if ($entregaExistente) {
                $policia = Policia::findOrFail($validated['policia_id']);
                
                // Obtener todas las entregas del policía para mostrar qué productos ya tiene
                $entregasPolicia = Salida::where('es_entrega_policia', true)
                    ->where('policia_id', $validated['policia_id'])
                    ->with('producto')
                    ->get();
                
                $productosEntregados = $entregasPolicia->map(function($e) {
                    return $e->producto->nombre;
                })->implode(', ');
                
                throw new \Exception(
                    "⚠️ NO SE PUEDE REALIZAR LA SALIDA\n\n" .
                    "El producto \"{$producto->nombre}\" ya fue entregado anteriormente al policía:\n" .
                    "• Nombre: {$policia->nombre_completo}\n" .
                    "• Número de Empleado: " . ($policia->numero_empleado ?: 'N/A') . "\n\n" .
                    "Productos ya entregados a este policía:\n" .
                    "• {$productosEntregados}\n\n" .
                    "Cada producto solo puede entregarse una vez por policía. " .
                    "Si necesitas entregar otro producto diferente, selecciona un producto que aún no se haya entregado."
                );
            }

            // Generar folio único (continuación de salidas normales)
            $fecha = \Carbon\Carbon::parse($validated['fecha']);
            $year = $fecha->format('Y');
            
            // Contar salidas del año para generar número secuencial
            $ultimoNumero = Salida::whereYear('fecha', $year)->max('id') ?? 0;
            $numeroFolio = $ultimoNumero + 1;
            
            $validated['folio'] = 'FISCALÍA ESTATAL (FÍSICO).OUT/' . $year . '/' . str_pad($numeroFolio, 5, '0', STR_PAD_LEFT);
            
            // Verificar que el folio no exista (por si acaso)
            while (Salida::where('folio', $validated['folio'])->exists()) {
                $numeroFolio++;
                $validated['folio'] = 'FISCALÍA ESTATAL (FÍSICO).OUT/' . $year . '/' . str_pad($numeroFolio, 5, '0', STR_PAD_LEFT);
            }

            // Crear salida
            $salida = Salida::create($validated);

            // Actualizar stock del producto
            $producto->decrement('stock_actual', $validated['cantidad']);
        });

        // Recargar la salida para asegurar que tiene todos los datos
        $salida->refresh();
        
        // FORZAR que es_entrega_policia esté establecido correctamente (siempre debe ser true)
        $salida->es_entrega_policia = true;
        $salida->save();
        $salida->refresh();

        // Redirigir directamente a la página de impresión
        return redirect()->route('salidas-policia.imprimir', $salida)
            ->with('success', 'Salida a policía registrada exitosamente.');
    }

    /**
     * Mostrar detalle de salida a policía
     */
    public function show(Salida $salida)
    {
        // Recargar la salida para asegurar que tiene todos los datos actualizados
        $salida->refresh();
        
        // Si tiene policia_id, automáticamente es una entrega a policía - CORREGIRLO
        if ($salida->policia_id) {
            // Asegurar que esté marcado como entrega
            $salida->es_entrega_policia = true;
            $salida->save();
            $salida->refresh();
        } elseif (!$salida->es_entrega_policia) {
            // Solo mostrar error si realmente no es una entrega (no tiene policia_id ni está marcado)
            return redirect()->route('salidas-policia.index')
                ->with('error', 'Esta salida no es una entrega a policía.');
        }

        $salida->load('producto', 'policia', 'usuario');
        return view('salidas-policia.show', compact('salida'));
    }

    /**
     * Mostrar vista de impresión de salida a policía
     */
    public function imprimir(Salida $salida)
    {
        $salida->refresh();
        
        // Si tiene policia_id, automáticamente es una entrega a policía
        if ($salida->policia_id) {
            if (!$salida->es_entrega_policia) {
                $salida->es_entrega_policia = true;
                $salida->save();
                $salida->refresh();
            }
        } elseif (!$salida->es_entrega_policia) {
            return redirect()->route('salidas-policia.index')
                ->with('error', 'Esta salida no es una entrega a policía.');
        }

        $salida->load('producto', 'policia', 'usuario');
        return view('salidas-policia.imprimir', compact('salida'));
    }

    /**
     * Mostrar formulario para editar salida a policía
     */
    public function edit(Salida $salida)
    {
        $salida->refresh();
        
        // Si tiene policia_id, automáticamente es una entrega a policía
        if ($salida->policia_id) {
            if (!$salida->es_entrega_policia) {
                $salida->es_entrega_policia = true;
                $salida->save();
                $salida->refresh();
            }
        } elseif (!$salida->es_entrega_policia) {
            return redirect()->route('salidas-policia.index')
                ->with('error', 'Esta salida no es una entrega a policía.');
        }

        $policias = Policia::activos()->orderBy('nombre_completo')->get();
        $productos = Producto::where('activo', true)
            ->where('es_uniforme', true)
            ->orderBy('nombre')
            ->get();

        return view('salidas-policia.edit', compact('salida', 'policias', 'productos'));
    }

    /**
     * Actualizar salida a policía
     */
    public function update(Request $request, Salida $salida)
    {
        $salida->refresh();
        
        // Si tiene policia_id, automáticamente es una entrega a policía
        if ($salida->policia_id) {
            if (!$salida->es_entrega_policia) {
                $salida->es_entrega_policia = true;
                $salida->save();
                $salida->refresh();
            }
        } elseif (!$salida->es_entrega_policia) {
            return redirect()->route('salidas-policia.index')
                ->with('error', 'Esta salida no es una entrega a policía.');
        }

        $validated = $request->validate([
            'policia_id' => 'required|exists:policias,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
            'fecha' => 'required|date',
            'motivo' => 'nullable|string|max:255',
            'destino' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
            'entrega_nombre' => 'nullable|string|max:255',
            'entrega_firma' => 'nullable|string',
            'recibe_nombre' => 'nullable|string|max:255',
            'recibe_firma' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $salida) {
            // Verificar que el producto sea uniforme
            $producto = Producto::findOrFail($validated['producto_id']);
            
            if (!$producto->es_uniforme) {
                throw new \Exception('Este producto no está marcado como uniforme/equipo para policías.');
            }

            // Verificar duplicado (excluyendo la salida actual)
            $entregaExistente = Salida::where('es_entrega_policia', true)
                ->where('producto_id', $validated['producto_id'])
                ->where('policia_id', $validated['policia_id'])
                ->where('id', '!=', $salida->id)
                ->first();

            if ($entregaExistente) {
                $policia = Policia::findOrFail($validated['policia_id']);
                throw new \Exception(
                    "Ya se entregó este producto ({$producto->nombre}) al policía {$policia->nombre_completo} " .
                    "(Placa: {$policia->numero_placa}) anteriormente."
                );
            }

            // Revertir stock anterior
            $productoAnterior = Producto::findOrFail($salida->producto_id);
            $productoAnterior->increment('stock_actual', $salida->cantidad);

            // Verificar stock nuevo
            if ($producto->stock_actual < $validated['cantidad']) {
                throw new \Exception('No hay suficiente stock disponible.');
            }

            // Actualizar salida
            $salida->update($validated);

            // Actualizar stock nuevo
            $producto->decrement('stock_actual', $validated['cantidad']);
        });

        return redirect()->route('salidas-policia.index')
            ->with('success', 'Salida a policía actualizada exitosamente.');
    }

    /**
     * Eliminar salida a policía
     */
    public function destroy(Salida $salida)
    {
        $salida->refresh();
        
        // Si tiene policia_id, automáticamente es una entrega a policía
        if ($salida->policia_id) {
            if (!$salida->es_entrega_policia) {
                $salida->es_entrega_policia = true;
                $salida->save();
                $salida->refresh();
            }
        } elseif (!$salida->es_entrega_policia) {
            return redirect()->route('salidas-policia.index')
                ->with('error', 'Esta salida no es una entrega a policía.');
        }

        DB::transaction(function () use ($salida) {
            // Revertir stock
            $producto = Producto::findOrFail($salida->producto_id);
            $producto->increment('stock_actual', $salida->cantidad);

            // Eliminar salida
            $salida->delete();
        });

        return redirect()->route('salidas-policia.index')
            ->with('success', 'Salida a policía eliminada exitosamente.');
    }
}
