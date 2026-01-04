<?php

namespace App\Http\Controllers;

use App\Models\Entrada;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntradaController extends Controller
{
    /**
     * Mostrar listado de entradas con historial
     */
    public function index(Request $request)
    {
        $query = Entrada::with('producto');

        // Filtros
        if ($request->filled('producto_id')) {
            $query->where('producto_id', $request->producto_id);
        }

        if ($request->filled('fecha_desde')) {
            $query->where('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }

        $entradas = $query->latest('fecha')->paginate(20);
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();

        return view('entradas.index', compact('entradas', 'productos'));
    }

    /**
     * Mostrar formulario para crear entrada
     */
    public function create()
    {
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();
        return view('entradas.create', compact('productos'));
    }

    /**
     * Guardar nueva entrada
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
            'fecha' => 'required|date',
            'proveedor' => 'nullable|string|max:255',
            'numero_factura' => 'nullable|string|max:100',
            'precio_unitario' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'entrega_nombre' => 'nullable|string|max:255',
            'entrega_firma' => 'nullable|string',
            'recibe_nombre' => 'nullable|string|max:255',
            'recibe_firma' => 'nullable|string',
        ]);

        // Calcular total
        $validated['total'] = $validated['cantidad'] * $validated['precio_unitario'];
        $validated['user_id'] = auth()->id();

        $entrada = null;
        DB::transaction(function () use ($validated, &$entrada, $request) {
            // Subir imagen si existe
            if ($request->hasFile('imagen')) {
                $imagen = $request->file('imagen');
                $nombreImagen = time() . '_' . uniqid() . '.' . $imagen->getClientOriginalExtension();
                $imagen->move(public_path('storage/entradas'), $nombreImagen);
                $validated['imagen'] = 'storage/entradas/' . $nombreImagen;
            }

            // Generar folio único
            $fecha = \Carbon\Carbon::parse($validated['fecha']);
            $year = $fecha->format('Y');
            
            // Contar entradas del año para generar número secuencial
            $ultimoNumero = Entrada::whereYear('fecha', $year)->max('id') ?? 0;
            $numeroFolio = $ultimoNumero + 1;
            
            $validated['folio'] = 'FISCALÍA ESTATAL (FÍSICO).IN/' . $year . '/' . str_pad($numeroFolio, 5, '0', STR_PAD_LEFT);
            
            // Verificar que el folio no exista (por si acaso)
            while (Entrada::where('folio', $validated['folio'])->exists()) {
                $numeroFolio++;
                $validated['folio'] = 'FISCALÍA ESTATAL (FÍSICO).IN/' . $year . '/' . str_pad($numeroFolio, 5, '0', STR_PAD_LEFT);
            }
            
            // Crear entrada
            $entrada = Entrada::create($validated);

            // Actualizar stock del producto
            $producto = Producto::findOrFail($validated['producto_id']);
            $producto->increment('stock_actual', $validated['cantidad']);
        });

        return redirect()->route('entradas.show', $entrada)
            ->with('success', 'Entrada registrada exitosamente.')
            ->with('autoPrint', true);
    }

    /**
     * Mostrar detalle de entrada
     */
    public function show(Entrada $entrada)
    {
        $entrada->load('producto', 'usuario');
        return view('entradas.show', compact('entrada'));
    }

    /**
     * Mostrar vista de impresión de entrada
     */
    public function imprimir(Entrada $entrada)
    {
        $entrada->load('producto', 'usuario');
        return view('entradas.imprimir', compact('entrada'));
    }

    /**
     * Mostrar formulario para editar entrada
     */
    public function edit(Entrada $entrada)
    {
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();
        return view('entradas.edit', compact('entrada', 'productos'));
    }

    /**
     * Actualizar entrada
     */
    public function update(Request $request, Entrada $entrada)
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
            'fecha' => 'required|date',
            'proveedor' => 'nullable|string|max:255',
            'numero_factura' => 'nullable|string|max:100',
            'precio_unitario' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string',
            'entrega_nombre' => 'nullable|string|max:255',
            'entrega_firma' => 'nullable|string',
            'recibe_nombre' => 'nullable|string|max:255',
            'recibe_firma' => 'nullable|string',
        ]);

        $validated['total'] = $validated['cantidad'] * $validated['precio_unitario'];

        DB::transaction(function () use ($validated, $entrada) {
            // Revertir stock anterior
            $productoAnterior = Producto::findOrFail($entrada->producto_id);
            $productoAnterior->decrement('stock_actual', $entrada->cantidad);

            // Actualizar entrada
            $entrada->update($validated);

            // Actualizar stock nuevo
            $productoNuevo = Producto::findOrFail($validated['producto_id']);
            $productoNuevo->increment('stock_actual', $validated['cantidad']);
        });

        return redirect()->route('entradas.index')
            ->with('success', 'Entrada actualizada exitosamente.');
    }

    /**
     * Eliminar entrada
     */
    public function destroy(Entrada $entrada)
    {
        DB::transaction(function () use ($entrada) {
            // Revertir stock
            $producto = Producto::findOrFail($entrada->producto_id);
            $producto->decrement('stock_actual', $entrada->cantidad);

            // Eliminar entrada
            $entrada->delete();
        });

        return redirect()->route('entradas.index')
            ->with('success', 'Entrada eliminada exitosamente.');
    }
}



