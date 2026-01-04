@extends('layouts.app')

@section('title', 'Nuevo Oficio de Entrada')

@section('content')
<div class="px-4 sm:px-0">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Registrar Nuevo Oficio de Entrada</h1>

    <div class="bg-white shadow sm:rounded-lg">
        <form method="POST" action="{{ route('oficios-entrada.store') }}" id="oficioForm" class="p-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 mb-6">
                <div>
                    <label for="numero_oficio" class="block text-sm font-medium text-gray-700">Número de Oficio (Opcional)</label>
                    <p class="text-xs text-gray-500 mb-1">Si no se proporciona, se generará automáticamente</p>
                    <input type="number" name="numero_oficio" id="numero_oficio" value="{{ old('numero_oficio') }}" min="1"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('numero_oficio')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="fecha_oficio" class="block text-sm font-medium text-gray-700">Fecha del Oficio *</label>
                    <input type="date" name="fecha_oficio" id="fecha_oficio" value="{{ old('fecha_oficio', date('Y-m-d')) }}" required
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('fecha_oficio')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="fecha_recepcion" class="block text-sm font-medium text-gray-700">Fecha de Recepción *</label>
                    <input type="date" name="fecha_recepcion" id="fecha_recepcion" value="{{ old('fecha_recepcion', date('Y-m-d')) }}" required
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('fecha_recepcion')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="proveedor_nombre" class="block text-sm font-medium text-gray-700">Nombre del Proveedor *</label>
                    <input type="text" name="proveedor_nombre" id="proveedor_nombre" value="{{ old('proveedor_nombre') }}" required
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('proveedor_nombre')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="numero_factura" class="block text-sm font-medium text-gray-700">Número de Factura *</label>
                    <input type="text" name="numero_factura" id="numero_factura" value="{{ old('numero_factura') }}" required
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('numero_factura')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="descripcion_material" class="block text-sm font-medium text-gray-700">Descripción General del Material (Opcional)</label>
                    <p class="text-xs text-gray-500 mb-2">Ejemplo: (122 PIEZA) CAJAS DE PLASTICO DE 60X40X28 TIPO ZAMORA CON TAPA</p>
                    <textarea name="descripcion_material" id="descripcion_material" rows="2"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('descripcion_material') }}</textarea>
                    @error('descripcion_material')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Sección para agregar productos -->
            <div class="border-t pt-6 mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Productos del Oficio *</h3>
                <p class="text-sm text-gray-600 mb-4">Agrega los productos que se recibieron en este oficio. Se crearán automáticamente las entradas al inventario.</p>
                
                <div class="mb-4">
                    <label for="producto_buscar" class="block text-sm font-medium text-gray-700 mb-2">Buscar Producto</label>
                    <div class="flex gap-2">
                        <div class="flex-1 relative">
                            <input type="text" 
                                   id="producto_buscar" 
                                   placeholder="Escribe el nombre del producto..."
                                   autocomplete="off"
                                   class="focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <div id="producto_suggestions" class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-96 overflow-y-auto"></div>
                        </div>
                        <button type="button" 
                                id="ver_todos_btn" 
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md border border-gray-300 whitespace-nowrap">
                            Ver Todos
                        </button>
                    </div>
                </div>

                <!-- Tabla de productos agregados -->
                <div id="productos_lista" class="mb-4">
                    <table class="min-w-full divide-y divide-gray-200" id="productos_table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unit.</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="productos_tbody" class="bg-white divide-y divide-gray-200">
                            <tr id="productos_vacio">
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">
                                    No hay productos agregados. Busca y agrega productos arriba.
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right text-sm font-medium text-gray-700">Importe Total:</td>
                                <td class="px-4 py-3 text-sm font-bold text-gray-900" id="importe_total_display">$0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <input type="hidden" name="importe_total" id="importe_total" value="0" required>
                <input type="hidden" name="productos_data" id="productos_data" value="[]">
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('oficios-entrada.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Cancelar
                </a>
                <button type="submit" id="submit_btn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Guardar Oficio de Entrada
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let productos = [];
    const searchInput = document.getElementById('producto_buscar');
    const suggestionsDiv = document.getElementById('producto_suggestions');
    const productosTbody = document.getElementById('productos_tbody');
    const productosVacio = document.getElementById('productos_vacio');
    const productosDataInput = document.getElementById('productos_data');
    const importeTotalInput = document.getElementById('importe_total');
    const importeTotalDisplay = document.getElementById('importe_total_display');
    const form = document.getElementById('oficioForm');

    // Buscar productos
    function loadProductos(query = '', showAll = false) {
        const url = showAll 
            ? `{{ route('api.productos.search') }}?all=true`
            : `{{ route('api.productos.search') }}?q=${encodeURIComponent(query)}`;
        
        fetch(url)
            .then(response => response.json())
            .then(productosList => {
                if (productosList.length === 0 && query.trim().length > 0 && !showAll) {
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
                                Se creará automáticamente con código único y se seleccionará para este oficio
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

                if (productosList.length === 0) {
                    suggestionsDiv.innerHTML = '<div class="p-3 text-sm text-gray-500">No se encontraron productos</div>';
                    suggestionsDiv.classList.remove('hidden');
                    return;
                }

                suggestionsDiv.innerHTML = productosList.map(producto => `
                    <div class="p-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0" 
                         data-id="${producto.id}" 
                         data-nombre="${producto.nombre}"
                         data-precio="${producto.precio_unitario || 0}">
                        <div class="font-medium text-gray-900">${producto.nombre}</div>
                        <div class="text-xs text-gray-500">Código: ${producto.codigo} | Precio: $${parseFloat(producto.precio_unitario || 0).toFixed(2)}</div>
                    </div>
                `).join('');

                suggestionsDiv.classList.remove('hidden');

                suggestionsDiv.querySelectorAll('div[data-id]').forEach(item => {
                    item.addEventListener('click', function() {
                        const id = parseInt(this.getAttribute('data-id'));
                        const nombre = this.getAttribute('data-nombre');
                        const precio = parseFloat(this.getAttribute('data-precio') || 0);
                        agregarProducto(id, nombre, precio);
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
                // Agregar el producto recién creado
                agregarProducto(
                    parseInt(result.producto.id),
                    result.producto.nombre,
                    parseFloat(result.producto.precio_unitario || 0)
                );
                suggestionsDiv.classList.add('hidden');
                searchInput.value = '';
                
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

    function agregarProducto(id, nombre, precio) {
        // Convertir ID a número
        const productoId = parseInt(id);
        
        // Verificar si ya existe
        if (productos.find(p => parseInt(p.id) === productoId)) {
            alert('Este producto ya está agregado. Puedes editar la cantidad en la tabla.');
            return;
        }

        const producto = {
            id: productoId,
            nombre: nombre,
            cantidad: 1,
            precio_unitario: parseFloat(precio) || 0,
            total: parseFloat(precio) || 0
        };

        productos.push(producto);
        actualizarTabla();
        searchInput.value = '';
        suggestionsDiv.classList.add('hidden');
    }

    function eliminarProducto(index) {
        productos.splice(index, 1);
        actualizarTabla();
    }

    function actualizarCantidad(index, cantidad) {
        if (cantidad <= 0) {
            eliminarProducto(index);
            return;
        }
        productos[index].cantidad = parseInt(cantidad);
        productos[index].total = productos[index].cantidad * productos[index].precio_unitario;
        actualizarTabla();
    }

    function actualizarPrecio(index, precio) {
        productos[index].precio_unitario = parseFloat(precio) || 0;
        productos[index].total = productos[index].cantidad * productos[index].precio_unitario;
        actualizarTabla();
    }

    function actualizarTabla() {
        if (productos.length === 0) {
            productosTbody.innerHTML = '<tr id="productos_vacio"><td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No hay productos agregados. Busca y agrega productos arriba.</td></tr>';
            importeTotalInput.value = 0;
            importeTotalDisplay.textContent = '$0.00';
        } else {
            productosTbody.innerHTML = productos.map((producto, index) => `
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">${producto.nombre}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <input type="number" 
                               min="1" 
                               value="${producto.cantidad}" 
                               onchange="window.actualizarCantidad(${index}, this.value)"
                               class="w-20 px-2 py-1 border border-gray-300 rounded-md text-sm">
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <input type="number" 
                               step="0.01" 
                               min="0" 
                               value="${producto.precio_unitario.toFixed(2)}" 
                               onchange="window.actualizarPrecio(${index}, this.value)"
                               class="w-24 px-2 py-1 border border-gray-300 rounded-md text-sm">
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">$${producto.total.toFixed(2)}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <button type="button" 
                                onclick="window.eliminarProducto(${index})"
                                class="text-red-600 hover:text-red-900 text-sm font-medium">
                            Eliminar
                        </button>
                    </td>
                </tr>
            `).join('');

            const total = productos.reduce((sum, p) => sum + p.total, 0);
            importeTotalInput.value = total.toFixed(2);
            importeTotalDisplay.textContent = '$' + total.toFixed(2);
        }

        productosDataInput.value = JSON.stringify(productos);
    }

    // Exponer funciones globalmente para los eventos inline
    window.actualizarCantidad = actualizarCantidad;
    window.actualizarPrecio = actualizarPrecio;
    window.eliminarProducto = eliminarProducto;

    // Event listeners
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        if (query.length >= 2) {
            loadProductos(query, false);
        } else {
            suggestionsDiv.classList.add('hidden');
        }
    });

    document.getElementById('ver_todos_btn').addEventListener('click', function() {
        loadProductos('', true);
    });

    // Validar antes de enviar
    form.addEventListener('submit', function(e) {
        if (productos.length === 0) {
            e.preventDefault();
            alert('⚠️ Debes agregar al menos un producto al oficio.');
            searchInput.focus();
            return false;
        }
        
        // Asegurar que productos_data esté actualizado
        productosDataInput.value = JSON.stringify(productos);
        
        // Verificar que el importe total coincida
        const totalCalculado = productos.reduce((sum, p) => sum + p.total, 0);
        importeTotalInput.value = totalCalculado.toFixed(2);
        
        // Debug (puedes quitar esto después)
        console.log('Productos a enviar:', productos);
        console.log('JSON a enviar:', productosDataInput.value);
        console.log('Importe total:', importeTotalInput.value);
    });

    // Cerrar sugerencias al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
            suggestionsDiv.classList.add('hidden');
        }
    });
});
</script>
@endsection
