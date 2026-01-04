<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Entrada;
use App\Models\Salida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteController extends Controller
{
    /**
     * Mostrar reportes de inventario
     */
    public function index(Request $request)
    {
        $periodo = $request->get('periodo', 'mensual'); // mensual o anual
        $mes = $request->get('mes', date('Y-m'));
        $ano = $request->get('ano', date('Y'));
        $tipoFiltro = $request->get('tipo_filtro', 'general'); // general, producto, categoria
        $productoId = $request->get('producto_id');
        $categoria = $request->get('categoria');

        // Reporte de productos con stock mínimo y máximo
        $productosMinimos = Producto::whereColumn('stock_actual', '<=', 'stock_minimo')
            ->where('activo', true)
            ->orderBy('stock_actual', 'asc')
            ->get();

        $productosMaximos = Producto::where('activo', true)
            ->orderBy('stock_actual', 'desc')
            ->limit(10)
            ->get();

        // Obtener lista de categorías para el filtro
        $categorias = Producto::where('activo', true)
            ->whereNotNull('categoria')
            ->distinct()
            ->pluck('categoria')
            ->sort()
            ->values();

        // Obtener lista de productos para el filtro
        $productos = Producto::where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'codigo']);

        // Promedio mensual de entradas y salidas
        if ($periodo === 'mensual') {
            $inicio = Carbon::parse($mes . '-01')->startOfMonth();
            $fin = Carbon::parse($mes . '-01')->endOfMonth();
        } else {
            $inicio = Carbon::create($ano, 1, 1)->startOfYear();
            $fin = Carbon::create($ano, 12, 31)->endOfYear();
        }

        // Total y promedio de entradas
        $entradasQuery = Entrada::whereBetween('fecha', [$inicio->format('Y-m-d'), $fin->format('Y-m-d')]);
        $totalEntradas = $entradasQuery->sum('cantidad');
        
        // Calcular días del período
        $diasPeriodo = $periodo === 'mensual' 
            ? $inicio->daysInMonth 
            : ($inicio->isLeapYear() ? 366 : 365);
        
        // Promedio diario
        $promedioDiarioEntradas = $totalEntradas > 0 && $diasPeriodo > 0 
            ? $totalEntradas / $diasPeriodo 
            : 0;
        
        // Promedio mensual: 
        // - Para reporte mensual: es el total del mes (un solo mes)
        // - Para reporte anual: promedio mensual = total del año / 12
        $promedioMensualEntradas = $periodo === 'mensual'
            ? $totalEntradas  // En un reporte mensual, el "promedio mensual" es el total del mes
            : ($totalEntradas > 0 ? $totalEntradas / 12 : 0);
        
        // Promedio anual (solo para reporte anual)
        $promedioAnualEntradas = $periodo === 'anual' ? $totalEntradas : 0;

        // Total y promedio de salidas
        $salidasQuery = Salida::whereBetween('fecha', [$inicio->format('Y-m-d'), $fin->format('Y-m-d')]);
        $totalSalidas = $salidasQuery->sum('cantidad');
        
        // Promedio diario
        $promedioDiarioSalidas = $totalSalidas > 0 && $diasPeriodo > 0 
            ? $totalSalidas / $diasPeriodo 
            : 0;
        
        // Promedio mensual:
        // - Para reporte mensual: es el total del mes (un solo mes)
        // - Para reporte anual: promedio mensual = total del año / 12
        $promedioMensualSalidas = $periodo === 'mensual'
            ? $totalSalidas  // En un reporte mensual, el "promedio mensual" es el total del mes
            : ($totalSalidas > 0 ? $totalSalidas / 12 : 0);
        
        // Promedio anual
        $promedioAnualSalidas = $periodo === 'anual' ? $totalSalidas : 0;

        // Calcular promedios mensuales y anuales históricos
        $anoActual = date('Y');
        $inicioAnoActual = Carbon::create($anoActual, 1, 1)->startOfYear();
        $finAnoActual = Carbon::create($anoActual, 12, 31)->endOfYear();

        // Aplicar filtros a las consultas base
        $entradasBaseQuery = Entrada::join('productos', 'entradas.producto_id', '=', 'productos.id')
            ->whereBetween('entradas.fecha', [$inicio->format('Y-m-d'), $fin->format('Y-m-d')])
            ->where('productos.activo', true);

        $salidasBaseQuery = Salida::join('productos', 'salidas.producto_id', '=', 'productos.id')
            ->whereBetween('salidas.fecha', [$inicio->format('Y-m-d'), $fin->format('Y-m-d')])
            ->where('productos.activo', true);

        if ($tipoFiltro === 'producto' && $productoId) {
            $entradasBaseQuery->where('productos.id', $productoId);
            $salidasBaseQuery->where('productos.id', $productoId);
        } elseif ($tipoFiltro === 'categoria' && $categoria) {
            $entradasBaseQuery->where('productos.categoria', $categoria);
            $salidasBaseQuery->where('productos.categoria', $categoria);
        }

        // Entradas y salidas por producto
        $entradasPorProducto = (clone $entradasBaseQuery)->select('productos.id', 'productos.nombre', 'productos.codigo', 'productos.categoria',
                DB::raw('SUM(entradas.cantidad) as total_entradas'),
                DB::raw('AVG(entradas.cantidad) as promedio_entradas')
            )
            ->groupBy('productos.id', 'productos.nombre', 'productos.codigo', 'productos.categoria')
            ->orderBy('total_entradas', 'desc')
            ->get();

        $salidasPorProducto = (clone $salidasBaseQuery)->select('productos.id', 'productos.nombre', 'productos.codigo', 'productos.categoria',
                DB::raw('SUM(salidas.cantidad) as total_salidas'),
                DB::raw('AVG(salidas.cantidad) as promedio_salidas')
            )
            ->groupBy('productos.id', 'productos.nombre', 'productos.codigo', 'productos.categoria')
            ->orderBy('total_salidas', 'desc')
            ->get();

        // Promedios mensuales y anuales por producto
        // Obtener todos los productos únicos de ambas listas
        $productosUnicos = collect();
        foreach ($entradasPorProducto as $item) {
            if (!$productosUnicos->contains('id', $item->id)) {
                $productosUnicos->push(['id' => $item->id, 'codigo' => $item->codigo]);
            }
        }
        foreach ($salidasPorProducto as $item) {
            if (!$productosUnicos->contains('id', $item->id)) {
                $productosUnicos->push(['id' => $item->id, 'codigo' => $item->codigo]);
            }
        }
        
        $promediosPorProducto = [];
        foreach ($productosUnicos as $prodUnico) {
            $productoIdItem = $prodUnico['id'];
            $productoCodigo = $prodUnico['codigo'];
            
            // Calcular promedios mensuales y anuales históricos de ENTRADAS
            $entradasAnoActual = Entrada::where('producto_id', $productoIdItem)
                ->whereBetween('fecha', [$inicioAnoActual->format('Y-m-d'), $finAnoActual->format('Y-m-d')])
                ->sum('cantidad');
            
            $entradasHistorico = Entrada::where('producto_id', $productoIdItem)
                ->selectRaw('YEAR(fecha) as ano, SUM(cantidad) as total')
                ->groupBy('ano')
                ->get();
            
            $promedioMensualEntradas = $entradasAnoActual > 0 ? $entradasAnoActual / 12 : 0;
            $promedioAnualEntradas = $entradasHistorico->count() > 0 ? $entradasHistorico->avg('total') : 0;
            
            // Calcular promedios mensuales y anuales históricos de SALIDAS
            $salidasAnoActual = Salida::where('producto_id', $productoIdItem)
                ->whereBetween('fecha', [$inicioAnoActual->format('Y-m-d'), $finAnoActual->format('Y-m-d')])
                ->sum('cantidad');
            
            $salidasHistorico = Salida::where('producto_id', $productoIdItem)
                ->selectRaw('YEAR(fecha) as ano, SUM(cantidad) as total')
                ->groupBy('ano')
                ->get();
            
            $promedioMensualSalidas = $salidasAnoActual > 0 ? $salidasAnoActual / 12 : 0;
            $promedioAnualSalidas = $salidasHistorico->count() > 0 ? $salidasHistorico->avg('total') : 0;
            
            $promediosPorProducto[$productoCodigo] = [
                'promedio_mensual_entradas' => $promedioMensualEntradas,
                'promedio_anual_entradas' => $promedioAnualEntradas,
                'promedio_mensual_salidas' => $promedioMensualSalidas,
                'promedio_anual_salidas' => $promedioAnualSalidas,
            ];
        }

        // Promedios por categoría
        $promediosPorCategoria = [];
        if ($tipoFiltro === 'general' || $tipoFiltro === 'categoria') {
            $categoriasConMovimientos = Producto::where('activo', true)
                ->whereNotNull('categoria')
                ->when($tipoFiltro === 'categoria' && $categoria, function($q) use ($categoria) {
                    return $q->where('categoria', $categoria);
                })
                ->distinct()
                ->pluck('categoria');

            foreach ($categoriasConMovimientos as $cat) {
                $productosCategoria = Producto::where('categoria', $cat)->where('activo', true)->pluck('id');
                
                // Entradas
                $entradasCategoriaAno = Entrada::whereIn('producto_id', $productosCategoria)
                    ->whereBetween('fecha', [$inicioAnoActual->format('Y-m-d'), $finAnoActual->format('Y-m-d')])
                    ->sum('cantidad');
                
                $entradasCategoriaHistorico = Entrada::whereIn('producto_id', $productosCategoria)
                    ->selectRaw('YEAR(fecha) as ano, SUM(cantidad) as total')
                    ->groupBy('ano')
                    ->get();
                
                // Salidas
                $salidasCategoriaAno = Salida::whereIn('producto_id', $productosCategoria)
                    ->whereBetween('fecha', [$inicioAnoActual->format('Y-m-d'), $finAnoActual->format('Y-m-d')])
                    ->sum('cantidad');
                
                $salidasCategoriaHistorico = Salida::whereIn('producto_id', $productosCategoria)
                    ->selectRaw('YEAR(fecha) as ano, SUM(cantidad) as total')
                    ->groupBy('ano')
                    ->get();
                
                $promediosPorCategoria[$cat] = [
                    'promedio_mensual_entradas' => $entradasCategoriaAno > 0 ? $entradasCategoriaAno / 12 : 0,
                    'promedio_anual_entradas' => $entradasCategoriaHistorico->count() > 0 ? $entradasCategoriaHistorico->avg('total') : 0,
                    'promedio_mensual_salidas' => $salidasCategoriaAno > 0 ? $salidasCategoriaAno / 12 : 0,
                    'promedio_anual_salidas' => $salidasCategoriaHistorico->count() > 0 ? $salidasCategoriaHistorico->avg('total') : 0,
                    'total_entradas' => (clone $entradasBaseQuery)->where('productos.categoria', $cat)->sum('entradas.cantidad'),
                    'total_salidas' => (clone $salidasBaseQuery)->where('productos.categoria', $cat)->sum('salidas.cantidad'),
                ];
            }
        }

        // Productos pronto a acabar (stock < 100)
        $productosProntoAcabar = Producto::where('stock_actual', '<', 100)
            ->where('activo', true)
            ->orderBy('stock_actual', 'asc')
            ->get();
        
        // Total de salidas del año actual
        $totalSalidasAnoActual = Salida::whereBetween('fecha', [$inicioAnoActual->format('Y-m-d'), $finAnoActual->format('Y-m-d')])
            ->sum('cantidad');
        
        // Promedio mensual de salidas (promedio por mes del año actual)
        $promedioMensualSalidasHistorico = $totalSalidasAnoActual > 0 ? $totalSalidasAnoActual / 12 : 0;
        
        // Calcular promedio anual de salidas (promedio por año de todos los años disponibles)
        $anosConSalidas = Salida::selectRaw('YEAR(fecha) as ano, SUM(cantidad) as total')
            ->groupBy('ano')
            ->get();
        
        $promedioAnualSalidasHistorico = $anosConSalidas->count() > 0 
            ? $anosConSalidas->avg('total') 
            : 0;

        // Calcular promedios mensuales y anuales históricos de entradas
        $totalEntradasAnoActual = Entrada::whereBetween('fecha', [$inicioAnoActual->format('Y-m-d'), $finAnoActual->format('Y-m-d')])
            ->sum('cantidad');
        
        $promedioMensualEntradasHistorico = $totalEntradasAnoActual > 0 ? $totalEntradasAnoActual / 12 : 0;
        
        $anosConEntradas = Entrada::selectRaw('YEAR(fecha) as ano, SUM(cantidad) as total')
            ->groupBy('ano')
            ->get();
        
        $promedioAnualEntradasHistorico = $anosConEntradas->count() > 0 
            ? $anosConEntradas->avg('total') 
            : 0;

        return view('reportes.index', compact(
            'periodo',
            'mes',
            'ano',
            'tipoFiltro',
            'productoId',
            'categoria',
            'productos',
            'categorias',
            'productosMinimos',
            'productosMaximos',
            'totalEntradas',
            'promedioDiarioEntradas',
            'promedioMensualEntradas',
            'promedioAnualEntradas',
            'totalSalidas',
            'promedioDiarioSalidas',
            'promedioMensualSalidas',
            'promedioAnualSalidas',
            'entradasPorProducto',
            'salidasPorProducto',
            'productosProntoAcabar',
            'promedioMensualSalidasHistorico',
            'promedioAnualSalidasHistorico',
            'promedioMensualEntradasHistorico',
            'promedioAnualEntradasHistorico',
            'promediosPorProducto',
            'promediosPorCategoria'
        ));
    }

    /**
     * Mostrar Kardex de inventario (movimientos por producto y fecha)
     */
    public function kardex(Request $request)
    {
        // Filtros
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::now()->format('Y-m-d'));
        $productoId = $request->get('producto_id');

        // Obtener productos para el filtro
        $productos = Producto::where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'codigo']);

        // Consulta base de entradas
        $entradasQuery = Entrada::with('producto')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->join('productos', 'entradas.producto_id', '=', 'productos.id')
            ->where('productos.activo', true);

        // Consulta base de salidas
        $salidasQuery = Salida::with('producto')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->join('productos', 'salidas.producto_id', '=', 'productos.id')
            ->where('productos.activo', true);

        // Aplicar filtro de producto si existe
        if ($productoId) {
            $entradasQuery->where('productos.id', $productoId);
            $salidasQuery->where('productos.id', $productoId);
        }

        // Obtener entradas y salidas
        $entradas = $entradasQuery
            ->select('entradas.*', 'productos.nombre as producto_nombre', 'productos.unidad_medida')
            ->orderBy('entradas.fecha')
            ->orderBy('entradas.id')
            ->get();

        $salidas = $salidasQuery
            ->select('salidas.*', 'productos.nombre as producto_nombre', 'productos.unidad_medida')
            ->orderBy('salidas.fecha')
            ->orderBy('salidas.id')
            ->get();

        // Agrupar por producto y fecha
        $kardex = [];

        // Procesar entradas
        foreach ($entradas as $entrada) {
            $prodId = $entrada->producto_id;
            $fecha = $entrada->fecha->format('Y-m-d');
            $fechaFormateada = $entrada->fecha->format('d/m/Y');

            if (!isset($kardex[$prodId])) {
                $kardex[$prodId] = [
                    'producto' => $entrada->producto,
                    'producto_nombre' => $entrada->producto_nombre,
                    'unidad_medida' => $entrada->unidad_medida,
                    'fechas' => []
                ];
            }

            if (!isset($kardex[$prodId]['fechas'][$fecha])) {
                $kardex[$prodId]['fechas'][$fecha] = [
                    'fecha' => $fechaFormateada,
                    'entradas' => [],
                    'salidas' => [],
                    'total_entradas' => 0,
                    'total_salidas' => 0
                ];
            }

            $kardex[$prodId]['fechas'][$fecha]['entradas'][] = [
                'folio' => $entrada->folio ?? 'ENT-' . str_pad($entrada->id, 6, '0', STR_PAD_LEFT),
                'cantidad' => $entrada->cantidad,
                'proveedor' => $entrada->proveedor,
                'numero_factura' => $entrada->numero_factura,
            ];

            $kardex[$prodId]['fechas'][$fecha]['total_entradas'] += $entrada->cantidad;
        }

        // Procesar salidas
        foreach ($salidas as $salida) {
            $prodId = $salida->producto_id;
            $fecha = $salida->fecha->format('Y-m-d');
            $fechaFormateada = $salida->fecha->format('d/m/Y');

            if (!isset($kardex[$prodId])) {
                $kardex[$prodId] = [
                    'producto' => $salida->producto,
                    'producto_nombre' => $salida->producto_nombre,
                    'unidad_medida' => $salida->unidad_medida,
                    'fechas' => []
                ];
            }

            if (!isset($kardex[$prodId]['fechas'][$fecha])) {
                $kardex[$prodId]['fechas'][$fecha] = [
                    'fecha' => $fechaFormateada,
                    'entradas' => [],
                    'salidas' => [],
                    'total_entradas' => 0,
                    'total_salidas' => 0
                ];
            }

            $kardex[$prodId]['fechas'][$fecha]['salidas'][] = [
                'folio' => $salida->folio ?? 'SAL-' . str_pad($salida->id, 6, '0', STR_PAD_LEFT),
                'cantidad' => $salida->cantidad,
                'motivo' => $salida->motivo,
                'destino' => $salida->destino,
            ];

            $kardex[$prodId]['fechas'][$fecha]['total_salidas'] += $salida->cantidad;
        }

        // Ordenar fechas dentro de cada producto
        foreach ($kardex as &$productoData) {
            ksort($productoData['fechas']);
        }

        return view('reportes.kardex', compact(
            'kardex',
            'productos',
            'fechaInicio',
            'fechaFin',
            'productoId'
        ));
    }

    /**
     * Exportar Kardex a Excel
     */
    public function exportarKardexExcel(Request $request)
    {
        // Filtros
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::now()->format('Y-m-d'));
        $productoId = $request->get('producto_id');

        // Normalizar producto_id: si está vacío, null o '0', no filtrar (mostrar todos)
        if (empty($productoId) || $productoId == '0' || $productoId == '') {
            $productoId = null;
        }

        // Obtener datos (reutilizar lógica del método kardex)
        $entradasQuery = Entrada::with(['producto', 'oficioEntrada'])
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->join('productos', 'entradas.producto_id', '=', 'productos.id')
            ->where('productos.activo', true);

        $salidasQuery = Salida::with('producto')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->join('productos', 'salidas.producto_id', '=', 'productos.id')
            ->where('productos.activo', true);

        // Aplicar filtro de producto solo si está especificado
        if ($productoId) {
            $entradasQuery->where('productos.id', $productoId);
            $salidasQuery->where('productos.id', $productoId);
        }

        $entradas = $entradasQuery
            ->select('entradas.id', 'entradas.producto_id', 'entradas.cantidad', 'entradas.fecha', 
                     'entradas.folio', 'entradas.oficio_entrada_id', 'entradas.precio_unitario', 'entradas.total',
                     'productos.nombre as producto_nombre', 'productos.unidad_medida')
            ->orderBy('entradas.fecha')
            ->orderBy('entradas.id')
            ->get();

        $salidas = $salidasQuery
            ->select('salidas.*', 'productos.nombre as producto_nombre', 'productos.unidad_medida')
            ->orderBy('salidas.fecha')
            ->orderBy('salidas.id')
            ->get();

        // Agrupar movimientos por producto y fecha
        $kardex = [];
        
        // Si hay filtro de producto, solo inicializar ese producto
        if ($productoId) {
            $productoFiltrado = Producto::where('activo', true)
                ->where('id', $productoId)
                ->first(['id', 'nombre', 'codigo', 'categoria', 'unidad_medida', 'stock_actual']);
            
            if ($productoFiltrado) {
                $kardex[$productoId] = [
                    'producto_nombre' => $productoFiltrado->nombre,
                    'unidad_medida' => $productoFiltrado->unidad_medida,
                    'fechas' => []
                ];
            }
        }
        
        // Si no hay filtro, inicializar todos los productos activos
        if (!$productoId) {
            $todosProductos = Producto::where('activo', true)
                ->orderBy('nombre')
                ->get(['id', 'nombre', 'codigo', 'categoria', 'unidad_medida', 'stock_actual'])
                ->keyBy('id');
            
            foreach ($todosProductos as $prodId => $producto) {
                $kardex[$prodId] = [
                    'producto_nombre' => $producto->nombre,
                    'unidad_medida' => $producto->unidad_medida,
                    'fechas' => []
                ];
            }
        }
        
        // Agregar entradas
        foreach ($entradas as $entrada) {
            $prodId = $entrada->producto_id;
            $fecha = $entrada->fecha->format('Y-m-d');
            if (!isset($kardex[$prodId])) {
                $kardex[$prodId] = [
                    'producto_nombre' => $entrada->producto_nombre,
                    'unidad_medida' => $entrada->unidad_medida,
                    'fechas' => []
                ];
            }
            if (!isset($kardex[$prodId]['fechas'][$fecha])) {
                $kardex[$prodId]['fechas'][$fecha] = [
                    'fecha' => $entrada->fecha,
                    'entradas' => [],
                    'salidas' => []
                ];
            }
            $kardex[$prodId]['fechas'][$fecha]['entradas'][] = [
                'folio' => $entrada->folio ?? 'ENT-' . str_pad($entrada->id, 6, '0', STR_PAD_LEFT),
                'cantidad' => $entrada->cantidad,
                'id' => $entrada->id,
                'oficio_entrada_id' => $entrada->oficio_entrada_id,
                'precio_unitario' => $entrada->precio_unitario ?? 0,
                'total' => $entrada->total ?? ($entrada->precio_unitario ?? 0) * $entrada->cantidad,
            ];
        }

        // Agregar salidas
        foreach ($salidas as $salida) {
            $prodId = $salida->producto_id;
            $fecha = $salida->fecha->format('Y-m-d');
            if (!isset($kardex[$prodId])) {
                $kardex[$prodId] = [
                    'producto_nombre' => $salida->producto_nombre,
                    'unidad_medida' => $salida->unidad_medida,
                    'fechas' => []
                ];
            }
            if (!isset($kardex[$prodId]['fechas'][$fecha])) {
                $kardex[$prodId]['fechas'][$fecha] = [
                    'fecha' => $salida->fecha,
                    'entradas' => [],
                    'salidas' => []
                ];
            }
            $kardex[$prodId]['fechas'][$fecha]['salidas'][] = [
                'folio' => $salida->folio ?? 'SAL-' . str_pad($salida->id, 6, '0', STR_PAD_LEFT),
                'cantidad' => $salida->cantidad,
                'id' => $salida->id,
            ];
        }

        // Crear Excel - UNA SOLA PESTAÑA con todos los productos
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Kardex Inventario');
        
        // Título principal - se centrará después de calcular todas las columnas
        $row = 1;
        $sheet->setCellValue('A' . $row, 'KARDEX DE INVENTARIO');
        $row++;
        
        // Período
        $sheet->setCellValue('A' . $row, 'Período: ' . Carbon::parse($fechaInicio)->format('d/m/Y') . ' - ' . Carbon::parse($fechaFin)->format('d/m/Y'));
        $row += 2;
        
        // Obtener todos los meses en el rango de fechas
        $inicio = Carbon::parse($fechaInicio);
        $fin = Carbon::parse($fechaFin);
        $meses = [];
        $fechaActual = $inicio->copy()->startOfMonth();
        while ($fechaActual <= $fin) {
            $meses[] = [
                'mes' => $fechaActual->format('m'),
                'ano' => $fechaActual->format('Y'),
                'nombre' => $fechaActual->format('F Y'),
                'nombre_corto' => $fechaActual->format('M Y')
            ];
            $fechaActual->addMonth();
        }
        
        // Estilos
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];
        
        $row = 1;
        
        // Título - se centrará después de calcular todas las columnas
        $sheet->setCellValue('A' . $row, 'KARDEX DE INVENTARIO');
        $row += 2;
        
        // Usar Coordinate para manejar columnas correctamente
        $colIndex = 1; // Empezar en columna A (índice 1)
        
        // Encabezados principales
        $col = Coordinate::stringFromColumnIndex($colIndex);
        $sheet->setCellValue($col . $row, 'Producto');
        $colIndex++;
        
        $col = Coordinate::stringFromColumnIndex($colIndex);
        $sheet->setCellValue($col . $row, 'GPO / Fam');
        $colIndex++;
        
        $col = Coordinate::stringFromColumnIndex($colIndex);
        $sheet->setCellValue($col . $row, 'U.M.');
        $colIndex++;
        
        $col = Coordinate::stringFromColumnIndex($colIndex);
        $sheet->setCellValue($col . $row, 'Existencia Inicial');
        $colIndex++;
        
        // Columnas por mes para entradas (FR y LPL) - sin encabezado merged
        foreach ($meses as $mes) {
            // Columna FR
            $colFR = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($colFR . $row, 'FR ' . strtoupper($mes['nombre_corto']));
            $colIndex++;
            
            // Columna LPL
            $colLPL = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($colLPL . $row, 'LPL ' . strtoupper($mes['nombre_corto']));
            $colIndex++;
        }
        
        // Columnas por mes para salidas
        foreach ($meses as $mes) {
            $col = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($col . $row, 'SALIDAS ' . strtoupper($mes['nombre_corto']));
            $colIndex++;
        }
        
        $col = Coordinate::stringFromColumnIndex($colIndex);
        $sheet->setCellValue($col . $row, 'Total Entradas');
        $colIndex++;
        
        // Obtener todos los días únicos con movimientos para crear columnas
        $diasConMovimientos = [];
        foreach ($kardex as $prodId => $productoData) {
            foreach ($productoData['fechas'] as $fecha => $datosFecha) {
                $fechaObj = is_object($datosFecha['fecha']) ? $datosFecha['fecha'] : Carbon::parse($datosFecha['fecha']);
                $diaKey = $fechaObj->format('Y-m-d');
                $diaNum = $fechaObj->format('d');
                $mesNum = $fechaObj->format('m');
                if (!isset($diasConMovimientos[$diaKey])) {
                    $diasConMovimientos[$diaKey] = [
                        'dia' => $diaNum,
                        'mes' => $mesNum,
                        'fecha' => $fechaObj
                    ];
                }
            }
        }
        ksort($diasConMovimientos);
        
        // Crear encabezados por día (después de Total Entradas)
        $colInicioDesglose = $colIndex;
        foreach ($diasConMovimientos as $diaKey => $diaInfo) {
            $col = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($col . $row, $diaInfo['dia']);
            $colIndex++;
        }
        
        $col = Coordinate::stringFromColumnIndex($colIndex);
        $sheet->setCellValue($col . $row, 'Total Salidas');
        $colIndex++;
        
        $col = Coordinate::stringFromColumnIndex($colIndex);
        $sheet->setCellValue($col . $row, 'Saldo Final');
        $lastCol = $col;
        
        // Aplicar estilo a encabezados
        $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray($headerStyle);
        $row++;
        
        // Obtener información de productos para el Excel (solo los que están en el kardex)
        $productosIds = array_keys($kardex);
        $productosInfo = Producto::whereIn('id', $productosIds)
            ->get(['id', 'nombre', 'codigo', 'categoria', 'unidad_medida', 'stock_actual'])
            ->keyBy('id');
        
        // Calcular stock inicial: stock que había ANTES del período seleccionado
        // Stock inicial = stock actual - entradas del período + salidas del período
        $stockInicialPorProducto = [];
        foreach ($kardex as $prodId => $productoData) {
            $totalEntradasPeriodo = 0;
            $totalSalidasPeriodo = 0;
            foreach ($productoData['fechas'] as $fecha => $datosFecha) {
                foreach ($datosFecha['entradas'] as $entrada) {
                    $totalEntradasPeriodo += $entrada['cantidad'];
                }
                foreach ($datosFecha['salidas'] as $salida) {
                    $totalSalidasPeriodo += $salida['cantidad'];
                }
            }
            $producto = $productosInfo->get($prodId);
            if ($producto) {
                // Stock inicial = stock actual - entradas + salidas (revertir movimientos del período)
                $stockInicialPorProducto[$prodId] = max(0, ($producto->stock_actual ?? 0) - $totalEntradasPeriodo + $totalSalidasPeriodo);
            }
        }
        
        // Calcular datos por producto y mes
        $datosPorProducto = [];
        foreach ($kardex as $prodId => $productoData) {
            $producto = $productosInfo->get($prodId);
            if (!$producto) continue;
            
            $datosPorProducto[$prodId] = [
                'nombre' => $producto->nombre,
                'codigo' => $producto->codigo ?? '',
                'categoria' => $producto->categoria ?? '',
                'unidad_medida' => $producto->unidad_medida ?? $productoData['unidad_medida'],
                'stock_inicial' => $stockInicialPorProducto[$prodId] ?? 0,
                'entradas_por_mes' => [],
                'salidas_por_mes' => [],
                'entradas_detalle_por_mes' => [], // Guardar detalle para fórmulas
                'salidas_detalle_por_mes' => [], // Guardar detalle para fórmulas
                'entradas_por_dia' => [], // Guardar detalle por día
                'salidas_por_dia' => [], // Guardar detalle por día
                'total_entradas' => 0,
                'total_salidas' => 0
            ];
            
            // Procesar entradas y salidas por mes y por día
            foreach ($productoData['fechas'] as $fecha => $datosFecha) {
                // Asegurar que fecha sea un objeto Carbon
                $fechaObj = is_object($datosFecha['fecha']) ? $datosFecha['fecha'] : Carbon::parse($datosFecha['fecha']);
                $mesKey = $fechaObj->format('Y-m');
                $diaKey = $fechaObj->format('Y-m-d');
                $diaNumero = $fechaObj->format('d');
                $mesNombre = $fechaObj->format('m');
                
                // Inicializar arrays de detalle
                if (!isset($datosPorProducto[$prodId]['entradas_por_mes'][$mesKey])) {
                    $datosPorProducto[$prodId]['entradas_por_mes'][$mesKey] = ['FR' => 0, 'LPL' => 0];
                    $datosPorProducto[$prodId]['entradas_detalle_por_mes'][$mesKey] = ['FR' => [], 'LPL' => []];
                }
                if (!isset($datosPorProducto[$prodId]['salidas_por_mes'][$mesKey])) {
                    $datosPorProducto[$prodId]['salidas_por_mes'][$mesKey] = 0;
                    $datosPorProducto[$prodId]['salidas_detalle_por_mes'][$mesKey] = [];
                }
                
                // Inicializar arrays por día
                if (!isset($datosPorProducto[$prodId]['entradas_por_dia'][$diaKey])) {
                    $datosPorProducto[$prodId]['entradas_por_dia'][$diaKey] = [
                        'dia' => $diaNumero,
                        'mes' => $mesNombre,
                        'FR' => [],
                        'LPL' => []
                    ];
                }
                if (!isset($datosPorProducto[$prodId]['salidas_por_dia'][$diaKey])) {
                    $datosPorProducto[$prodId]['salidas_por_dia'][$diaKey] = [
                        'dia' => $diaNumero,
                        'mes' => $mesNombre,
                        'cantidades' => []
                    ];
                }
                
                // Entradas
                foreach ($datosFecha['entradas'] as $entrada) {
                    // Detectar si es FR o LPL: si el total es >= 65000 es LPL, sino es FR
                    $montoTotal = $entrada['total'] ?? ($entrada['precio_unitario'] ?? 0) * $entrada['cantidad'];
                    $tipo = ($montoTotal >= 65000) ? 'LPL' : 'FR';
                    $datosPorProducto[$prodId]['entradas_por_mes'][$mesKey][$tipo] += $entrada['cantidad'];
                    $datosPorProducto[$prodId]['entradas_detalle_por_mes'][$mesKey][$tipo][] = $entrada['cantidad'];
                    $datosPorProducto[$prodId]['entradas_por_dia'][$diaKey][$tipo][] = $entrada['cantidad'];
                    $datosPorProducto[$prodId]['total_entradas'] += $entrada['cantidad'];
                }
                
                // Salidas
                foreach ($datosFecha['salidas'] as $salida) {
                    $datosPorProducto[$prodId]['salidas_por_mes'][$mesKey] += $salida['cantidad'];
                    $datosPorProducto[$prodId]['salidas_detalle_por_mes'][$mesKey][] = $salida['cantidad'];
                    $datosPorProducto[$prodId]['salidas_por_dia'][$diaKey]['cantidades'][] = $salida['cantidad'];
                    $datosPorProducto[$prodId]['total_salidas'] += $salida['cantidad'];
                }
            }
        }
        
        // Generar filas de datos - UNA SOLA PESTAÑA
        foreach ($datosPorProducto as $prodId => $datos) {
            $colIndex = 1; // Empezar en columna A
            
            // Verificar si el producto tiene tanto entradas como salidas
            $tieneEntradasYSalidas = ($datos['total_entradas'] > 0 && $datos['total_salidas'] > 0);
            
            // Producto
            $col = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($col . $row, $datos['nombre']);
            
            // Si tiene entradas y salidas, poner fondo azul
            if ($tieneEntradasYSalidas) {
                $sheet->getStyle($col . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D9E1F2'] // Azul claro
                    ]
                ]);
            }
            $colIndex++;
            
            // GPO / Fam (categoría)
            $col = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($col . $row, $datos['categoria']);
            $colIndex++;
            
            // U.M.
            $col = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($col . $row, $datos['unidad_medida']);
            $colIndex++;
            
            // Existencia Inicial (columna D)
            $colStockInicial = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($colStockInicial . $row, $datos['stock_inicial']);
            $colIndex++;
            
            // Guardar la primera columna FR para la fórmula de Total Entradas
            $colPrimeraFR = Coordinate::stringFromColumnIndex($colIndex);
            
            // Entradas por mes (FR y LPL)
            foreach ($meses as $mes) {
                $mesKey = $mes['ano'] . '-' . $mes['mes'];
                $entradasMes = $datos['entradas_por_mes'][$mesKey] ?? ['FR' => 0, 'LPL' => 0];
                $entradasDetalle = $datos['entradas_detalle_por_mes'][$mesKey] ?? ['FR' => [], 'LPL' => []];
                
                // FR - Crear fórmula si hay datos
                $col = Coordinate::stringFromColumnIndex($colIndex);
                if (!empty($entradasDetalle['FR']) && count($entradasDetalle['FR']) > 0) {
                    $formulaFR = '=' . implode('+', $entradasDetalle['FR']);
                    $sheet->setCellValue($col . $row, $formulaFR);
                } else {
                    $sheet->setCellValue($col . $row, '');
                }
                $colIndex++;
                
                // LPL - Crear fórmula si hay datos
                $col = Coordinate::stringFromColumnIndex($colIndex);
                if (!empty($entradasDetalle['LPL']) && count($entradasDetalle['LPL']) > 0) {
                    $formulaLPL = '=' . implode('+', $entradasDetalle['LPL']);
                    $sheet->setCellValue($col . $row, $formulaLPL);
                } else {
                    $sheet->setCellValue($col . $row, '');
                }
                $colIndex++;
            }
            
            // Guardar la última columna LPL y primera columna de Salidas para las fórmulas
            $colUltimaLPL = Coordinate::stringFromColumnIndex($colIndex - 1);
            $colPrimeraSalida = Coordinate::stringFromColumnIndex($colIndex);
            
            // Salidas por mes
            foreach ($meses as $mes) {
                $mesKey = $mes['ano'] . '-' . $mes['mes'];
                $salidasMes = $datos['salidas_por_mes'][$mesKey] ?? 0;
                $salidasDetalle = $datos['salidas_detalle_por_mes'][$mesKey] ?? [];
                $col = Coordinate::stringFromColumnIndex($colIndex);
                if (!empty($salidasDetalle) && count($salidasDetalle) > 0) {
                    $formulaSalidas = '=' . implode('+', $salidasDetalle);
                    $sheet->setCellValue($col . $row, $formulaSalidas);
                } else {
                    $sheet->setCellValue($col . $row, '');
                }
                $colIndex++;
            }
            
            // Guardar la última columna de Salidas
            $colUltimaSalida = Coordinate::stringFromColumnIndex($colIndex - 1);
            
            // Total Entradas = Suma de todas las columnas FR + LPL
            $colTotalEntradas = Coordinate::stringFromColumnIndex($colIndex);
            $formulaTotalEntradas = '=SUM(' . $colPrimeraFR . $row . ':' . $colUltimaLPL . $row . ')';
            $sheet->setCellValue($colTotalEntradas . $row, $formulaTotalEntradas);
            $colIndex++;
            
            // Desglose diario - crear columnas por día con fórmulas (después de Total Entradas)
            foreach ($diasConMovimientos as $diaKey => $diaInfo) {
                $col = Coordinate::stringFromColumnIndex($colIndex);
                $formulaDia = '';
                
                // Verificar si hay salidas para este día
                if (isset($datos['salidas_por_dia'][$diaKey]) && !empty($datos['salidas_por_dia'][$diaKey]['cantidades'])) {
                    $formulaDia = '=' . implode('+', $datos['salidas_por_dia'][$diaKey]['cantidades']);
                }
                // Verificar si hay entradas para este día
                elseif (isset($datos['entradas_por_dia'][$diaKey])) {
                    $entradasDia = [];
                    if (!empty($datos['entradas_por_dia'][$diaKey]['FR'])) {
                        $entradasDia[] = implode('+', $datos['entradas_por_dia'][$diaKey]['FR']);
                    }
                    if (!empty($datos['entradas_por_dia'][$diaKey]['LPL'])) {
                        $entradasDia[] = implode('+', $datos['entradas_por_dia'][$diaKey]['LPL']);
                    }
                    if (!empty($entradasDia)) {
                        $formulaDia = '=' . implode('+', $entradasDia);
                    }
                }
                
                if (!empty($formulaDia)) {
                    $sheet->setCellValue($col . $row, $formulaDia);
                } else {
                    $sheet->setCellValue($col . $row, '');
                }
                $colIndex++;
            }
            
            // Total Salidas = Suma de todas las columnas de Salidas
            $colTotalSalidas = Coordinate::stringFromColumnIndex($colIndex);
            $formulaTotalSalidas = '=SUM(' . $colPrimeraSalida . $row . ':' . $colUltimaSalida . $row . ')';
            $sheet->setCellValue($colTotalSalidas . $row, $formulaTotalSalidas);
            $colIndex++;
            
            // Saldo Final = Existencia Inicial + Total Entradas - Total Salidas
            $colSaldoFinal = Coordinate::stringFromColumnIndex($colIndex);
            $formulaSaldoFinal = '=' . $colStockInicial . $row . '+' . $colTotalEntradas . $row . '-' . $colTotalSalidas . $row;
            $sheet->setCellValue($colSaldoFinal . $row, $formulaSaldoFinal);
            
            // Aplicar formato: números negativos en rojo
            $saldoCalculado = $datos['stock_inicial'] + $datos['total_entradas'] - $datos['total_salidas'];
            if ($saldoCalculado < 0) {
                $sheet->getStyle($colSaldoFinal . $row)->applyFromArray([
                    'font' => [
                        'color' => ['rgb' => 'FF0000'] // Rojo
                    ],
                    'numberFormat' => [
                        'formatCode' => '#,##0;[Red]-#,##0' // Formato con negativos en rojo
                    ]
                ]);
            } else {
                $sheet->getStyle($colSaldoFinal . $row)->applyFromArray([
                    'numberFormat' => [
                        'formatCode' => NumberFormat::FORMAT_NUMBER
                    ]
                ]);
            }
            
            $colIndex++;
            $row++;
        }
        
        // Ajustar ancho de columnas de días
        for ($i = $colInicioDesglose; $i < $colIndex; $i++) {
            $col = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($col)->setWidth(12);
        }
        
        // Aplicar formato condicional para números negativos en todas las celdas numéricas
        // Esto se aplicará a las columnas de saldo final y otras columnas numéricas
        $rowInicioDatos = 4; // Fila donde empiezan los datos (después de encabezados)
        $rowFinDatos = $row - 1; // Última fila con datos
        
        if ($rowFinDatos >= $rowInicioDatos) {
            // Aplicar formato a la columna de Saldo Final
            $colSaldoFinal = Coordinate::stringFromColumnIndex($colIndex - 1);
            $rangeSaldoFinal = $colSaldoFinal . $rowInicioDatos . ':' . $colSaldoFinal . $rowFinDatos;
            
            // Aplicar formato de número con color rojo para negativos
            $sheet->getStyle($rangeSaldoFinal)->applyFromArray([
                'numberFormat' => [
                    'formatCode' => '#,##0;[Red]-#,##0' // Formato con negativos en rojo
                ]
            ]);
        }
        
        // Ajustar ancho de columnas
        $colIndex = 1;
        $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colIndex++))->setWidth(40); // Producto
        $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colIndex++))->setWidth(15); // GPO/Fam
        $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colIndex++))->setWidth(12); // U.M.
        $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colIndex++))->setWidth(15); // Existencia Inicial
        
        // Ajustar columnas de meses dinámicamente
        foreach ($meses as $mes) {
            $colIndex++; // Saltar columna del título merged
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colIndex++))->setWidth(10); // FR
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colIndex++))->setWidth(10); // LPL
        }
        foreach ($meses as $mes) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colIndex++))->setWidth(12); // Salidas
        }
        $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colIndex++))->setWidth(15); // Total Entradas
        $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colIndex++))->setWidth(15); // Total Salidas
        $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colIndex++))->setWidth(15); // Saldo Final
        
        // Bordes en todas las celdas con datos
        if ($row > 3) {
            $sheet->getStyle('A3:' . $lastCol . ($row - 1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ]);
        }
        
        // Centrar el título "KARDEX DE INVENTARIO" usando la última columna real
        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 18],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // Nombre del archivo
        $nombreArchivo = 'Kardex_Inventario_' . $fechaInicio . '_' . $fechaFin . '.xlsx';
        
        // Descargar
        $response = new StreamedResponse(function() use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });
        
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $nombreArchivo . '"');
        $response->headers->set('Cache-Control', 'max-age=0');
        
        return $response;
    }
}


