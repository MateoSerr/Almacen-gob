@extends('layouts.app')

@section('title', 'Kardex de Inventario')

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
        
        /* Evitar que se corten las secciones */
        .producto-section {
            page-break-inside: avoid;
            margin-bottom: 20px;
        }
        
        /* Mejorar colores para impresión */
        .bg-blue-50 {
            background-color: #f0f0f0 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        .bg-green-50 {
            background-color: #f0f8f0 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        .bg-red-50 {
            background-color: #fff0f0 !important;
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
        
        /* Encabezados de producto */
        h2 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        /* Fechas */
        h3 {
            font-size: 16px;
            margin-bottom: 10px;
        }
    }
</style>

<div class="px-4 sm:px-0">
    <div class="flex justify-between items-center mb-6 no-print">
        <h1 class="text-3xl font-bold text-gray-900">Kardex de Inventario</h1>
        <div class="flex gap-3">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center shadow-lg border-2 border-blue-800">
                🖨️ Imprimir
            </button>
            <a href="{{ route('reportes.kardex.excel', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin] + ($productoId ? ['producto_id' => $productoId] : [])) }}" 
               style="background-color: #16a34a; color: white; font-weight: bold; padding: 0.5rem 1rem; border-radius: 0.375rem; display: inline-flex; align-items: center; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border: 2px solid #166534; text-decoration: none;"
               onmouseover="this.style.backgroundColor='#15803d'; this.style.transform='scale(1.05)'"
               onmouseout="this.style.backgroundColor='#16a34a'; this.style.transform='scale(1)'">
                📥 Descargar Excel
            </a>
            <a href="{{ route('reportes.index') }}" class="text-blue-600 hover:text-blue-800">
                ← Volver a Reportes
            </a>
        </div>
    </div>
    
    <!-- Título para impresión -->
    <div class="print-container" style="display: none;">
        <h1 class="text-3xl font-bold text-gray-900 text-center mb-2" style="display: block;">Kardex de Inventario</h1>
        <p class="text-center text-gray-600 mb-4" style="display: block;">
            Período: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
        </p>
    </div>

    <!-- Filtros -->
    <div class="bg-white shadow sm:rounded-lg mb-6 no-print">
        <form method="GET" action="{{ route('reportes.kardex') }}" class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}" 
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                    <input type="date" name="fecha_fin" value="{{ $fechaFin }}" 
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Producto (Opcional)</label>
                    <div class="relative">
                        <input type="text" 
                               id="producto_buscar_kardex" 
                               placeholder="Escribe para buscar un producto o deja vacío para todos..."
                               autocomplete="off"
                               value="{{ $productoId ? ($productos->firstWhere('id', $productoId)->nombre ?? '') . ' (' . ($productos->firstWhere('id', $productoId)->codigo ?? '') . ')' : '' }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <input type="hidden" name="producto_id" id="producto_id_kardex" value="{{ $productoId ?? '' }}">
                        <div id="producto_suggestions_kardex" class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto"></div>
                    </div>
                    @if($productoId)
                        <button type="button" id="limpiar_producto_kardex" class="mt-2 text-sm text-blue-600 hover:text-blue-800">
                            ✕ Limpiar selección
                        </button>
                    @endif
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Contenido del Kardex -->
    @if(count($kardex) > 0)
        @foreach($kardex as $productoId => $productoData)
            <div class="bg-white shadow sm:rounded-lg mb-6 overflow-hidden producto-section print-container">
                <!-- Encabezado del Producto -->
                <div class="bg-blue-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">
                        {{ $productoData['producto_nombre'] }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Unidad de Medida: <span class="font-medium">{{ $productoData['unidad_medida'] }}</span>
                    </p>
                </div>

                <!-- Movimientos por Fecha -->
                <div class="divide-y divide-gray-200">
                    @foreach($productoData['fechas'] as $fecha => $datosFecha)
                        <div class="px-6 py-4 print-spacing" style="page-break-inside: avoid;">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    {{ $datosFecha['fecha'] }}
                                </h3>
                                <div class="flex gap-4 text-sm">
                                    @if($datosFecha['total_entradas'] > 0)
                                        <span class="text-green-600 font-medium">
                                            Entradas: {{ $datosFecha['total_entradas'] }} {{ $productoData['unidad_medida'] }}
                                        </span>
                                    @endif
                                    @if($datosFecha['total_salidas'] > 0)
                                        <span class="text-red-600 font-medium">
                                            Salidas: {{ $datosFecha['total_salidas'] }} {{ $productoData['unidad_medida'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Entradas del día -->
                            @if(count($datosFecha['entradas']) > 0)
                                <div class="mb-4">
                                    <h4 class="text-sm font-semibold text-green-700 mb-2 flex items-center">
                                        <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                        Entradas
                                    </h4>
                                    <div class="ml-4 space-y-2">
                                        @foreach($datosFecha['entradas'] as $entrada)
                                            <div class="bg-green-50 border-l-4 border-green-400 p-3 rounded">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <span class="font-medium text-gray-900">
                                                            {{ $entrada['cantidad'] }} {{ $productoData['unidad_medida'] }}
                                                        </span>
                                                        <span class="text-gray-600 ml-2">
                                                            - Folio: <span class="font-mono">{{ $entrada['folio'] }}</span>
                                                        </span>
                                                    </div>
                                                </div>
                                                @if($entrada['proveedor'])
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        Proveedor: {{ $entrada['proveedor'] }}
                                                        @if($entrada['numero_factura'])
                                                            | Factura: {{ $entrada['numero_factura'] }}
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Salidas del día -->
                            @if(count($datosFecha['salidas']) > 0)
                                <div>
                                    <h4 class="text-sm font-semibold text-red-700 mb-2 flex items-center">
                                        <span class="inline-block w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                        Salidas
                                    </h4>
                                    <div class="ml-4 space-y-2">
                                        @foreach($datosFecha['salidas'] as $salida)
                                            <div class="bg-red-50 border-l-4 border-red-400 p-3 rounded">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <span class="font-medium text-gray-900">
                                                            {{ $salida['cantidad'] }} {{ $productoData['unidad_medida'] }}
                                                        </span>
                                                        <span class="text-gray-600 ml-2">
                                                            - Folio: <span class="font-mono">{{ $salida['folio'] }}</span>
                                                        </span>
                                                    </div>
                                                </div>
                                                @if($salida['motivo'] || $salida['destino'])
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        @if($salida['motivo'])
                                                            Motivo: {{ $salida['motivo'] }}
                                                        @endif
                                                        @if($salida['destino'])
                                                            @if($salida['motivo']) | @endif
                                                            Destino: {{ $salida['destino'] }}
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @else
        <div class="bg-white shadow sm:rounded-lg p-8 text-center">
            <p class="text-gray-500 text-lg">No se encontraron movimientos para el período seleccionado.</p>
        </div>
    @endif
</div>

<script>
    // Búsqueda de productos con autocompletado
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('producto_buscar_kardex');
        const suggestionsDiv = document.getElementById('producto_suggestions_kardex');
        const productoIdInput = document.getElementById('producto_id_kardex');
        const limpiarBtn = document.getElementById('limpiar_producto_kardex');
        let searchTimeout;

        if (searchInput && suggestionsDiv && productoIdInput) {
            // Buscar productos
            function loadProductos(query = '') {
                clearTimeout(searchTimeout);
                
                if (query.trim().length < 2) {
                    suggestionsDiv.classList.add('hidden');
                    // Si está vacío, limpiar selección
                    if (query.trim().length === 0) {
                        productoIdInput.value = '';
                        if (limpiarBtn) limpiarBtn.style.display = 'none';
                    }
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
                                     data-codigo="${producto.codigo || ''}">
                                    <div class="font-medium text-gray-900">${producto.nombre}</div>
                                    <div class="text-xs text-gray-500">Código: ${producto.codigo || 'N/A'}</div>
                                </div>
                            `).join('');

                            suggestionsDiv.classList.remove('hidden');

                            // Agregar event listeners
                            suggestionsDiv.querySelectorAll('div[data-id]').forEach(item => {
                                item.addEventListener('click', function() {
                                    const id = this.getAttribute('data-id');
                                    const nombre = this.getAttribute('data-nombre');
                                    const codigo = this.getAttribute('data-codigo');
                                    
                                    productoIdInput.value = id;
                                    searchInput.value = nombre + (codigo ? ' (' + codigo + ')' : '');
                                    suggestionsDiv.classList.add('hidden');
                                    
                                    if (limpiarBtn) limpiarBtn.style.display = 'block';
                                });
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            suggestionsDiv.innerHTML = '<div class="p-3 text-sm text-red-500">Error al buscar productos</div>';
                            suggestionsDiv.classList.remove('hidden');
                        });
                }, 300);
            }

            // Event listener para búsqueda
            searchInput.addEventListener('input', function() {
                loadProductos(this.value);
            });

            // Botón limpiar
            if (limpiarBtn) {
                limpiarBtn.addEventListener('click', function() {
                    productoIdInput.value = '';
                    searchInput.value = '';
                    suggestionsDiv.classList.add('hidden');
                    limpiarBtn.style.display = 'none';
                });
            }

            // Cerrar sugerencias al hacer clic fuera
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
                    suggestionsDiv.classList.add('hidden');
                }
            });
        }
    });

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
@endsection

