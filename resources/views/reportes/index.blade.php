@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
<style>
    @media print {
        /* Ocultar elementos innecesarios al imprimir */
        .no-print {
            display: none !important;
        }
        
        /* Ajustar márgenes */
        body {
            margin: 0;
            padding: 20px;
        }
        
        /* Mejorar formato de impresión */
        .print-container {
            page-break-inside: avoid;
        }
        
        /* Mejorar colores para impresión */
        .bg-blue-50, .bg-green-50, .bg-yellow-50, .bg-red-50 {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        /* Asegurar que los bordes se impriman */
        .border-l-4 {
            border-left-width: 4px !important;
        }
        
        /* Mejorar espaciado */
        .print-spacing {
            margin-bottom: 15px;
        }
        
        /* Título principal */
        h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        /* Tablas */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        
        th {
            background-color: #f0f0f0 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<div class="px-4 sm:px-0">
    <div class="flex justify-between items-center mb-6 no-print">
        <h1 class="text-3xl font-bold text-gray-900">Reportes de Inventario</h1>
        <div class="flex gap-3">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                🖨️ Imprimir
            </button>
            <a href="{{ route('reportes.kardex') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                📋 Ver Kardex
            </a>
        </div>
    </div>
    
    <!-- Título para impresión -->
    <div class="print-container" style="display: none;">
        <h1 class="text-3xl font-bold text-gray-900 text-center mb-2" style="display: block;">Reportes de Inventario</h1>
        <p class="text-center text-gray-600 mb-4" style="display: block;">
            @if($periodo === 'mensual')
                Período: {{ \Carbon\Carbon::parse($mes . '-01')->format('F Y') }}
            @else
                Período: Año {{ $ano }}
            @endif
        </p>
    </div>

    <div class="bg-white shadow sm:rounded-lg mb-6 no-print">
        <form method="GET" action="{{ route('reportes.index') }}" class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Período</label>
                    <select name="periodo" id="periodo" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="mensual" {{ $periodo === 'mensual' ? 'selected' : '' }}>Mensual</option>
                        <option value="anual" {{ $periodo === 'anual' ? 'selected' : '' }}>Anual</option>
                    </select>
                </div>
                @if($periodo === 'mensual')
                <div>
                    <label class="block text-sm font-medium text-gray-700">Mes</label>
                    <input type="month" name="mes" value="{{ $mes }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                @else
                <div>
                    <label class="block text-sm font-medium text-gray-700">Año</label>
                    <input type="number" name="ano" value="{{ $ano }}" min="2020" max="{{ date('Y') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700">Filtrar por</label>
                    <select name="tipo_filtro" id="tipo_filtro" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="general" {{ $tipoFiltro === 'general' ? 'selected' : '' }}>General</option>
                        <option value="producto" {{ $tipoFiltro === 'producto' ? 'selected' : '' }}>Producto Específico</option>
                        <option value="categoria" {{ $tipoFiltro === 'categoria' ? 'selected' : '' }}>Categoría/Familia</option>
                    </select>
                </div>
                <div id="filtro-producto" style="display: {{ $tipoFiltro === 'producto' ? 'block' : 'none' }};">
                    <label class="block text-sm font-medium text-gray-700">Producto</label>
                    <div class="relative">
                        <input type="text" 
                               id="producto_buscar_reportes" 
                               name="producto_buscar"
                               value="{{ old('producto_buscar') }}"
                               placeholder="Escribe el nombre o código del producto..."
                               autocomplete="off"
                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <input type="hidden" name="producto_id" id="producto_id_reportes" value="{{ $productoId }}">
                        <div id="producto_suggestions_reportes" class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-auto"></div>
                    </div>
                    @if($productoId)
                        @php
                            $productoSeleccionado = $productos->firstWhere('id', $productoId);
                        @endphp
                        @if($productoSeleccionado)
                            <div id="producto_selected_reportes" class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded-md">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-900">{{ $productoSeleccionado->nombre }}</span>
                                    <button type="button" id="producto_clear_reportes" class="text-blue-600 hover:text-blue-800 text-sm">Cambiar</button>
                                </div>
                                <div class="text-xs text-gray-600 mt-1">Código: {{ $productoSeleccionado->codigo }}</div>
                            </div>
                        @endif
                    @endif
                </div>
                <div id="filtro-categoria" style="display: {{ $tipoFiltro === 'categoria' ? 'block' : 'none' }};">
                    <label class="block text-sm font-medium text-gray-700">Categoría/Familia</label>
                    <select name="categoria" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Todas las categorías</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat }}" {{ $categoria == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Generar Reporte
                </button>
            </div>
        </form>
    </div>

    @if($entradasPorProducto->count() === 0 && $salidasPorProducto->count() === 0)
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    No se encontraron entradas ni salidas para el período seleccionado. 
                    Asegúrate de seleccionar el mes o año correcto en el filtro superior.
                </p>
            </div>
        </div>
    </div>
    @endif

    @if($entradasPorProducto->count() > 0)
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Entradas por Producto</h3>
            <p class="mt-1 text-sm text-gray-500">Detalle de entradas registradas en el período seleccionado con promedios mensuales y anuales</p>
        </div>
        <div class="border-t border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Entradas</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Promedio Mensual</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Promedio Anual</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($entradasPorProducto as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->nombre }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->codigo }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->categoria ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-bold">{{ number_format($item->total_entradas, 0) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 text-right font-semibold">
                                {{ isset($promediosPorProducto[$item->codigo]) ? number_format($promediosPorProducto[$item->codigo]['promedio_mensual_entradas'], 2) : '0.00' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 text-right font-semibold">
                                {{ isset($promediosPorProducto[$item->codigo]) ? number_format($promediosPorProducto[$item->codigo]['promedio_anual_entradas'], 2) : '0.00' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if($salidasPorProducto->count() > 0)
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Salidas por Producto</h3>
            <p class="mt-1 text-sm text-gray-500">Detalle de salidas registradas en el período seleccionado con promedios mensuales y anuales</p>
        </div>
        <div class="border-t border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Salidas</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Promedio Mensual</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Promedio Anual</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($salidasPorProducto as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->nombre }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->codigo }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->categoria ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-bold">{{ number_format($item->total_salidas, 0) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 text-right font-semibold">
                                {{ isset($promediosPorProducto[$item->codigo]) ? number_format($promediosPorProducto[$item->codigo]['promedio_mensual_salidas'], 2) : '0.00' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 text-right font-semibold">
                                {{ isset($promediosPorProducto[$item->codigo]) ? number_format($promediosPorProducto[$item->codigo]['promedio_anual_salidas'], 2) : '0.00' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if(isset($promediosPorCategoria) && count($promediosPorCategoria) > 0)
    <div class="bg-gradient-to-r from-purple-50 to-pink-50 border-l-4 border-purple-500 shadow-lg mb-6">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">📊 Promedios por Categoría/Familia</h3>
            <p class="mt-1 text-sm text-gray-500">Promedios mensuales y anuales agrupados por categoría o familia de productos</p>
        </div>
        <div class="border-t border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-purple-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Categoría/Familia</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-purple-800 uppercase tracking-wider">Total Entradas</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-purple-800 uppercase tracking-wider">Promedio Mensual Entradas</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-purple-800 uppercase tracking-wider">Promedio Anual Entradas</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-purple-800 uppercase tracking-wider">Total Salidas</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-purple-800 uppercase tracking-wider">Promedio Mensual Salidas</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-purple-800 uppercase tracking-wider">Promedio Anual Salidas</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($promediosPorCategoria as $cat => $promedios)
                        <tr class="hover:bg-purple-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $cat }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($promedios['total_entradas'], 0) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 text-right font-semibold">{{ number_format($promedios['promedio_mensual_entradas'], 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 text-right font-semibold">{{ number_format($promedios['promedio_anual_entradas'], 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($promedios['total_salidas'], 0) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 text-right font-semibold">{{ number_format($promedios['promedio_mensual_salidas'], 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 text-right font-semibold">{{ number_format($promedios['promedio_anual_salidas'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Promedios Mensuales y Anuales - Sección Destacada -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 shadow-lg mb-6">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">📊 Promedios Históricos Generales</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg p-4 shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <dt class="text-sm font-medium text-gray-500">Promedio Mensual de Salidas</dt>
                            <dd class="mt-1 text-3xl font-bold text-blue-600">{{ number_format($promedioMensualSalidasHistorico, 2) }} <span class="text-lg text-gray-500">unidades/mes</span></dd>
                            <p class="text-xs text-gray-400 mt-1">Basado en el promedio del año actual</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <dt class="text-sm font-medium text-gray-500">Promedio Anual de Salidas</dt>
                            <dd class="mt-1 text-3xl font-bold text-green-600">{{ number_format($promedioAnualSalidasHistorico, 2) }} <span class="text-lg text-gray-500">unidades/año</span></dd>
                            <p class="text-xs text-gray-400 mt-1">Promedio histórico de todos los años</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 pt-4 border-t border-blue-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-lg p-4 shadow">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <dt class="text-sm font-medium text-gray-500">Promedio Mensual de Entradas</dt>
                                <dd class="mt-1 text-3xl font-bold text-purple-600">{{ number_format($promedioMensualEntradasHistorico, 2) }} <span class="text-lg text-gray-500">unidades/mes</span></dd>
                                <p class="text-xs text-gray-400 mt-1">Basado en el promedio del año actual</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <dt class="text-sm font-medium text-gray-500">Promedio Anual de Entradas</dt>
                                <dd class="mt-1 text-3xl font-bold text-orange-600">{{ number_format($promedioAnualEntradasHistorico, 2) }} <span class="text-lg text-gray-500">unidades/año</span></dd>
                                <p class="text-xs text-gray-400 mt-1">Promedio histórico de todos los años</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-6">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Resumen de Entradas</h3>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Total Entradas</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($totalEntradas, 0) }} unidades</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Promedio Diario</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($promedioDiarioEntradas, 2) }} unidades</dd>
                    </div>
                    @if($periodo === 'mensual')
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Total del Mes</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($totalEntradas, 0) }} unidades</dd>
                    </div>
                    @else
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Promedio Mensual</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($promedioMensualEntradas, 2) }} unidades</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Promedio Anual</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($promedioAnualEntradas, 2) }} unidades</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Resumen de Salidas</h3>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Total Salidas</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($totalSalidas, 0) }} unidades</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Promedio Diario</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($promedioDiarioSalidas, 2) }} unidades</dd>
                    </div>
                    @if($periodo === 'mensual')
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Total del Mes</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($totalSalidas, 0) }} unidades</dd>
                    </div>
                    @else
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Promedio Mensual</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($promedioMensualSalidas, 2) }} unidades</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Promedio Anual</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($promedioAnualSalidas, 2) }} unidades</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-6">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Productos con Stock Mínimo</h3>
            </div>
            <div class="border-t border-gray-200">
                <ul class="divide-y divide-gray-200">
                    @forelse($productosMinimos as $producto)
                        <li class="px-4 py-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $producto->nombre }}</p>
                                    <p class="text-sm text-gray-500">Stock: {{ $producto->stock_actual }} / Mínimo: {{ $producto->stock_minimo }}</p>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-4 py-4 text-center text-gray-500 text-sm">No hay productos con stock mínimo</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Productos con Mayor Stock</h3>
            </div>
            <div class="border-t border-gray-200">
                <ul class="divide-y divide-gray-200">
                    @forelse($productosMaximos as $producto)
                        <li class="px-4 py-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $producto->nombre }}</p>
                                    <p class="text-sm text-gray-500">Stock: {{ $producto->stock_actual }} {{ $producto->unidad_medida }}</p>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-4 py-4 text-center text-gray-500 text-sm">No hay productos</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    @if($entradasPorProducto->count() > 0)
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Entradas por Producto</h3>
            <p class="mt-1 text-sm text-gray-500">Detalle de entradas registradas en el período seleccionado con promedios mensuales y anuales</p>
        </div>
        <div class="border-t border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Entradas</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Promedio Mensual</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Promedio Anual</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($entradasPorProducto as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->nombre }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->codigo }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->categoria ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-bold">{{ number_format($item->total_entradas, 0) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 text-right font-semibold">
                                {{ isset($promediosPorProducto[$item->codigo]) ? number_format($promediosPorProducto[$item->codigo]['promedio_mensual_entradas'], 2) : '0.00' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 text-right font-semibold">
                                {{ isset($promediosPorProducto[$item->codigo]) ? number_format($promediosPorProducto[$item->codigo]['promedio_anual_entradas'], 2) : '0.00' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if($salidasPorProducto->count() > 0)
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Salidas por Producto</h3>
            <p class="mt-1 text-sm text-gray-500">Detalle de salidas registradas en el período seleccionado con promedios mensuales y anuales</p>
        </div>
        <div class="border-t border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Salidas</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Promedio Mensual</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Promedio Anual</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($salidasPorProducto as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->nombre }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->codigo }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->categoria ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-bold">{{ number_format($item->total_salidas, 0) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 text-right font-semibold">
                                {{ isset($promediosPorProducto[$item->codigo]) ? number_format($promediosPorProducto[$item->codigo]['promedio_mensual_salidas'], 2) : '0.00' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 text-right font-semibold">
                                {{ isset($promediosPorProducto[$item->codigo]) ? number_format($promediosPorProducto[$item->codigo]['promedio_anual_salidas'], 2) : '0.00' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if(isset($promediosPorCategoria) && count($promediosPorCategoria) > 0)
    <div class="bg-gradient-to-r from-purple-50 to-pink-50 border-l-4 border-purple-500 shadow-lg mb-6">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">📊 Promedios por Categoría/Familia</h3>
            <p class="mt-1 text-sm text-gray-500">Promedios mensuales y anuales agrupados por categoría o familia de productos</p>
        </div>
        <div class="border-t border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-purple-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Categoría/Familia</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-purple-800 uppercase tracking-wider">Total Entradas</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-purple-800 uppercase tracking-wider">Promedio Mensual Entradas</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-purple-800 uppercase tracking-wider">Promedio Anual Entradas</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-purple-800 uppercase tracking-wider">Total Salidas</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-purple-800 uppercase tracking-wider">Promedio Mensual Salidas</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-purple-800 uppercase tracking-wider">Promedio Anual Salidas</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($promediosPorCategoria as $cat => $promedios)
                        <tr class="hover:bg-purple-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $cat }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($promedios['total_entradas'], 0) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 text-right font-semibold">{{ number_format($promedios['promedio_mensual_entradas'], 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 text-right font-semibold">{{ number_format($promedios['promedio_anual_entradas'], 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($promedios['total_salidas'], 0) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 text-right font-semibold">{{ number_format($promedios['promedio_mensual_salidas'], 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 text-right font-semibold">{{ number_format($promedios['promedio_anual_salidas'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if(isset($productosProntoAcabar) && $productosProntoAcabar->count() > 0)
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6 bg-yellow-50 border-l-4 border-yellow-500">
            <h3 class="text-lg leading-6 font-medium text-yellow-800">⚠️ Alerta: Productos con Stock Menor a 100 Unidades</h3>
            <p class="mt-1 text-sm text-yellow-700">Productos que requieren atención por tener stock bajo</p>
        </div>
        <div class="border-t border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-yellow-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-yellow-800 uppercase tracking-wider">Producto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-yellow-800 uppercase tracking-wider">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-yellow-800 uppercase tracking-wider">Categoría</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-yellow-800 uppercase tracking-wider">Stock Actual</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-yellow-800 uppercase tracking-wider">Unidad</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($productosProntoAcabar as $producto)
                        <tr class="hover:bg-yellow-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $producto->nombre }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $producto->codigo }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $producto->categoria ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-red-600 text-right">{{ number_format($producto->stock_actual, 0) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">{{ $producto->unidad_medida }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.getElementById('tipo_filtro').addEventListener('change', function() {
        const tipoFiltro = this.value;
        const filtroProducto = document.getElementById('filtro-producto');
        const filtroCategoria = document.getElementById('filtro-categoria');
        
        if (tipoFiltro === 'producto') {
            filtroProducto.style.display = 'block';
            filtroCategoria.style.display = 'none';
        } else if (tipoFiltro === 'categoria') {
            filtroProducto.style.display = 'none';
            filtroCategoria.style.display = 'block';
        } else {
            filtroProducto.style.display = 'none';
            filtroCategoria.style.display = 'none';
        }
    });

    // Búsqueda de productos con autocomplete
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('producto_buscar_reportes');
        const productoIdInput = document.getElementById('producto_id_reportes');
        const suggestionsDiv = document.getElementById('producto_suggestions_reportes');
        const selectedDiv = document.getElementById('producto_selected_reportes');
        const clearBtn = document.getElementById('producto_clear_reportes');
        let searchTimeout;

        if (!searchInput) return;

        function showSelected(nombre, codigo, id) {
            if (!selectedDiv) return;
            selectedDiv.innerHTML = `
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-900">${nombre}</span>
                    <button type="button" id="producto_clear_reportes" class="text-blue-600 hover:text-blue-800 text-sm">Cambiar</button>
                </div>
                <div class="text-xs text-gray-600 mt-1">Código: ${codigo}</div>
            `;
            selectedDiv.classList.remove('hidden');
            searchInput.classList.add('hidden');
            productoIdInput.value = id;
            
            // Re-attach clear button listener
            const newClearBtn = document.getElementById('producto_clear_reportes');
            if (newClearBtn) {
                newClearBtn.addEventListener('click', function() {
                    productoIdInput.value = '';
                    searchInput.value = '';
                    if (selectedDiv) selectedDiv.classList.add('hidden');
                    searchInput.classList.remove('hidden');
                    searchInput.focus();
                });
            }
        }

        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                suggestionsDiv.classList.add('hidden');
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`{{ route('api.productos.search') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(productos => {
                        if (productos.length === 0) {
                            suggestionsDiv.innerHTML = '<div class="p-3 text-sm text-gray-500">No se encontraron productos</div>';
                            suggestionsDiv.classList.remove('hidden');
                            return;
                        }

                        suggestionsDiv.innerHTML = productos.map(producto => `
                            <div class="p-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0" 
                                 data-id="${producto.id}" 
                                 data-nombre="${producto.nombre}"
                                 data-codigo="${producto.codigo}">
                                <div class="font-medium text-gray-900">${producto.nombre}</div>
                                <div class="text-xs text-gray-500">Código: ${producto.codigo} | Stock: ${producto.stock_actual}</div>
                            </div>
                        `).join('');

                        suggestionsDiv.classList.remove('hidden');

                        // Agregar event listeners a las sugerencias
                        suggestionsDiv.querySelectorAll('div[data-id]').forEach(item => {
                            item.addEventListener('click', function() {
                                const id = this.getAttribute('data-id');
                                const nombre = this.getAttribute('data-nombre');
                                const codigo = this.getAttribute('data-codigo');
                                selectProducto(id, nombre, codigo);
                            });
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        suggestionsDiv.innerHTML = '<div class="p-3 text-sm text-red-500">Error al buscar productos</div>';
                        suggestionsDiv.classList.remove('hidden');
                    });
            }, 300);
        });

        function selectProducto(id, nombre, codigo) {
            productoIdInput.value = id;
            searchInput.value = nombre;
            suggestionsDiv.classList.add('hidden');
            showSelected(nombre, codigo, id);
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                productoIdInput.value = '';
                searchInput.value = '';
                if (selectedDiv) selectedDiv.classList.add('hidden');
                searchInput.classList.remove('hidden');
                searchInput.focus();
            });
        }

        // Cerrar sugerencias al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (searchInput && !searchInput.contains(e.target) && suggestionsDiv && !suggestionsDiv.contains(e.target)) {
                suggestionsDiv.classList.add('hidden');
            }
        });

        // Navegación con teclado
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp' || e.key === 'Enter') {
                const items = suggestionsDiv.querySelectorAll('div[data-id]');
                if (items.length === 0) return;
                
                let currentIndex = -1;
                items.forEach((item, index) => {
                    if (item.classList.contains('bg-blue-100')) {
                        currentIndex = index;
                    }
                });

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    currentIndex = (currentIndex + 1) % items.length;
                    items.forEach(item => item.classList.remove('bg-blue-100'));
                    items[currentIndex].classList.add('bg-blue-100');
                    items[currentIndex].scrollIntoView({ block: 'nearest' });
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    currentIndex = currentIndex <= 0 ? items.length - 1 : currentIndex - 1;
                    items.forEach(item => item.classList.remove('bg-blue-100'));
                    items[currentIndex].classList.add('bg-blue-100');
                    items[currentIndex].scrollIntoView({ block: 'nearest' });
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (currentIndex >= 0) {
                        const item = items[currentIndex];
                        const id = item.getAttribute('data-id');
                        const nombre = item.getAttribute('data-nombre');
                        const codigo = item.getAttribute('data-codigo');
                        selectProducto(id, nombre, codigo);
                    }
                }
            }
        });
    });
</script>

<script>
    // Mostrar título al imprimir
    window.addEventListener('beforeprint', function() {
        const printTitle = document.querySelector('.print-container');
        if (printTitle) {
            printTitle.style.display = 'block';
        }
    });
    
    window.addEventListener('afterprint', function() {
        const printTitle = document.querySelector('.print-container');
        if (printTitle) {
            printTitle.style.display = 'none';
        }
    });
</script>
@endpush
@endsection


