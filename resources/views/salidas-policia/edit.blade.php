@extends('layouts.app')

@section('title', 'Editar Entrega a Policía')

@section('content')
<div class="px-4 sm:px-0">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Editar Entrega a Policía</h1>

    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
        <p class="text-sm text-yellow-700">
            <strong>⚠️ Importante:</strong> Al editar, el sistema validará que no se haya entregado el mismo producto a otro policía. 
            Si cambias el producto o el policía, se verificará que no exista una entrega duplicada.
        </p>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow sm:rounded-lg">
        <form method="POST" action="{{ route('salidas-policia.update', $salida) }}" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="policia_buscar" class="block text-sm font-medium text-gray-700">Buscar o Crear Policía *</label>
                    <div class="relative">
                        <div class="flex gap-2">
                            <div class="flex-1 relative">
                                <input type="text" 
                                       id="policia_buscar" 
                                       name="policia_buscar"
                                       value="{{ old('policia_buscar', $salida->policia->nombre_completo ?? '') }}"
                                       placeholder="Escribe el nombre, número de placa o número de empleado del policía..."
                                       autocomplete="off"
                                       class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <input type="hidden" name="policia_id" id="policia_id" value="{{ old('policia_id', $salida->policia_id) }}" required>
                                <div id="policia_suggestions" class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-96 overflow-y-auto"></div>
                            </div>
                            <button type="button" 
                                    id="ver_todos_policias_btn" 
                                    class="mt-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md border border-gray-300 whitespace-nowrap">
                                Ver Todos
                            </button>
                        </div>
                    </div>
                    <div id="policia_selected" class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded-md {{ old('policia_id', $salida->policia_id) ? '' : 'hidden' }}">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-900">{{ $salida->policia->nombre_completo ?? '' }}</span>
                            <button type="button" id="policia_clear" class="text-blue-600 hover:text-blue-800 text-sm">Cambiar</button>
                        </div>
                        <div class="text-xs text-gray-600 mt-1">Placa: {{ $salida->policia->numero_placa ?? '' }} | ID: {{ $salida->policia_id }}</div>
                    </div>
                    <div id="policia_create_form" class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-md hidden">
                        <p class="text-sm text-yellow-800 mb-2">El policía no existe. Completa los datos para crearlo:</p>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Número de Placa *</label>
                                <input type="text" id="policia_numero_placa" class="mt-1 block w-full text-sm border-gray-300 rounded-md" required>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Número de Empleado (Único) *</label>
                                <input type="text" id="policia_numero_empleado" class="mt-1 block w-full text-sm border-gray-300 rounded-md" placeholder="Debe ser único" required>
                                <p class="mt-1 text-xs text-gray-500">Obligatorio. Se usará para verificar duplicados</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Rango</label>
                                <input type="text" id="policia_rango" class="mt-1 block w-full text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Área</label>
                                <input type="text" id="policia_area" class="mt-1 block w-full text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        <button type="button" id="policia_create_btn" class="mt-2 px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded">
                            Crear Policía
                        </button>
                        <button type="button" id="policia_cancel_create" class="mt-2 ml-2 px-3 py-1 bg-gray-300 hover:bg-gray-400 text-gray-800 text-sm font-medium rounded">
                            Cancelar
                        </button>
                    </div>
                    @error('policia_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="producto_buscar" class="block text-sm font-medium text-gray-700">Buscar o Seleccionar Producto (Solo Uniformes/Equipo) *</label>
                    <div class="relative">
                        <div class="flex gap-2">
                            <div class="flex-1 relative">
                                <input type="text" 
                                       id="producto_buscar" 
                                       name="producto_buscar"
                                       value="{{ old('producto_buscar', $salida->producto->nombre) }}"
                                       placeholder="Escribe para buscar uniformes/equipo..."
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
                    <input type="text" name="motivo" id="motivo" value="{{ old('motivo', $salida->motivo) }}" placeholder="Ej: Entrega de uniforme/equipo"
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
                    <label for="recibe_nombre" class="block text-sm font-medium text-gray-700">Nombre del que Recibe (Policía)</label>
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
                <a href="{{ route('salidas-policia.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Actualizar Entrega
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

    function loadProductos(query = '', showAll = false) {
        clearTimeout(searchTimeout);
        
        const url = showAll 
            ? `{{ route('api.productos.search') }}?all=true&uniforme=true`
            : `{{ route('api.productos.search') }}?q=${encodeURIComponent(query)}&uniforme=true`;
        
        fetch(url)
            .then(response => response.json())
            .then(productos => {
                if (productos.length === 0) {
                    suggestionsDiv.innerHTML = '<div class="p-3 text-sm text-gray-500">No se encontraron productos de uniforme/equipo</div>';
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

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
            suggestionsDiv.classList.add('hidden');
        }
    });

    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !suggestionsDiv.classList.contains('hidden')) {
            const firstSuggestion = suggestionsDiv.querySelector('div[data-id]');
            if (firstSuggestion) {
                e.preventDefault();
                firstSuggestion.click();
            }
        }
    });

    // ========== BÚSQUEDA Y CREACIÓN DE POLICÍAS ==========
    const policiaSearchInput = document.getElementById('policia_buscar');
    const policiaIdInput = document.getElementById('policia_id');
    const policiaSuggestionsDiv = document.getElementById('policia_suggestions');
    const policiaSelectedDiv = document.getElementById('policia_selected');
    const policiaClearBtn = document.getElementById('policia_clear');
    const policiaCreateForm = document.getElementById('policia_create_form');
    const policiaCreateBtn = document.getElementById('policia_create_btn');
    const policiaCancelCreateBtn = document.getElementById('policia_cancel_create');
    const verTodosPoliciasBtn = document.getElementById('ver_todos_policias_btn');
    let policiaSearchTimeout;

    function loadPolicias(query = '', showAll = false) {
        clearTimeout(policiaSearchTimeout);
        
        const url = showAll 
            ? `{{ route('api.policias.search') }}?all=true`
            : `{{ route('api.policias.search') }}?q=${encodeURIComponent(query)}`;
        
        fetch(url)
            .then(response => response.json())
            .then(policias => {
                if (policias.length === 0 && query.length > 0) {
                    policiaSuggestionsDiv.innerHTML = `
                        <div class="p-3 text-sm text-gray-500">
                            No se encontró el policía. El nombre que escribiste será usado para crearlo.
                        </div>
                        <div class="p-3 border-t border-gray-200">
                            <button type="button" id="show_create_policia" class="w-full text-left p-2 bg-green-50 hover:bg-green-100 text-green-800 rounded">
                                ✨ Crear nuevo policía: "<strong>${query}</strong>"
                            </button>
                        </div>
                    `;
                    policiaSuggestionsDiv.classList.remove('hidden');
                    
                    document.getElementById('show_create_policia').addEventListener('click', function() {
                        showCreatePoliciaForm(query);
                    });
                    return;
                }

                if (policias.length === 0) {
                    policiaSuggestionsDiv.innerHTML = '<div class="p-3 text-sm text-gray-500">No se encontraron policías</div>';
                    policiaSuggestionsDiv.classList.remove('hidden');
                    return;
                }

                policiaSuggestionsDiv.innerHTML = policias.map(policia => `
                    <div class="p-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0" 
                         data-id="${policia.id}" 
                         data-nombre="${policia.nombre_completo}"
                         data-placa="${policia.numero_placa}"
                         data-empleado="${policia.numero_empleado || ''}"
                         data-rango="${policia.rango || ''}"
                         data-area="${policia.area || ''}">
                        <div class="font-medium text-gray-900">${policia.nombre_completo}</div>
                        <div class="text-xs text-gray-500">Placa: ${policia.numero_placa}${policia.numero_empleado ? ' | Núm. Empleado: ' + policia.numero_empleado : ''}${policia.rango ? ' | Rango: ' + policia.rango : ''}${policia.area ? ' | Área: ' + policia.area : ''}</div>
                    </div>
                `).join('');

                policiaSuggestionsDiv.classList.remove('hidden');

                policiaSuggestionsDiv.querySelectorAll('div[data-id]').forEach(item => {
                    item.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const nombre = this.getAttribute('data-nombre');
                        const placa = this.getAttribute('data-placa');
                        selectPolicia(id, nombre, placa);
                    });
                });
            })
            .catch(error => {
                console.error('Error:', error);
                policiaSuggestionsDiv.innerHTML = '<div class="p-3 text-sm text-red-500">Error al buscar policías</div>';
                policiaSuggestionsDiv.classList.remove('hidden');
            });
    }

    function showCreatePoliciaForm(nombreCompleto = '') {
        policiaSearchInput.value = nombreCompleto;
        document.getElementById('policia_numero_placa').value = '';
        document.getElementById('policia_numero_empleado').value = '';
        document.getElementById('policia_rango').value = '';
        document.getElementById('policia_area').value = '';
        policiaCreateForm.classList.remove('hidden');
        policiaSuggestionsDiv.classList.add('hidden');
    }

    policiaSearchInput.addEventListener('focus', function() {
        if (this.value.trim().length === 0 && policiaIdInput.value === '') {
            loadPolicias('', true);
        }
    });

    policiaSearchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        if (query.length === 0) {
            policiaSuggestionsDiv.classList.add('hidden');
            policiaCreateForm.classList.add('hidden');
            return;
        }

        if (query.length < 2) {
            policiaSuggestionsDiv.classList.add('hidden');
            return;
        }

        policiaSearchTimeout = setTimeout(() => {
            loadPolicias(query, false);
        }, 300);
    });

    verTodosPoliciasBtn.addEventListener('click', function() {
        policiaSearchInput.value = '';
        policiaIdInput.value = '';
        loadPolicias('', true);
        policiaSearchInput.focus();
    });

    function selectPolicia(id, nombre, placa) {
        policiaIdInput.value = id;
        policiaSearchInput.value = nombre;
        policiaSuggestionsDiv.classList.add('hidden');
        policiaCreateForm.classList.add('hidden');
        showPoliciaSelected(nombre, placa, id);
    }

    function showPoliciaSelected(nombre, placa, id) {
        policiaSelectedDiv.querySelector('span').textContent = nombre;
        policiaSelectedDiv.querySelector('div.text-xs').textContent = `Placa: ${placa} | ID: ${id}`;
        policiaSelectedDiv.classList.remove('hidden');
        policiaSearchInput.classList.add('hidden');
    }

    policiaClearBtn.addEventListener('click', function() {
        policiaIdInput.value = '';
        policiaSearchInput.value = '';
        policiaSelectedDiv.classList.add('hidden');
        policiaCreateForm.classList.add('hidden');
        policiaSearchInput.classList.remove('hidden');
        policiaSearchInput.focus();
    });

    policiaCancelCreateBtn.addEventListener('click', function() {
        policiaCreateForm.classList.add('hidden');
        policiaSearchInput.focus();
    });

    policiaCreateBtn.addEventListener('click', function() {
        const nombreCompleto = policiaSearchInput.value.trim();
        const numeroPlaca = document.getElementById('policia_numero_placa').value.trim();
        const numeroEmpleado = document.getElementById('policia_numero_empleado').value.trim();
        const rango = document.getElementById('policia_rango').value.trim();
        const area = document.getElementById('policia_area').value.trim();

        if (!nombreCompleto || !numeroPlaca || !numeroEmpleado) {
            alert('El nombre completo, número de placa y número de empleado son obligatorios');
            return;
        }

        fetch('{{ route("api.policias.quick-create") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                nombre_completo: nombreCompleto,
                numero_placa: numeroPlaca,
                numero_empleado: numeroEmpleado,
                rango: rango,
                area: area
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw err;
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                selectPolicia(data.policia.id, data.policia.nombre_completo, data.policia.numero_placa);
                policiaCreateForm.classList.add('hidden');
            } else {
                alert('Error al crear el policía: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (error.errors) {
                let errorMsg = 'Error al crear el policía:\n';
                for (let field in error.errors) {
                    errorMsg += error.errors[field].join('\n') + '\n';
                }
                alert(errorMsg);
            } else {
                alert('Error al crear el policía. Verifica que el número de placa o número de empleado no estén duplicados.');
            }
        });
    });

    document.addEventListener('click', function(e) {
        if (!policiaSearchInput.contains(e.target) && !policiaSuggestionsDiv.contains(e.target) && !policiaCreateForm.contains(e.target)) {
            policiaSuggestionsDiv.classList.add('hidden');
        }
    });

    policiaSearchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !policiaSuggestionsDiv.classList.contains('hidden')) {
            const firstSuggestion = policiaSuggestionsDiv.querySelector('div[data-id]');
            if (firstSuggestion) {
                e.preventDefault();
                firstSuggestion.click();
            }
        }
    });
});
</script>
@endsection

