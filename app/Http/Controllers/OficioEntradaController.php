<?php

namespace App\Http\Controllers;

use App\Models\OficioEntrada;
use App\Helpers\NumerosEnLetras;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;

class OficioEntradaController extends Controller
{
    /**
     * Mostrar listado de oficios de entrada
     */
    public function index(Request $request)
    {
        $query = OficioEntrada::with('usuario');

        // Filtros
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_oficio', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_oficio', '<=', $request->fecha_hasta);
        }

        if ($request->filled('proveedor')) {
            $query->where('proveedor_nombre', 'like', '%' . $request->proveedor . '%');
        }

        $oficios = $query->latest('fecha_oficio')->paginate(20);

        return view('oficios-entrada.index', compact('oficios'));
    }

    /**
     * Mostrar formulario para crear oficio de entrada
     */
    public function create()
    {
        return view('oficios-entrada.create');
    }

    /**
     * Guardar nuevo oficio de entrada
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_oficio' => 'nullable|integer|min:1|unique:oficios_entrada,numero_oficio',
            'fecha_oficio' => 'required|date',
            'descripcion_material' => 'nullable|string',
            'fecha_recepcion' => 'required|date',
            'proveedor_nombre' => 'required|string|max:255',
            'numero_factura' => 'required|string|max:100',
            'importe_total' => 'required|numeric|min:0',
            'productos_data' => 'required|json',
        ]);

        $productosData = json_decode($validated['productos_data'], true);
        
        if (empty($productosData) || !is_array($productosData) || count($productosData) === 0) {
            return back()->withErrors(['productos_data' => 'Debe agregar al menos un producto al oficio.'])->withInput();
        }

        try {
            $fecha = \Carbon\Carbon::parse($validated['fecha_oficio']);
            $year = $fecha->format('Y');
            
            // Si no se proporcionó número de oficio, generarlo automáticamente
            if (empty($validated['numero_oficio'])) {
                // Obtener el último número de oficio del año
                $ultimoNumero = OficioEntrada::whereYear('fecha_oficio', $year)
                    ->max('numero_oficio') ?? 567; // Empezar desde 568 como en el ejemplo
                
                $numeroOficio = $ultimoNumero + 1;
            } else {
                $numeroOficio = $validated['numero_oficio'];
            }
            
            // Generar folio completo: FE.19.01/{consecutivo}/2025/CGA
            $folioCompleto = 'FE.19.01/' . $numeroOficio . '/' . $year . '/CGA';
            
            // Verificar que el folio no exista
            while (OficioEntrada::where('folio_completo', $folioCompleto)->exists()) {
                if (empty($validated['numero_oficio'])) {
                    // Si fue generado automáticamente, incrementar
                    $numeroOficio++;
                    $folioCompleto = 'FE.19.01/' . $numeroOficio . '/' . $year . '/CGA';
                } else {
                    // Si fue proporcionado por el usuario, mostrar error
                    return back()->withErrors(['numero_oficio' => 'El número de oficio ya existe o genera un folio duplicado.'])->withInput();
                }
            }
            
            // Convertir importe a letras
            $importeLetra = NumerosEnLetras::convertir($validated['importe_total'], 'PESOS', 'CENTAVOS');
            
            // Generar descripción automática si no se proporcionó
            if (empty($validated['descripcion_material'])) {
                $descripciones = array_map(function($p) {
                    return "({$p['cantidad']} PIEZA) " . $p['nombre'];
                }, $productosData);
                $validated['descripcion_material'] = implode(', ', $descripciones);
            }
            
            $validated['numero_oficio'] = $numeroOficio;
            $validated['folio_completo'] = $folioCompleto;
            $validated['importe_total_letra'] = $importeLetra;
            $validated['user_id'] = auth()->id();
            
            // Crear oficio (solo el oficio, sin crear entradas)
            $oficio = OficioEntrada::create($validated);

        } catch (\Exception $e) {
            \Log::error('Error al crear oficio de entrada', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Error al crear el oficio: ' . $e->getMessage()])->withInput();
        }

        if (!$oficio) {
            return back()->withErrors(['error' => 'Error al crear el oficio de entrada.'])->withInput();
        }

        return redirect()->route('oficios-entrada.show', $oficio->id)
            ->with('success', 'Oficio de entrada registrado exitosamente.');
    }

    /**
     * Mostrar detalle de oficio de entrada
     */
    public function show(OficioEntrada $oficioEntrada)
    {
        $oficioEntrada->load('usuario');
        return view('oficios-entrada.show', compact('oficioEntrada'));
    }

    /**
     * Mostrar vista de impresión de oficio de entrada
     */
    public function imprimir(OficioEntrada $oficioEntrada)
    {
        $oficioEntrada->load('usuario');
        return view('oficios-entrada.imprimir', compact('oficioEntrada'));
    }

    /**
     * Descargar documento Word del oficio de entrada
     */
    public function descargarWord(OficioEntrada $oficioEntrada)
    {
        $oficioEntrada->load('usuario');
        
        try {
            // Ruta del documento plantilla
            $templatePath = base_path('FORMATO-WORD.docx');
            
            // Verificar que el archivo existe
            if (!file_exists($templatePath)) {
                \Log::error('Plantilla Word no encontrada en: ' . $templatePath);
                return back()->withErrors(['error' => 'No se encontró el archivo plantilla FORMATO-WORD.docx en: ' . $templatePath]);
            }
            
            // Crear el procesador de plantilla
            $templateProcessor = new TemplateProcessor($templatePath);
            
            // Formatear fechas
            $meses = [
                1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
                5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
                9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
            ];
            
            $fechaOficio = $oficioEntrada->fecha_oficio;
            $diaOficio = $fechaOficio->format('d');
            $mesOficio = $meses[(int)$fechaOficio->format('m')];
            $anioOficio = $fechaOficio->format('Y');
            $fechaOficioCompleta = $diaOficio . ' de ' . $mesOficio . ' del ' . $anioOficio;
            
            $fechaRecepcion = $oficioEntrada->fecha_recepcion;
            $diaRecepcion = str_pad($fechaRecepcion->format('d'), 2, '0', STR_PAD_LEFT);
            $mesRecepcion = strtoupper($meses[(int)$fechaRecepcion->format('m')]);
            $anioRecepcion = $fechaRecepcion->format('Y');
            $fechaRecepcionCompleta = $diaRecepcion . ' DE ' . $mesRecepcion . ' DEL ' . $anioRecepcion;
            
            // Texto completo para la fecha del oficio (incluyendo ciudad configurable)
            $ciudadOficio = config('app.oficio_lugar', 'Guadalajara, Jal.');
            $fechaTexto = sprintf(
                '%s, %s de %s del %s.',
                $ciudadOficio,
                ltrim($diaOficio, '0'),
                $mesOficio,
                $anioOficio
            );

            // Texto para la plantilla Word utilizando las variables acordadas
            $templateProcessor->setValue('OFICIO', $oficioEntrada->numero_oficio);
            $templateProcessor->setValue('ANIO', $anioOficio);
            $templateProcessor->setValue('FECHA', $fechaTexto);
            $templateProcessor->setValue('PRODUCTO', $oficioEntrada->descripcion_material);
            $templateProcessor->setValue('FECHA_RECIBIDO', $fechaRecepcionCompleta);
            $templateProcessor->setValue('PROVEEDOR', $oficioEntrada->proveedor_nombre);
            $templateProcessor->setValue('FACTURA', $oficioEntrada->numero_factura);
            $templateProcessor->setValue('IMPORTE_NUM', '$' . number_format($oficioEntrada->importe_total, 2, '.', ','));
            $templateProcessor->setValue('IMPORTE_LETRA', strtoupper($oficioEntrada->importe_total_letra));

            // Variables legadas (por si aún existen plantillas anteriores)
            $templateProcessor->setValue('folio_completo', $oficioEntrada->folio_completo);
            $templateProcessor->setValue('numero_oficio', $oficioEntrada->numero_oficio);
            $templateProcessor->setValue('fecha_oficio', $fechaOficioCompleta);
            $templateProcessor->setValue('fecha_oficio_dia', $diaOficio);
            $templateProcessor->setValue('fecha_oficio_mes', $mesOficio);
            $templateProcessor->setValue('fecha_oficio_anio', $anioOficio);
            $templateProcessor->setValue('fecha_recepcion', $fechaRecepcionCompleta);
            $templateProcessor->setValue('fecha_recepcion_dia', $diaRecepcion);
            $templateProcessor->setValue('fecha_recepcion_mes', $mesRecepcion);
            $templateProcessor->setValue('fecha_recepcion_anio', $anioRecepcion);
            $templateProcessor->setValue('descripcion_material', $oficioEntrada->descripcion_material);
            $templateProcessor->setValue('proveedor_nombre', $oficioEntrada->proveedor_nombre);
            $templateProcessor->setValue('proveedor_nombre_upper', strtoupper($oficioEntrada->proveedor_nombre));
            $templateProcessor->setValue('numero_factura', $oficioEntrada->numero_factura);
            $templateProcessor->setValue('importe_total', number_format($oficioEntrada->importe_total, 2, '.', ','));
            $templateProcessor->setValue('importe_total_letra', strtoupper($oficioEntrada->importe_total_letra));
            
            // Generar archivo temporal
            $tempFile = tempnam(sys_get_temp_dir(), 'oficio_entrada_');
            $templateProcessor->saveAs($tempFile);
            
            // Nombre del archivo a descargar
            $fileName = 'Oficio_Entrada_' . $oficioEntrada->folio_completo . '.docx';
            $fileName = str_replace(['/', '\\'], '_', $fileName); // Limpiar caracteres no válidos
            
            // Descargar el archivo
            return response()->download($tempFile, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            \Log::error('Error al generar documento Word', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Error al generar el documento Word: ' . $e->getMessage()]);
        }
    }

    /**
     * Mostrar formulario para editar oficio de entrada
     */
    public function edit(OficioEntrada $oficioEntrada)
    {
        return view('oficios-entrada.edit', compact('oficioEntrada'));
    }

    /**
     * Actualizar oficio de entrada
     */
    public function update(Request $request, OficioEntrada $oficioEntrada)
    {
        $validated = $request->validate([
            'fecha_oficio' => 'required|date',
            'descripcion_material' => 'required|string',
            'fecha_recepcion' => 'required|date',
            'proveedor_nombre' => 'required|string|max:255',
            'numero_factura' => 'required|string|max:100',
            'importe_total' => 'required|numeric|min:0',
        ]);

        // Convertir importe a letras
        $validated['importe_total_letra'] = NumerosEnLetras::convertir($validated['importe_total'], 'PESOS', 'CENTAVOS');
        
        // Si cambió la fecha, podría necesitar regenerar el folio, pero por ahora no lo hacemos
        $oficioEntrada->update($validated);

        return redirect()->route('oficios-entrada.show', $oficioEntrada)
            ->with('success', 'Oficio de entrada actualizado exitosamente.');
    }

    /**
     * Eliminar oficio de entrada
     */
    public function destroy(OficioEntrada $oficioEntrada)
    {
        $oficioEntrada->delete();

        return redirect()->route('oficios-entrada.index')
            ->with('success', 'Oficio de entrada eliminado exitosamente.');
    }
}
