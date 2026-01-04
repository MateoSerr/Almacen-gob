@extends('layouts.app')

@section('title', 'Editar Salida')

@section('content')
<div class="px-4 sm:px-0">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Editar Salida</h1>

    <div class="bg-white shadow sm:rounded-lg">
        <form method="POST" action="{{ route('salidas.update', $salida) }}" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="producto_buscar" class="block text-sm font-medium text-gray-700">Buscar o Seleccionar Producto *</label>
                    <div class="relative">
                        <div class="flex gap-2">
                            <div class="flex-1 relative">
                                <input type="text" 
                                       id="producto_buscar" 
                                       name="producto_buscar"
                                       value="{{ old('producto_buscar', $salida->producto->nombre) }}"
                                       placeholder="Escribe para buscar o haz clic para ver todos..."
                                       autocomplete="off"
                                       class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <input type="hidden" name="producto_id" id="producto_id" value="{{ old('producto_id', $salida->producto_id) }}" required>
                                <div id="producto_suggestions" class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-96 overflow-y-auto"></div>
                            </div>
                            <button type="button" 
                                    id="ver_todos_btn" 
                                    class="mt-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md border border-gray-300 whitespace-nowrap">
                                Ver Todos
                            </button>
                        </div>
                    </div>
                    <div id="producto_selected" class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded-md {{ old('producto_id', $salida->producto_id) ? '' : 'hidden' }}">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-900">{{ $salida->producto->nombre }}</span>
                            <button type="button" id="producto_clear" class="text-blue-600 hover:text-blue-800 text-sm">Cambiar</button>
                        </div>
                        <div class="text-xs text-gray-600 mt-1">ID: {{ $salida->producto_id }} | Stock disponible: {{ $salida->producto->stock_actual }}</div>
                    </div>
                    @error('producto_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="cantidad" class="block text-sm font-medium text-gray-700">Cantidad *</label>
                    <input type="number" name="cantidad" id="cantidad" value="{{ old('cantidad', $salida->cantidad) }}" required min="1"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('cantidad')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="fecha" class="block text-sm font-medium text-gray-700">Fecha *</label>
                    <input type="date" name="fecha" id="fecha" value="{{ old('fecha', $salida->fecha->format('Y-m-d')) }}" required
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('fecha')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="motivo" class="block text-sm font-medium text-gray-700">Motivo</label>
                    <input type="text" name="motivo" id="motivo" value="{{ old('motivo', $salida->motivo) }}" placeholder="Ej: Uso interno, Venta, Dañado, etc."
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div>
                    <label for="destino" class="block text-sm font-medium text-gray-700">Destino</label>
                    <input type="text" name="destino" id="destino" value="{{ old('destino', $salida->destino) }}"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div class="sm:col-span-2">
                    <label for="observaciones" class="block text-sm font-medium text-gray-700">Observaciones</label>
                    <textarea name="observaciones" id="observaciones" rows="3"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('observaciones', $salida->observaciones) }}</textarea>
                </div>

                <div class="sm:col-span-2 border-t pt-4 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Información de Entrega y Recepción</h3>
                </div>

                <div class="sm:col-span-1">
                    <label for="entrega_nombre" class="block text-sm font-medium text-gray-700">Nombre del que Entrega (Almacén)</label>
                    <input type="text" name="entrega_nombre" id="entrega_nombre" value="{{ old('entrega_nombre', $salida->entrega_nombre) }}"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div class="sm:col-span-1">
                    <label for="recibe_nombre" class="block text-sm font-medium text-gray-700">Nombre del que Recibe (Destino)</label>
                    <input type="text" name="recibe_nombre" id="recibe_nombre" value="{{ old('recibe_nombre', $salida->recibe_nombre) }}"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div class="sm:col-span-1">
                    <label for="entrega_firma" class="block text-sm font-medium text-gray-700">Firma del que Entrega</label>
                    <textarea name="entrega_firma" id="entrega_firma" rows="3" placeholder="Firma o huella digital..."
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('entrega_firma', $salida->entrega_firma) }}</textarea>
                </div>

                <div class="sm:col-span-1">
                    <label for="recibe_firma" class="block text-sm font-medium text-gray-700">Firma del que Recibe</label>
                    <textarea name="recibe_firma" id="recibe_firma" rows="3" placeholder="Firma o huella digital..."
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('recibe_firma', $salida->recibe_firma) }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end space-x-3">
                <a href="{{ route('salidas.show', $salida) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Actualizar Salida
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('producto_buscar');
    const productoIdInput = document.getElementById('producto_id');
    const suggestionsDiv = document.getElementById('producto_suggestions');
    const selectedDiv = document.getElementById('producto_selected');
    const clearBtn = document.getElementById('producto_clear');
    let searchTimeout;

    // Si hay un producto_id previamente seleccionado, cargar su nombre
    @if(old('producto_id', $salida->producto_id))
        const currentProductoId = {{ old('producto_id', $salida->producto_id) }};
        @foreach($productos as $producto)
            if ({{ $producto->id }} === currentProductoId) {
                showSelected('{{ $producto->nombre }}', {{ $producto->stock_actual }}, currentProductoId);
            }
        @endforeach
    @endif

    function loadProductos(query = '', showAll = false) {
        clearTimeout(searchTimeout);
        
        const url = showAll 
            ? `{{ route('api.productos.search') }}?all=true`
            : `{{ route('api.productos.search') }}?q=${encodeURIComponent(query)}`;
        
        fetch(url)
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
                         data-stock="${producto.stock_actual}">
                        <div class="font-medium text-gray-900">${producto.nombre}</div>
                        <div class="text-xs text-gray-500">Código: ${producto.codigo} | Stock disponible: ${producto.stock_actual}</div>
                    </div>
                `).join('');

                suggestionsDiv.classList.remove('hidden');

                // Agregar event listeners a las sugerencias
                suggestionsDiv.querySelectorAll('div[data-id]').forEach(item => {
                    item.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const nombre = this.getAttribute('data-nombre');
                        const stock = this.getAttribute('data-stock');
                        selectProducto(id, nombre, stock);
                    });
                });
            })
            .catch(error => {
                console.error('Error:', error);
                suggestionsDiv.innerHTML = '<div class="p-3 text-sm text-red-500">Error al buscar productos</div>';
                suggestionsDiv.classList.remove('hidden');
            });
    }

    // Mostrar todos los productos al hacer clic en el input
    searchInput.addEventListener('focus', function() {
        if (this.value.trim().length === 0 && productoIdInput.value === '') {
            loadProductos('', true);
        }
    });

    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        if (query.length === 0) {
            suggestionsDiv.classList.add('hidden');
            return;
        }

        if (query.length < 2) {
            suggestionsDiv.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(() => {
            loadProductos(query, false);
        }, 300);
    });

    // Botón "Ver Todos"
    document.getElementById('ver_todos_btn').addEventListener('click', function() {
        searchInput.value = '';
        productoIdInput.value = '';
        loadProductos('', true);
        searchInput.focus();
    });

    function selectProducto(id, nombre, stock) {
        productoIdInput.value = id;
        searchInput.value = nombre;
        suggestionsDiv.classList.add('hidden');
        showSelected(nombre, stock, id);
    }

    function showSelected(nombre, stock, id) {
        selectedDiv.querySelector('span').textContent = nombre;
        selectedDiv.querySelector('div.text-xs').textContent = `ID: ${id} | Stock disponible: ${stock}`;
        selectedDiv.classList.remove('hidden');
        searchInput.classList.add('hidden');
    }

    clearBtn.addEventListener('click', function() {
        productoIdInput.value = '';
        searchInput.value = '';
        selectedDiv.classList.add('hidden');
        searchInput.classList.remove('hidden');
        searchInput.focus();
    });

    // Cerrar sugerencias al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
            suggestionsDiv.classList.add('hidden');
        }
    });

    // Seleccionar primera sugerencia con Enter
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !suggestionsDiv.classList.contains('hidden')) {
            const firstSuggestion = suggestionsDiv.querySelector('div[data-id]');
            if (firstSuggestion) {
                e.preventDefault();
                firstSuggestion.click();
            }
        }
    });
});
</script>
@endsection




