<?php

namespace App\Http\Controllers;

use App\Models\Policia;
use Illuminate\Http\Request;

class PoliciaController extends Controller
{
    /**
     * Mostrar listado de policías
     */
    public function index(Request $request)
    {
        $query = Policia::query();

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre_completo', 'like', "%{$buscar}%")
                  ->orWhere('numero_placa', 'like', "%{$buscar}%")
                  ->orWhere('numero_empleado', 'like', "%{$buscar}%")
                  ->orWhere('rango', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('activo')) {
            $query->where('activo', $request->activo === '1');
        }

        $policias = $query->orderBy('nombre_completo')->paginate(20);

        return view('policias.index', compact('policias'));
    }

    /**
     * Mostrar formulario para crear policía
     */
    public function create()
    {
        return view('policias.create');
    }

    /**
     * Guardar nuevo policía
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_placa' => 'required|string|unique:policias,numero_placa|max:50',
            'numero_empleado' => 'nullable|string|unique:policias,numero_empleado|max:50',
            'nombre_completo' => 'required|string|max:255',
            'rango' => 'nullable|string|max:100',
            'area' => 'nullable|string|max:100',
            'activo' => 'boolean',
            'observaciones' => 'nullable|string',
        ]);

        $validated['activo'] = $validated['activo'] ?? true;

        Policia::create($validated);

        return redirect()->route('policias.index')
            ->with('success', 'Policía registrado exitosamente.');
    }

    /**
     * Mostrar detalle de policía
     */
    public function show(Policia $policia)
    {
        $policia->load('salidas.producto');
        return view('policias.show', compact('policia'));
    }

    /**
     * Mostrar formulario para editar policía
     */
    public function edit(Policia $policia)
    {
        return view('policias.edit', compact('policia'));
    }

    /**
     * Actualizar policía
     */
    public function update(Request $request, Policia $policia)
    {
        $validated = $request->validate([
            'numero_placa' => 'required|string|unique:policias,numero_placa,' . $policia->id . '|max:50',
            'numero_empleado' => 'nullable|string|unique:policias,numero_empleado,' . $policia->id . '|max:50',
            'nombre_completo' => 'required|string|max:255',
            'rango' => 'nullable|string|max:100',
            'area' => 'nullable|string|max:100',
            'activo' => 'boolean',
            'observaciones' => 'nullable|string',
        ]);

        $policia->update($validated);

        return redirect()->route('policias.index')
            ->with('success', 'Policía actualizado exitosamente.');
    }

    /**
     * Eliminar policía
     */
    public function destroy(Policia $policia)
    {
        // Verificar si tiene salidas asociadas
        if ($policia->salidas()->count() > 0) {
            return redirect()->route('policias.index')
                ->with('error', 'No se puede eliminar el policía porque tiene entregas asociadas.');
        }

        $policia->delete();

        return redirect()->route('policias.index')
            ->with('success', 'Policía eliminado exitosamente.');
    }

    /**
     * Buscar policías por nombre, placa o número de empleado (API)
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $all = $request->get('all', false);
        $empleado = $request->get('empleado', false);
        $conEntregas = $request->get('con_entregas', false);
        
        $policiasQuery = Policia::where('activo', true);
        
        // Si se solicita solo policías con entregas
        if ($conEntregas) {
            $policiasQuery->whereHas('salidas', function($q) {
                $q->where('es_entrega_policia', true);
            });
        }
        
        // Si hay query, filtrar; si no, mostrar todos
        if (!empty($query) && !$all) {
            if ($empleado) {
                // Búsqueda específica por número de empleado
                $policiasQuery->where('numero_empleado', 'like', "%{$query}%");
            } else {
                // Búsqueda general por nombre, placa, rango
                $policiasQuery->where(function($q) use ($query) {
                    $q->where('nombre_completo', 'like', "%{$query}%")
                      ->orWhere('numero_placa', 'like', "%{$query}%")
                      ->orWhere('rango', 'like', "%{$query}%");
                });
            }
        }
        
        $policias = $policiasQuery->orderBy('nombre_completo')
            ->get(['id', 'nombre_completo', 'numero_placa', 'numero_empleado', 'rango', 'area']);
        
        return response()->json($policias);
    }

    /**
     * Verificar entregas por número de empleado
     */
    public function verificarEntregas(Request $request)
    {
        $numeroEmpleado = $request->get('numero_empleado');
        
        if (!$numeroEmpleado) {
            return response()->json([
                'success' => false,
                'message' => 'Número de empleado requerido'
            ], 400);
        }
        
        $policia = Policia::where('numero_empleado', $numeroEmpleado)->first();
        
        if (!$policia) {
            return response()->json([
                'success' => true,
                'registrado' => false,
                'entregas' => []
            ]);
        }
        
        $entregas = \App\Models\Salida::where('es_entrega_policia', true)
            ->where('policia_id', $policia->id)
            ->with('producto')
            ->get(['id', 'producto_id', 'cantidad', 'fecha', 'folio']);
        
        return response()->json([
            'success' => true,
            'registrado' => true,
            'policia' => [
                'id' => $policia->id,
                'nombre_completo' => $policia->nombre_completo,
                'numero_placa' => $policia->numero_placa,
                'numero_empleado' => $policia->numero_empleado,
            ],
            'entregas' => $entregas->map(function($entrega) {
                return [
                    'id' => $entrega->id,
                    'producto_id' => $entrega->producto_id,
                    'producto' => $entrega->producto->nombre,
                    'cantidad' => $entrega->cantidad,
                    'fecha' => $entrega->fecha->format('d/m/Y'),
                    'folio' => $entrega->folio,
                ];
            })
        ]);
    }

    /**
     * Crear policía rápido desde API (para usar en formularios de salidas)
     */
    public function quickCreate(Request $request)
    {
        try {
            // Verificar primero si el número de empleado ya existe
            $numeroEmpleado = $request->input('numero_empleado');
            $policiaExistente = Policia::where('numero_empleado', $numeroEmpleado)->first();
            
            if ($policiaExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'El número de empleado ya está registrado.',
                    'policia_existente' => [
                        'id' => $policiaExistente->id,
                        'nombre_completo' => $policiaExistente->nombre_completo,
                        'numero_empleado' => $policiaExistente->numero_empleado,
                    ]
                ], 422);
            }
            
            $validated = $request->validate([
                'numero_placa' => 'required|string|unique:policias,numero_placa|max:50',
                'numero_empleado' => 'required|string|unique:policias,numero_empleado|max:50',
                'nombre_completo' => 'required|string|max:255',
                'rango' => 'nullable|string|max:100',
                'area' => 'nullable|string|max:100',
            ]);

            // Valores por defecto
            $validated['activo'] = true;

            $policia = Policia::create($validated);

            return response()->json([
                'success' => true,
                'policia' => [
                    'id' => $policia->id,
                    'nombre_completo' => $policia->nombre_completo,
                    'numero_placa' => $policia->numero_placa,
                    'numero_empleado' => $policia->numero_empleado,
                    'rango' => $policia->rango,
                    'area' => $policia->area,
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación: ' . implode(', ', $e->errors()['numero_empleado'] ?? []),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el policía: ' . $e->getMessage()
            ], 500);
        }
    }
}
