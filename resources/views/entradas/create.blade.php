@extends('layouts.app')

@section('title', 'Nueva Entrada')

@section('content')
<div class="px-4 sm:px-0">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Registrar Nueva Entrada</h1>

    <div class="bg-white shadow sm:rounded-lg">
        <form method="POST" action="{{ route('entradas.store') }}" enctype="multipart/form-data" class="p-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="producto_buscar" class="block text-sm font-medium text-gray-700">Buscar o Seleccionar Producto *</label>
                    <p class="text-xs text-gray-500 mb-2">💡 <strong>Tip:</strong> Escribe el nombre del producto. Si no existe, aparecerá un botón para crearlo automáticamente.</p>
                    <div class="relative">
                        <div class="flex gap-2">
                            <div class="flex-1 relative">
                                <input type="text" 
                                       id="producto_buscar" 
                                       name="producto_buscar"
                                       value="{{ old('producto_buscar') }}"
                                       placeholder="Escribe el nombre del producto para buscar o crear uno nuevo..."
                                       autocomplete="off"
                                       class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <input type="hidden" name="producto_id" id="producto_id" value="{{ old('producto_id') }}" required>
                                <div id="producto_suggestions" class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-96 overflow-y-auto"></div>
                            </div>
                            <button type="button" 
                                    id="ver_todos_btn" 
                                    class="mt-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md border border-gray-300 whitespace-nowrap">
                                Ver Todos
                            </button>
                        </div>
                    </div>
                    <div id="producto_selected" class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded-md hidden">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-900"></span>
                            <button type="button" id="producto_clear" class="text-blue-600 hover:text-blue-800 text-sm">Cambiar</button>
                        </div>
                        <div class="text-xs text-gray-600 mt-1"></div>
                    </div>
                    @error('producto_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="cantidad" class="block text-sm font-medium text-gray-700">Cantidad *</label>
                    <input type="number" name="cantidad" id="cantidad" value="{{ old('cantidad') }}" required min="1"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('cantidad')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="fecha" class="block text-sm font-medium text-gray-700">Fecha *</label>
                    <input type="date" name="fecha" id="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('fecha')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="proveedor" class="block text-sm font-medium text-gray-700">Proveedor</label>
                    <input type="text" name="proveedor" id="proveedor" value="{{ old('proveedor') }}"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div>
                    <label for="numero_factura" class="block text-sm font-medium text-gray-700">Número de Factura</label>
                    <input type="text" name="numero_factura" id="numero_factura" value="{{ old('numero_factura') }}"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div>
                    <label for="precio_unitario" class="block text-sm font-medium text-gray-700">Precio Unitario *</label>
                    <input type="number" step="0.01" name="precio_unitario" id="precio_unitario" value="{{ old('precio_unitario', 0) }}" required
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('precio_unitario')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="observaciones" class="block text-sm font-medium text-gray-700">Observaciones</label>
                    <textarea name="observaciones" id="observaciones" rows="3"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('observaciones') }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label for="imagen" class="block text-sm font-medium text-gray-700">Imagen del Producto (Opcional)</label>
                    <input type="file" name="imagen" id="imagen" accept="image/*"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    <p class="mt-1 text-sm text-gray-500">Puedes subir una foto del producto que llegó. Formatos: JPG, PNG, GIF (máx. 5MB)</p>
                    @error('imagen')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div id="imagen_preview" class="mt-2 hidden">
                        <img id="imagen_preview_img" src="" alt="Vista previa" class="max-w-xs h-auto rounded-md border border-gray-300">
                    </div>
                </div>

                <div class="sm:col-span-2 border-t pt-4 mt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Información de Entrega y Recepción</h3>
                </div>

                <div class="sm:col-span-1">
                    <label for="entrega_nombre" class="block text-sm font-medium text-gray-700">Nombre del que Entrega (Proveedor)</label>
                    <input type="text" name="entrega_nombre" id="entrega_nombre" value="{{ old('entrega_nombre') }}"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div class="sm:col-span-1">
                    <label for="recibe_nombre" class="block text-sm font-medium text-gray-700">Nombre del que Recibe (Almacén)</label>
                    <input type="text" name="recibe_nombre" id="recibe_nombre" value="{{ old('recibe_nombre') }}"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div class="sm:col-span-1">
                    <label for="entrega_firma" class="block text-sm font-medium text-gray-700">Firma del que Entrega</label>
                    <textarea name="entrega_firma" id="entrega_firma" rows="3" placeholder="Firma o huella digital..."
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('entrega_firma') }}</textarea>
                </div>

                <div class="sm:col-span-1">
                    <label for="recibe_firma" class="block text-sm font-medium text-gray-700">Firma del que Recibe</label>
                    <textarea name="recibe_firma" id="recibe_firma" rows="3" placeholder="Firma o huella digital..."
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('recibe_firma') }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end space-x-3">
                <a href="{{ route('entradas.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Cancelar
                </a>
                <button type="submit" id="submit_entrada" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Registrar Entrada
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validar antes de enviar el formulario
    const form = document.querySelector('form[action="{{ route("entradas.store") }}"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            const productoId = document.getElementById('producto_id').value;
            if (!productoId || productoId === '' || productoId === null) {
                e.preventDefault();
                const searchInput = document.getElementById('producto_buscar');
                const nombreProducto = searchInput ? searchInput.value.trim() : '';
                
                if (nombreProducto.length > 0) {
                    // Si hay texto en el campo, ofrecer crear el producto
                    if (confirm(`⚠️ No has seleccionado un producto.\n\n¿Deseas crear "${nombreProducto}" como nuevo producto?`)) {
                        crearProductoRapido(nombreProducto);
                        // Esperar a que se cree y seleccione
                        setTimeout(() => {
                            // Volver a intentar enviar el formulario después de un momento
                            alert('✅ Producto creado. Por favor, completa los demás campos y vuelve a hacer clic en "Registrar Entrada".');
                        }, 1000);
                    } else {
                        alert('⚠️ Por favor, escribe el nombre de un producto en el campo de búsqueda y haz clic en "CREAR PRODUCTO" cuando aparezca la opción.');
                    }
                } else {
                    alert('⚠️ Por favor, escribe el nombre de un producto en el campo "Buscar o Seleccionar Producto" para buscarlo o crearlo.');
                }
                
                if (searchInput) {
                    searchInput.focus();
                    // Forzar búsqueda para mostrar la opción de crear
                    if (nombreProducto.length >= 2) {
                        loadProductos(nombreProducto, false);
                    }
                }
                return false;
            }
            return true;
        });
    }
    const searchInput = document.getElementById('producto_buscar');
    const productoIdInput = document.getElementById('producto_id');
    const suggestionsDiv = document.getElementById('producto_suggestions');
    const selectedDiv = document.getElementById('producto_selected');
    const clearBtn = document.getElementById('producto_clear');
    let searchTimeout;

    // Si hay un producto_id previamente seleccionado (por old), cargar su nombre
    @if(old('producto_id'))
        const oldProductoId = {{ old('producto_id') }};
        @foreach($productos as $producto)
            if ({{ $producto->id }} === oldProductoId) {
                showSelected('{{ $producto->nombre }}', {{ $producto->stock_actual }}, oldProductoId);
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
                if (productos.length === 0 && query.trim().length > 0 && !showAll) {
                    // Si no hay resultados y hay búsqueda, ofrecer crear nuevo producto
                    const nombreProducto = query.trim();
                    suggestionsDiv.innerHTML = `
                        <div class="p-4 border-2 border-green-500 bg-green-50 rounded-md shadow-md">
                            <div class="text-sm font-bold text-gray-900 mb-2">🔍 No se encontró el producto: "${nombreProducto}"</div>
                            <div class="text-xs text-gray-700 mb-3">Puedes crearlo automáticamente haciendo clic en el botón verde abajo:</div>
                            <button type="button" 
                                    id="crear_producto_nuevo_btn" 
                                    data-nombre="${nombreProducto}"
                                    class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-md shadow-md transform hover:scale-105 transition-all">
                                ➕ CREAR PRODUCTO: "${nombreProducto}"
                            </button>
                            <div class="text-xs text-gray-500 mt-2 text-center">
                                Se creará automáticamente con código único y se seleccionará para esta entrada
                            </div>
                        </div>
                    `;
                    suggestionsDiv.classList.remove('hidden');
                    
                    // Event listener para crear producto
                    setTimeout(() => {
                        const btn = document.getElementById('crear_producto_nuevo_btn');
                        if (btn) {
                            btn.addEventListener('click', function() {
                                crearProductoRapido(nombreProducto);
                            });
                        }
                    }, 100);
                    return;
                }

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
                        <div class="text-xs text-gray-500">Código: ${producto.codigo} | Stock: ${producto.stock_actual}</div>
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

    function crearProductoRapido(nombre) {
        // Generar código automático basado en el nombre
        const codigo = 'PROD-' + String(Date.now()).slice(-6);
        
        const data = {
            codigo: codigo,
            nombre: nombre,
            unidad_medida: 'Pieza(s)',
            stock_actual: 0,
            precio_unitario: 0
        };

        suggestionsDiv.innerHTML = '<div class="p-3 text-sm text-gray-500">Creando producto...</div>';

        fetch('{{ route("api.productos.quick-create") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                               document.querySelector('input[name="_token"]')?.value
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(result => {
            if (result.success && result.producto) {
                // Establecer el producto_id directamente
                productoIdInput.value = result.producto.id;
                searchInput.value = result.producto.nombre;
                
                // Seleccionar el producto recién creado
                selectProducto(
                    result.producto.id,
                    result.producto.nombre,
                    result.producto.stock_actual || 0
                );
                suggestionsDiv.classList.add('hidden');
                
                // Verificar que se estableció correctamente
                console.log('Producto ID establecido:', productoIdInput.value);
                
                // Mostrar mensaje de éxito
                alert('✅ Producto "' + nombre + '" creado exitosamente con código: ' + codigo);
            } else {
                alert('Error al crear el producto: ' + (result.message || 'Error desconocido'));
                suggestionsDiv.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (error.errors && error.errors.codigo) {
                alert('Error: El código ya existe. Por favor, intenta de nuevo.');
            } else {
                alert('Error al crear el producto. Por favor, intenta de nuevo.');
            }
            suggestionsDiv.classList.add('hidden');
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
        if (!id || id === '') {
            console.error('Error: ID de producto inválido');
            return;
        }
        productoIdInput.value = id;
        productoIdInput.setAttribute('value', id); // Asegurar que el atributo también se actualice
        searchInput.value = nombre;
        suggestionsDiv.classList.add('hidden');
        showSelected(nombre, stock, id);
        
        // Verificar que se estableció correctamente
        console.log('Producto seleccionado - ID:', productoIdInput.value, 'Nombre:', nombre);
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

    // Vista previa de imagen
    const imagenInput = document.getElementById('imagen');
    const imagenPreview = document.getElementById('imagen_preview');
    const imagenPreviewImg = document.getElementById('imagen_preview_img');

    if (imagenInput) {
        imagenInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validar tamaño (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('La imagen es demasiado grande. Máximo 5MB.');
                    imagenInput.value = '';
                    imagenPreview.classList.add('hidden');
                    return;
                }

                // Validar tipo
                if (!file.type.startsWith('image/')) {
                    alert('Por favor selecciona una imagen válida.');
                    imagenInput.value = '';
                    imagenPreview.classList.add('hidden');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    imagenPreviewImg.src = e.target.result;
                    imagenPreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                imagenPreview.classList.add('hidden');
            }
        });
    }

});
</script>
@endsection


