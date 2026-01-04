<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Mostrar listado de productos con alertas
     */
    public function index(Request $request)
    {
        $query = Producto::query();

        // Filtrar por búsqueda
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('codigo', 'like', "%{$buscar}%")
                  ->orWhere('categoria', 'like', "%{$buscar}%");
            });
        }

        // Filtrar solo productos con stock bajo
        if ($request->filled('solo_alerta')) {
            $query->where('stock_actual', '<', 100);
        }

        $productos = $query->orderBy('nombre')->paginate(20);

        // Obtener productos con alerta (stock < 100)
        $productosAlerta = Producto::where('stock_actual', '<', 100)
            ->where('activo', true)
            ->orderBy('stock_actual', 'asc')
            ->get();

        return view('productos.index', compact('productos', 'productosAlerta'));
    }

    /**
     * Mostrar inventario completo de productos
     */
    public function inventario(Request $request)
    {
        $query = Producto::where('activo', true);

        // Filtrar por búsqueda
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('codigo', 'like', "%{$buscar}%")
                  ->orWhere('categoria', 'like', "%{$buscar}%");
            });
        }

        // Filtrar por categoría
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        // Filtrar solo productos con stock bajo
        if ($request->filled('solo_alerta')) {
            $query->where('stock_actual', '<', 100);
        }

        // Ordenamiento
        $orden = $request->get('orden', 'nombre');
        $direccion = $request->get('direccion', 'asc');
        $query->orderBy($orden, $direccion);

        $productos = $query->paginate(50);

        // Estadísticas
        $totalProductos = Producto::where('activo', true)->count();
        $productosAlerta = Producto::where('stock_actual', '<', 100)->where('activo', true)->count();
        $valorTotal = Producto::where('activo', true)->whereNotNull('precio_unitario')->get()->sum(function($p) {
            return $p->stock_actual * $p->precio_unitario;
        });

        // Stock total agrupado por unidad de medida (no tiene sentido sumar diferentes unidades)
        $stockPorUnidad = Producto::where('activo', true)
            ->whereNotNull('unidad_medida')
            ->selectRaw('unidad_medida, SUM(stock_actual) as total')
            ->groupBy('unidad_medida')
            ->orderBy('unidad_medida')
            ->get();

        // Categorías para filtro
        $categorias = Producto::where('activo', true)->whereNotNull('categoria')->distinct()->pluck('categoria')->sort();

        return view('productos.inventario', compact('productos', 'totalProductos', 'productosAlerta', 'valorTotal', 'categorias', 'stockPorUnidad'));
    }

    /**
     * Mostrar formulario para crear producto
     */
    public function create()
    {
        return view('productos.create');
    }

    /**
     * Guardar nuevo producto
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|unique:productos,codigo|max:50',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'unidad_medida' => 'required|string|max:50',
            'precio_unitario' => 'nullable|numeric|min:0',
            'stock_actual' => 'nullable|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'stock_maximo' => 'nullable|integer|min:0',
            'categoria' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string',
        ]);

        Producto::create($validated);

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    /**
     * Mostrar detalle de producto
     */
    public function show(Producto $producto)
    {
        $producto->load(['entradas' => function($query) {
            $query->latest('fecha')->limit(10);
        }, 'salidas' => function($query) {
            $query->latest('fecha')->limit(10);
        }]);

        return view('productos.show', compact('producto'));
    }

    /**
     * Mostrar formulario para editar producto
     */
    public function edit(Producto $producto)
    {
        return view('productos.edit', compact('producto'));
    }

    /**
     * Actualizar producto
     */
    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|unique:productos,codigo,' . $producto->id . '|max:50',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'unidad_medida' => 'required|string|max:50',
            'precio_unitario' => 'nullable|numeric|min:0',
            'stock_actual' => 'nullable|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'stock_maximo' => 'nullable|integer|min:0',
            'categoria' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        $producto->update($validated);

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    /**
     * Eliminar producto
     */
    public function destroy(Producto $producto)
    {
        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }

    /**
     * Buscar productos por nombre (API)
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $all = $request->get('all', false);
        $uniforme = $request->get('uniforme', false);
        
        $productosQuery = Producto::where('activo', true);
        
        // Filtrar solo uniformes si se solicita
        if ($uniforme) {
            $productosQuery->where('es_uniforme', true);
        }
        
        // Si hay query, filtrar; si no, mostrar todos
        if (!empty($query) && !$all) {
            $queryUpper = strtoupper(trim($query));
            // Si se busca "BOTA", "BOTAS" o "BOT", buscar específicamente "BOTA TIPO TACTICO" primero
            if (strpos($queryUpper, 'BOT') === 0 || $queryUpper === 'BOTA' || $queryUpper === 'BOTAS') {
                $productosQuery->where(function($q) use ($query) {
                    // Incluir "BOTA TIPO TACTICO" y otros productos que contengan "BOTA"
                    $q->where('nombre', 'like', '%BOTA%')
                      ->orWhere('codigo', 'like', "%{$query}%");
                });
            } else {
                $productosQuery->where(function($q) use ($query) {
                    $q->where('nombre', 'like', "%{$query}%")
                      ->orWhere('codigo', 'like', "%{$query}%");
                });
            }
        }
        
        // Ordenar para que "BOTA TIPO TACTICO" aparezca primero cuando se busque "BOTA"
        $productos = $productosQuery->orderByRaw("CASE WHEN nombre LIKE '%BOTA TIPO TACTICO%' THEN 0 ELSE 1 END")
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'codigo', 'stock_actual']);

        return response()->json($productos);
    }

    /**
     * Crear producto rápido desde API (para usar en formularios de entradas/salidas)
     */
    public function quickCreate(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|unique:productos,codigo|max:50',
            'nombre' => 'required|string|max:255',
            'unidad_medida' => 'required|string|max:50',
            'descripcion' => 'nullable|string',
            'categoria' => 'nullable|string|max:100',
            'precio_unitario' => 'nullable|numeric|min:0',
            'stock_actual' => 'nullable|integer|min:0',
        ]);

        // Valores por defecto
        $validated['stock_actual'] = $validated['stock_actual'] ?? 0;
        $validated['stock_minimo'] = 100;
        $validated['activo'] = true;

        $producto = Producto::create($validated);

        return response()->json([
            'success' => true,
            'producto' => [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'codigo' => $producto->codigo,
                'stock_actual' => $producto->stock_actual,
            ]
        ]);
    }
}



