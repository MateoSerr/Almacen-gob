<?php

namespace App\Http\Controllers;

use App\Models\Salida;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;

class SalidaController extends Controller
{
    /**
     * Mostrar listado de salidas con historial
     */
    public function index(Request $request)
    {
        $query = Salida::with('producto');

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

        $salidas = $query->latest('fecha')->paginate(20);
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();

        return view('salidas.index', compact('salidas', 'productos'));
    }

    /**
     * Mostrar formulario para crear salida
     */
    public function create()
    {
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();
        return view('salidas.create', compact('productos'));
    }

    /**
     * Guardar nueva salida
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
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

        $salida = null;
        DB::transaction(function () use ($validated, &$salida) {
            // Verificar stock disponible
            $producto = Producto::findOrFail($validated['producto_id']);
            
            if ($producto->stock_actual < $validated['cantidad']) {
                throw new \Exception('No hay suficiente stock disponible.');
            }

            // Generar folio único
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

        return redirect()->route('salidas.show', $salida)
            ->with('success', 'Salida registrada exitosamente.')
            ->with('autoPrint', true);
    }

    /**
     * Mostrar detalle de salida
     */
    public function show(Salida $salida)
    {
        $salida->load('producto', 'usuario');
        return view('salidas.show', compact('salida'));
    }

    /**
     * Mostrar vista de impresión de salida
     */
    public function imprimir(Salida $salida)
    {
        // Asegurar que se carguen las relaciones necesarias
        if (!$salida->relationLoaded('producto')) {
            $salida->load('producto');
        }
        if (!$salida->relationLoaded('usuario')) {
            $salida->load('usuario');
        }
        
        return view('salidas.imprimir', compact('salida'));
    }

    /**
     * Descargar documento Word de la salida
     */
    public function descargarWord(Salida $salida)
    {
        $salida->load('producto', 'usuario');
        
        try {
            // Ruta del documento plantilla (usaremos la misma plantilla o una específica para salidas)
            // Si tienes una plantilla específica para salidas, cámbiala aquí
            $templatePath = base_path('FORMATO-WORD-SALIDA.docx');
            
            // Si no existe plantilla específica, usar la de entradas como fallback
            if (!file_exists($templatePath)) {
                $templatePath = base_path('FORMATO-WORD.docx');
            }
            
            // Verificar que el archivo existe
            if (!file_exists($templatePath)) {
                \Log::error('Plantilla Word no encontrada en: ' . $templatePath);
                return back()->withErrors(['error' => 'No se encontró el archivo plantilla Word.']);
            }
            
            // Crear el procesador de plantilla
            $templateProcessor = new TemplateProcessor($templatePath);
            
            // Formatear fechas
            $meses = [
                1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
                5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
                9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
            ];
            
            $fechaSalida = $salida->fecha;
            $diaSalida = $fechaSalida->format('d');
            $mesSalida = $meses[(int)$fechaSalida->format('m')];
            $anioSalida = $fechaSalida->format('Y');
            
            // Ciudad del oficio (configurable)
            $ciudadOficio = config('app.oficio_lugar', 'Guadalajara, Jal.');
            $fechaTexto = sprintf(
                '%s, %s de %s del %s.',
                $ciudadOficio,
                ltrim($diaSalida, '0'),
                $mesSalida,
                $anioSalida
            );
            
            // Extraer número de oficio del folio (ej: FISCALÍA ESTATAL (FÍSICO).OUT/2025/00001 -> 00001)
            $numeroOficio = '00001';
            if ($salida->folio) {
                $partes = explode('/', $salida->folio);
                if (count($partes) > 0) {
                    $numeroOficio = end($partes);
                }
            }
            
            // Folio completo para mostrar
            $folioCompleto = $salida->folio ?? 'FISCALÍA ESTATAL (FÍSICO).OUT/' . $anioSalida . '/' . str_pad($salida->id, 5, '0', STR_PAD_LEFT);
            
            // Descripción del producto con cantidad (formato como en la vista)
            $descripcionProducto = sprintf(
                '(%s %s) %s',
                number_format($salida->cantidad, 0, '.', ','),
                strtoupper($salida->producto->unidad_medida ?? 'PIEZA'),
                strtoupper($salida->producto->nombre)
            );
            
            // Fecha de recepción (para salidas, usamos la fecha de salida)
            $fechaRecepcionTexto = sprintf(
                '%s DE %s DEL PRESENTE AÑO',
                str_pad($diaSalida, 2, '0', STR_PAD_LEFT),
                strtoupper($mesSalida)
            );
            
            // Fecha formateada para mostrar (d/m/Y)
            $fechaFormateada = $salida->fecha->format('d/m/Y');
            
            // Precio unitario y total
            $precioUnitario = $salida->producto->precio_unitario ?? 0;
            $importeTotal = $precioUnitario * $salida->cantidad;
            
            // Calcular importe en letras
            $importeLetra = \App\Helpers\NumerosEnLetras::convertir($importeTotal, 'PESOS', 'CENTAVOS');
            
            // Reemplazar variables en el documento
            // Variables estándar (igual que en entradas para compatibilidad)
            $templateProcessor->setValue('OFICIO', $numeroOficio);
            $templateProcessor->setValue('ANIO', $anioSalida);
            $templateProcessor->setValue('FECHA', $fechaTexto);
            $templateProcessor->setValue('PRODUCTO', $descripcionProducto);
            $templateProcessor->setValue('FECHA_RECIBIDO', $fechaRecepcionTexto);
            $templateProcessor->setValue('PROVEEDOR', $salida->destino ?: ($salida->motivo ?: 'N/A'));
            $templateProcessor->setValue('FACTURA', $salida->motivo ?: 'N/A');
            $templateProcessor->setValue('IMPORTE_NUM', '$' . number_format($importeTotal, 2, '.', ','));
            $templateProcessor->setValue('IMPORTE_LETRA', strtoupper($importeLetra));
            
            // Variables específicas de salidas (formato de la vista HTML)
            $templateProcessor->setValue('FOLIO', $folioCompleto);
            $templateProcessor->setValue('FOLIO_COMPLETO', $folioCompleto);
            $templateProcessor->setValue('NUMERO_OFICIO', $numeroOficio);
            $templateProcessor->setValue('CANTIDAD', number_format($salida->cantidad, 2, '.', ','));
            $templateProcessor->setValue('CANTIDAD_ENTERO', number_format($salida->cantidad, 0, '.', ','));
            $templateProcessor->setValue('UNIDAD_MEDIDA', strtoupper($salida->producto->unidad_medida ?? 'PIEZA'));
            $templateProcessor->setValue('PRECIO_UNITARIO', '$' . number_format($precioUnitario, 2, '.', ','));
            $templateProcessor->setValue('NOMBRE_PRODUCTO', strtoupper($salida->producto->nombre ?? ''));
            $templateProcessor->setValue('MOTIVO', $salida->motivo ?: 'VALE MANUAL POR CORREO');
            $templateProcessor->setValue('DESTINO', $salida->destino ?: 'Oficina del Fiscal');
            $templateProcessor->setValue('AREA', $salida->destino ?: 'Oficina del Fiscal');
            $templateProcessor->setValue('OBSERVACIONES', $salida->observaciones ?: '');
            $templateProcessor->setValue('ENTREGA_NOMBRE', $salida->entrega_nombre ?: '');
            $templateProcessor->setValue('RECIBE_NOMBRE', $salida->recibe_nombre ?: '');
            $templateProcessor->setValue('USUARIO', $salida->usuario->name ?? '');
            $templateProcessor->setValue('EMPLEADO', $salida->usuario->name ?? '');
            $templateProcessor->setValue('FECHA_FORMATEADA', $fechaFormateada);
            $templateProcessor->setValue('ALMACEN', '16-FISCALÍA ESTATAL');
            $templateProcessor->setValue('TIPO_MOVIMIENTO', 'SALIDA DE INSUMOS');
            $templateProcessor->setValue('DEPENDENCIA', 'Fiscalía Estatal');
            $templateProcessor->setValue('ESTATUS', 'Hecho');
            $templateProcessor->setValue('NO_SOLICITUD', $salida->motivo ?: 'VALE MANUAL POR CORREO');
            
            // Generar archivo temporal
            $tempFile = tempnam(sys_get_temp_dir(), 'salida_');
            $templateProcessor->saveAs($tempFile);
            
            // Nombre del archivo a descargar
            $fileName = 'Salida_' . ($salida->folio ?? $salida->id) . '.docx';
            $fileName = str_replace(['/', '\\'], '_', $fileName); // Limpiar caracteres no válidos
            
            // Descargar el archivo
            return response()->download($tempFile, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            \Log::error('Error al generar documento Word de salida', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Error al generar el documento Word: ' . $e->getMessage()]);
        }
    }

    /**
     * Mostrar formulario para editar salida
     */
    public function edit(Salida $salida)
    {
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();
        return view('salidas.edit', compact('salida', 'productos'));
    }

    /**
     * Actualizar salida
     */
    public function update(Request $request, Salida $salida)
    {
        $validated = $request->validate([
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
            // Revertir stock anterior
            $productoAnterior = Producto::findOrFail($salida->producto_id);
            $productoAnterior->increment('stock_actual', $salida->cantidad);

            // Verificar stock nuevo
            $productoNuevo = Producto::findOrFail($validated['producto_id']);
            if ($productoNuevo->stock_actual < $validated['cantidad']) {
                throw new \Exception('No hay suficiente stock disponible.');
            }

            // Actualizar salida
            $salida->update($validated);

            // Actualizar stock nuevo
            $productoNuevo->decrement('stock_actual', $validated['cantidad']);
        });

        return redirect()->route('salidas.index')
            ->with('success', 'Salida actualizada exitosamente.');
    }

    /**
     * Eliminar salida
     */
    public function destroy(Salida $salida)
    {
        DB::transaction(function () use ($salida) {
            // Revertir stock
            $producto = Producto::findOrFail($salida->producto_id);
            $producto->increment('stock_actual', $salida->cantidad);

            // Eliminar salida
            $salida->delete();
        });

        return redirect()->route('salidas.index')
            ->with('success', 'Salida eliminada exitosamente.');
    }
}



