@extends('layouts.app')

@section('title', 'Nueva Entrega a Policía')

@section('content')
<div class="px-4 sm:px-0">
    <div class="rounded-lg shadow-xl p-6 mb-6 border-2" style="background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #312e81 100%) !important; border-color: #1e293b !important;">
        <h1 class="text-3xl font-bold mb-2" style="color: #ffffff !important; text-shadow: 2px 2px 6px rgba(0,0,0,0.5); font-weight: 900;">👮 Registrar Entrega de Uniforme/Equipo a Policía</h1>
        <p class="text-sm font-semibold" style="color: #f1f5f9 !important; text-shadow: 1px 1px 3px rgba(0,0,0,0.4);">Sistema de control de entregas de uniformes y equipo policial</p>
    </div>

    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg shadow-sm">
        <div class="flex items-start">
            <span class="text-2xl mr-3">⚠️</span>
            <div>
                <p class="text-sm font-semibold text-blue-900 mb-1">Importante</p>
                <p class="text-sm text-blue-800">
                    Cada producto de uniforme/equipo solo puede entregarse <strong>una vez por policía</strong>. 
                    Puedes entregar diferentes productos al mismo policía (ej: BOTA TIPO TACTICO y CHAMARRA ROMPEVIENTOS), 
                    pero no puedes entregar el mismo producto dos veces. El sistema validará automáticamente esto.
                </p>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg shadow-sm">
            <div class="flex items-center mb-2">
                <span class="text-xl mr-2">❌</span>
                <strong class="text-sm font-semibold">Error:</strong>
            </div>
            <div class="text-sm ml-6 whitespace-pre-line">
                @foreach($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        </div>
    @endif

    <div class="bg-white shadow-xl sm:rounded-lg border border-gray-200">
        <form method="POST" action="{{ route('salidas-policia.store') }}" id="formEntrega" class="p-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- SECCIÓN POLICÍA -->
                <div class="sm:col-span-2">
                    <div class="bg-gradient-to-r from-indigo-50 via-blue-50 to-purple-50 p-6 rounded-xl border-2 border-indigo-200 shadow-lg mb-4">
                        <label class="block text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <span class="text-2xl mr-3">👮</span>
                            Seleccionar Policía <span class="text-red-500 ml-1">*</span>
                        </label>
                        
                        <!-- Campo oculto para compatibilidad -->
                        <div class="hidden">
                            <input type="text" id="policia_buscar_nombre" name="policia_buscar_nombre" value="{{ old('policia_buscar_nombre') }}" autocomplete="off">
                        </div>
                        
                        <!-- Búsqueda mejorada -->
                        <div class="mb-4">
                            <label for="policia_buscar_empleado" class="block text-sm font-semibold text-gray-700 mb-2">
                                🔍 Buscar por Número de Empleado o Nombre Completo
                            </label>
                            <div class="relative">
                                <div class="flex gap-2">
                                    <div class="relative flex-1">
                                        <input type="text" 
                                               id="policia_buscar_empleado" 
                                               name="policia_buscar_empleado"
                                               value="{{ old('policia_buscar_empleado') }}"
                                               placeholder="Escribe el número de empleado (ej: 12345) o nombre completo..."
                                               autocomplete="off"
                                               class="w-full px-4 py-3 pr-12 text-base border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-all"
                                               aria-label="Campo de búsqueda de policía">
                                        <div id="policia_loading" class="hidden absolute right-4 top-1/2 transform -translate-y-1/2">
                                            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                                        </div>
                                        <div id="policia_search_status" class="hidden absolute right-4 top-1/2 transform -translate-y-1/2">
                                            <span class="text-green-600 text-xl">✓</span>
                                        </div>
                                    </div>
                                    <button type="button" 
                                            id="verificar_empleado_btn" 
                                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-lg shadow-md hover:shadow-lg transition-all transform hover:scale-105 whitespace-nowrap disabled:opacity-50 disabled:cursor-not-allowed">
                                        🔍 Buscar
                                    </button>
                                </div>
                                <div class="mt-2 flex items-center gap-2">
                                    <p class="text-xs text-gray-500 flex items-center">
                                        <span class="mr-1">💡</span>
                                        <span>Escribe y espera o presiona "Buscar". La búsqueda es automática mientras escribes.</span>
                                    </p>
                                </div>
                            </div>
                            <div id="verificacion_empleado" class="mt-3 hidden"></div>
                            <div id="policia_search_message" class="mt-2 text-sm hidden"></div>
                        </div>
                        
                        <!-- Input hidden para policía ID -->
                        <input type="hidden" name="policia_id" id="policia_id" value="{{ old('policia_id') }}" required>
                        
                        <!-- Sugerencias mejoradas -->
                        <div id="policia_suggestions" class="mt-2 hidden bg-white border-2 border-gray-200 rounded-lg shadow-xl max-h-96 overflow-y-auto"></div>
                        
                        <!-- Tarjeta de policía seleccionado -->
                        <div id="policia_selected" class="mt-4 hidden">
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-400 rounded-xl p-4 shadow-lg">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-4 flex-1">
                                        <div class="flex-shrink-0">
                                            <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                                                👮
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="text-lg font-bold text-gray-900 mb-1" id="policia_selected_nombre"></h3>
                                            <div class="text-sm text-gray-600 space-y-1" id="policia_selected_info"></div>
                                        </div>
                                    </div>
                                    <button type="button" 
                                            id="policia_clear" 
                                            class="ml-4 px-4 py-2 bg-white hover:bg-gray-100 text-gray-700 font-semibold rounded-lg shadow hover:shadow-md transition-all border border-gray-300">
                                        ✏️ Cambiar
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Formulario para crear nuevo policía (Modal mejorado) -->
                        <div id="policia_create_form" class="mt-4 hidden relative">
                            <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border-2 border-yellow-400 rounded-xl p-6 shadow-xl relative overflow-visible">
                                <div class="flex items-center mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-full flex items-center justify-center text-white text-xl mr-3 shadow-lg">
                                        ✨
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-yellow-900">Crear Nuevo Policía</h3>
                                        <p class="text-sm text-yellow-700">El policía no existe en el sistema. Completa los datos:</p>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">
                                            👤 Nombre Completo <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                               id="policia_nombre_completo" 
                                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 text-base" 
                                               placeholder="Ej: Juan Pérez García" 
                                               required>
                                    </div>
                                    
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">
                                            🆔 Número de Empleado <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                               id="policia_numero_empleado" 
                                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 text-base" 
                                               placeholder="Ej: 12345" 
                                               required>
                                        <p class="mt-2 text-xs text-gray-600 bg-yellow-100 p-2 rounded border border-yellow-300">
                                            ⚠️ <strong>Importante:</strong> Este número se usará para verificar entregas duplicadas. Debe ser único.
                                        </p>
                                    </div>
                                    
                                    <div class="hidden">
                                        <input type="text" id="policia_numero_placa">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">
                                            ⭐ Rango
                                        </label>
                                        <input type="text" 
                                               id="policia_rango" 
                                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 text-base" 
                                               placeholder="Opcional">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">
                                            🏢 Área
                                        </label>
                                        <input type="text" 
                                               id="policia_area" 
                                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 text-base" 
                                               placeholder="Opcional">
                                    </div>
                                </div>
                                
                                <div class="mt-6 p-5 bg-gradient-to-r from-white to-gray-50 rounded-xl border-2 border-gray-200 shadow-xl">
                                    <div class="flex gap-4 justify-end items-center">
                                        <button type="button" 
                                                id="policia_cancel_create" 
                                                class="inline-flex items-center justify-center px-7 py-3.5 text-white font-bold text-base rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 border-2"
                                                style="background-color: #dc2626 !important; color: #ffffff !important; border-color: #991b1b !important; min-width: 140px;">
                                            <span class="mr-2 text-lg">❌</span>
                                            <span>Cancelar</span>
                                        </button>
                                        <button type="button" 
                                                id="policia_create_btn" 
                                                class="inline-flex items-center justify-center px-8 py-3.5 text-white font-bold text-base rounded-xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 border-2"
                                                style="background-color: #166534 !important; color: #ffffff !important; border-color: #14532d !important; min-width: 160px;">
                                            <span class="mr-2 text-lg">✅</span>
                                            <span>Crear Policía</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @error('policia_id')
                            <div class="mt-4 p-3 bg-red-50 border-2 border-red-300 rounded-lg">
                                <p class="text-sm text-red-600 font-semibold">{{ $message }}</p>
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- SECCIÓN PRODUCTO -->
                <div class="sm:col-span-2">
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-4 rounded-lg border border-purple-200">
                        <label for="producto_buscar" class="block text-sm font-bold text-gray-800 mb-3 flex items-center">
                            <span class="text-xl mr-2">👕</span>
                            Buscar o Seleccionar Producto (Solo Uniformes/Equipo) <span class="text-red-500 ml-1">*</span>
                        </label>
                    <div class="relative">
                        <div class="flex gap-2">
                            <div class="flex-1 relative">
                                <input type="text" 
                                       id="producto_buscar" 
                                       name="producto_buscar"
                                       value="{{ old('producto_buscar') }}"
                                           placeholder="Escribe para buscar uniformes/equipo (ej: BOTA TIPO TACTICO)..."
                                       autocomplete="off"
                                           class="mt-1 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 border-2">
                                <input type="hidden" name="producto_id" id="producto_id" value="{{ old('producto_id') }}" required>
                                    <div id="producto_suggestions" class="absolute z-40 mt-1 w-full bg-white border-2 border-gray-300 rounded-md shadow-xl hidden max-h-96 overflow-y-auto"></div>
                            </div>
                            <button type="button" 
                                    id="ver_todos_btn" 
                                        class="mt-1 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-bold rounded-md shadow-md whitespace-nowrap transition-all">
                                    📋 Ver Todos
                            </button>
                        </div>
                    </div>
                        <div id="producto_selected" class="mt-3 p-3 bg-green-50 border-2 border-green-300 rounded-md hidden shadow-sm">
                        <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <span class="text-green-600 text-xl mr-2">✓</span>
                                    <span class="text-sm font-semibold text-gray-900" id="producto_selected_nombre"></span>
                        </div>
                                <button type="button" id="producto_clear" class="text-green-700 hover:text-green-900 text-sm font-medium px-2 py-1 rounded hover:bg-green-100 transition-colors">Cambiar</button>
                            </div>
                            <div class="text-xs text-gray-600 mt-1 ml-7" id="producto_selected_info"></div>
                    </div>
                    @error('producto_id')
                            <p class="mt-2 text-sm text-red-600 font-semibold">{{ $message }}</p>
                    @enderror
                    </div>
                </div>

                <div>
                    <label for="cantidad" class="block text-sm font-semibold text-gray-700 mb-1">Cantidad <span class="text-red-500">*</span></label>
                    <input type="number" name="cantidad" id="cantidad" value="{{ old('cantidad', 1) }}" required min="1"
                        class="mt-1 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 border-2">
                    @error('cantidad')
                        <p class="mt-2 text-sm text-red-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="fecha" class="block text-sm font-semibold text-gray-700 mb-1">Fecha <span class="text-red-500">*</span></label>
                    <input type="date" name="fecha" id="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required
                        class="mt-1 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 border-2">
                    @error('fecha')
                        <p class="mt-2 text-sm text-red-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="motivo" class="block text-sm font-semibold text-gray-700 mb-1">Motivo</label>
                    <input type="text" name="motivo" id="motivo" value="{{ old('motivo', 'Entrega de uniforme/equipo') }}" placeholder="Ej: Entrega de uniforme/equipo"
                        class="mt-1 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 border-2">
                </div>

                <div>
                    <label for="destino" class="block text-sm font-semibold text-gray-700 mb-1">Destino</label>
                    <input type="text" name="destino" id="destino" value="{{ old('destino') }}"
                        class="mt-1 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 border-2">
                </div>

                <div class="sm:col-span-2">
                    <label for="observaciones" class="block text-sm font-semibold text-gray-700 mb-1">Observaciones</label>
                    <textarea name="observaciones" id="observaciones" rows="3"
                        class="mt-1 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 border-2">{{ old('observaciones') }}</textarea>
                </div>

                <div class="sm:col-span-2 border-t-2 border-gray-200 pt-4 mt-4">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <span class="text-xl mr-2">✍️</span>
                        Información de Entrega y Recepción
                    </h3>
                </div>

                <div class="sm:col-span-1">
                    <label for="entrega_nombre" class="block text-sm font-semibold text-gray-700 mb-1">Nombre del que Entrega (Almacén)</label>
                    <input type="text" name="entrega_nombre" id="entrega_nombre" value="{{ old('entrega_nombre') }}"
                        class="mt-1 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 border-2">
                </div>

                <div class="sm:col-span-1">
                    <label for="recibe_nombre" class="block text-sm font-semibold text-gray-700 mb-1">Nombre del que Recibe (Policía)</label>
                    <input type="text" name="recibe_nombre" id="recibe_nombre" value="{{ old('recibe_nombre') }}"
                        class="mt-1 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 border-2">
                </div>

            </div>

            <div class="mt-8 p-6 bg-gradient-to-r from-gray-50 to-white rounded-xl border-2 border-gray-200 shadow-xl">
                <div class="flex gap-4 justify-end items-center">
                    <a href="{{ route('salidas-policia.index') }}" 
                       class="inline-flex items-center justify-center px-8 py-4 text-white font-bold text-base rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 border-2 transform hover:scale-105"
                       style="background-color: #dc2626 !important; color: #ffffff !important; border-color: #991b1b !important; min-width: 160px;">
                        <span class="mr-2 text-xl">❌</span>
                        <span>CANCELAR</span>
                    </a>
                    <button type="submit" id="btnRegistrar" 
                            class="inline-flex items-center justify-center px-10 py-4 text-white font-extrabold text-lg rounded-xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 border-2"
                            style="background-color: #166534 !important; color: #ffffff !important; border-color: #14532d !important; min-width: 180px;">
                        <span class="mr-2 text-2xl">✅</span>
                        <span>ACEPTAR</span>
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ======== ELEMENTOS ========
    const searchInput = document.getElementById('producto_buscar');
    const productoIdInput = document.getElementById('producto_id');
    const productoSuggestions = document.getElementById('producto_suggestions');
    const productoSelectedDiv = document.getElementById('producto_selected');
    const productoSelectedNombre = document.getElementById('producto_selected_nombre');
    const productoSelectedInfo = document.getElementById('producto_selected_info');
    const productoClearBtn = document.getElementById('producto_clear');
    const verTodosBtn = document.getElementById('ver_todos_btn');

    const policiaNombreInput = document.getElementById('policia_buscar_nombre'); // Oculto pero necesario para compatibilidad
    const policiaEmpleadoInput = document.getElementById('policia_buscar_empleado');
    const policiaIdInput = document.getElementById('policia_id');
    const policiaSuggestions = document.getElementById('policia_suggestions');
    const policiaSelectedDiv = document.getElementById('policia_selected');
    const policiaSelectedNombre = document.getElementById('policia_selected_nombre');
    const policiaSelectedInfo = document.getElementById('policia_selected_info');
    const policiaClearBtn = document.getElementById('policia_clear');
    // Botones "Ver Todos" y "Con Entregas" eliminados - simplificado a solo búsqueda por número de empleado
    const policiaLoading = document.getElementById('policia_loading');
    const policiaCreateForm = document.getElementById('policia_create_form');
    const policiaCreateBtn = document.getElementById('policia_create_btn');
    const policiaCancelCreateBtn = document.getElementById('policia_cancel_create');
    
    // Función para manejar el atributo required de los campos del formulario de creación
    function toggleRequiredFields(show) {
        const nombreCompletoField = document.getElementById('policia_nombre_completo');
        const numeroEmpleadoField = document.getElementById('policia_numero_empleado');
        // Nota: numeroPlacaField no necesita required porque siempre está oculto y se llena automáticamente
        
        if (nombreCompletoField) {
            if (show) {
                nombreCompletoField.setAttribute('required', 'required');
            } else {
                nombreCompletoField.removeAttribute('required');
            }
        }
        
        if (numeroEmpleadoField) {
            if (show) {
                numeroEmpleadoField.setAttribute('required', 'required');
            } else {
                numeroEmpleadoField.removeAttribute('required');
            }
        }
    }
    
    // Inicializar: quitar required si el formulario está oculto
    if (policiaCreateForm && policiaCreateForm.classList.contains('hidden')) {
        toggleRequiredFields(false);
    }

    const formEntrega = document.getElementById('formEntrega');
    const btnRegistrar = document.getElementById('btnRegistrar');

    // CSRF token
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

    // ======== HELPERS ========
    function showProductoSelected(nombre, stock, id) {
        productoSelectedNombre.textContent = nombre;
        productoSelectedInfo.textContent = `ID: ${id} | Stock disponible: ${stock}`;
        productoSelectedDiv.classList.remove('hidden');
        searchInput.classList.add('hidden');
    }

    function clearProductoSelection() {
        productoIdInput.value = '';
        searchInput.value = '';
        productoSelectedDiv.classList.add('hidden');
        searchInput.classList.remove('hidden');
        searchInput.focus();
    }

    function showPoliciaSelected(nombre, placa, id, numeroEmpleado = '', rango = '', area = '') {
        policiaSelectedNombre.textContent = nombre;
        
        // Construir información mejorada
        let infoHtml = '';
        if (numeroEmpleado) {
            infoHtml += `<div class="flex items-center mb-1"><span class="font-semibold mr-2">🆔 Empleado:</span> <span class="text-blue-600 font-bold">${numeroEmpleado}</span></div>`;
        }
        if (rango) {
            infoHtml += `<div class="flex items-center mb-1"><span class="font-semibold mr-2">⭐ Rango:</span> ${rango}</div>`;
        }
        if (area) {
            infoHtml += `<div class="flex items-center"><span class="font-semibold mr-2">🏢 Área:</span> ${area}</div>`;
        }
        if (!infoHtml) {
            infoHtml = `<div class="text-gray-500">ID: ${id}</div>`;
        }
        
        policiaSelectedInfo.innerHTML = infoHtml;
        policiaSelectedDiv.classList.remove('hidden');
        policiaSuggestions.classList.add('hidden');
        policiaCreateForm.classList.add('hidden');
        toggleRequiredFields(false);
        
        // Guardar el número de empleado en un atributo data para poder usarlo después
        policiaSelectedDiv.setAttribute('data-empleado', numeroEmpleado);
        policiaSelectedDiv.setAttribute('data-policia-id', id);
        
        // Verificar automáticamente las entregas cuando se selecciona un policía
        if (numeroEmpleado && numeroEmpleado.length > 0) {
            verificarEntregasAutomatico(numeroEmpleado, nombre);
        }
    }
    
    function verificarEntregasAutomatico(numeroEmpleado, nombrePolicia) {
        // Ocultar verificación anterior si existe
        if (verificacionEmpleadoDiv) verificacionEmpleadoDiv.classList.add('hidden');
        
        // Eliminar mensajes anteriores antes de crear nuevos
        const mensajeProductos = document.getElementById('mensaje_productos_entregados');
        if (mensajeProductos) mensajeProductos.remove();
        const mensajeExiste = document.getElementById('mensaje_empleado_existe');
        if (mensajeExiste) mensajeExiste.remove();
        
        const url = `{{ route('api.policias.verificar-entregas') }}?numero_empleado=${encodeURIComponent(numeroEmpleado)}`;
        
        fetch(url)
            .then(r => r.json())
            .then(data => {
                if (data.success && data.registrado && data.entregas && data.entregas.length > 0) {
                    const entregas = data.entregas || [];
                    
                    // Mostrar información de productos entregados
                    const entregasHtml = entregas.map(e => `
                        <div class="text-sm text-gray-700 mt-2 pl-3 border-l-3 border-yellow-400 py-1">
                            • <strong>${e.producto}</strong> (Cantidad: ${e.cantidad}) - Fecha: ${e.fecha}
                        </div>
                    `).join('');
                    
                    // Crear nuevo mensaje con productos entregados
                    const mensajeEntregas = document.createElement('div');
                    mensajeEntregas.setAttribute('id', 'mensaje_productos_entregados');
                    mensajeEntregas.className = 'mt-4 p-4 bg-gradient-to-r from-yellow-50 to-amber-50 border-2 border-yellow-400 rounded-xl shadow-md';
                    mensajeEntregas.innerHTML = `
                        <div class="flex items-start">
                            <span class="text-yellow-600 text-2xl mr-3">📦</span>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-yellow-900 mb-2">Productos ya entregados a este policía:</p>
                                <div class="mt-2">
                                    ${entregasHtml}
                                </div>
                                <p class="text-xs text-yellow-700 mt-3 font-medium">💡 Puedes entregar otros productos que aún no se hayan entregado.</p>
                            </div>
                        </div>
                    `;
                    // Insertar después del div de policía seleccionado
                    policiaSelectedDiv.parentNode.insertBefore(mensajeEntregas, policiaSelectedDiv.nextSibling);
                }
            })
            .catch(err => {
                console.error('Error al verificar entregas automáticamente:', err);
            });
    }

    function clearPoliciaSelection() {
        policiaIdInput.value = '';
        policiaNombreInput.value = '';
        policiaEmpleadoInput.value = '';
        policiaSelectedDiv.classList.add('hidden');
        policiaCreateForm.classList.add('hidden');
        // Desactivar campos required cuando se oculta el formulario
        toggleRequiredFields(false);
        policiaNombreInput.style.display = 'block';
        policiaEmpleadoInput.style.display = 'block';
        ocultarMensajeBusqueda();
        // Eliminar mensajes de productos entregados
        const mensajeProductos = document.getElementById('mensaje_productos_entregados');
        if (mensajeProductos) mensajeProductos.remove();
        policiaEmpleadoInput.focus();
    }

    // ======== PRODUCTOS: BUSCAR / SUGERENCIAS ========
    let productoTimeout;
    function fetchProductos(query = '', showAll = false) {
        clearTimeout(productoTimeout);
        productoTimeout = setTimeout(() => {
        const url = showAll 
            ? `{{ route('api.productos.search') }}?all=true&uniforme=true`
            : `{{ route('api.productos.search') }}?q=${encodeURIComponent(query)}&uniforme=true`;
        
        fetch(url)
                .then(r => r.json())
                .then(data => renderProductoSuggestions(data))
                .catch(err => {
                    console.error(err);
                    productoSuggestions.innerHTML = `<div class="p-3 text-sm text-red-500">Error al buscar productos</div>`;
                    productoSuggestions.classList.remove('hidden');
                });
        }, 250);
    }

    function renderProductoSuggestions(items) {
        if (!items || items.length === 0) {
            productoSuggestions.innerHTML = `<div class="p-3 text-sm text-gray-500">No se encontraron productos de uniforme/equipo</div>`;
            productoSuggestions.classList.remove('hidden');
                    return;
                }

        productoSuggestions.innerHTML = items.map(p => `
            <div class="p-3 hover:bg-purple-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors producto-item" 
                 data-id="${p.id}" 
                 data-nombre="${p.nombre}" 
                 data-stock="${p.stock_actual}">
                <div class="font-semibold text-gray-900">${p.nombre}</div>
                <div class="text-xs text-gray-500">Código: ${p.codigo || '-'} | Stock disponible: ${p.stock_actual}</div>
                    </div>
                `).join('');
        productoSuggestions.classList.remove('hidden');
    }

    // Delegación: clic en sugerencias de productos
    productoSuggestions.addEventListener('click', function(e) {
        const item = e.target.closest('.producto-item');
        if (!item) return;
        const id = item.getAttribute('data-id');
        const nombre = item.getAttribute('data-nombre');
        const stock = item.getAttribute('data-stock');
        
        // Si se selecciona "BOTA" (pero no "BOTA TIPO TACTICO"), buscar y seleccionar "BOTA TIPO TACTICO" automáticamente
        const nombreUpper = nombre.toUpperCase();
        if ((nombreUpper.includes('BOTA') || nombreUpper === 'BOTAS') && !nombreUpper.includes('TACTICO')) {
            // Buscar "BOTA TIPO TACTICO"
            fetch(`{{ route('api.productos.search') }}?q=BOTA TIPO TACTICO&uniforme=true`)
                .then(r => r.json())
                .then(data => {
                    const botaTactico = data.find(p => p.nombre.toUpperCase().includes('BOTA') && p.nombre.toUpperCase().includes('TACTICO'));
                    if (botaTactico) {
                        // Seleccionar BOTA TIPO TACTICO en lugar de BOTA
                        productoIdInput.value = botaTactico.id;
                        searchInput.value = botaTactico.nombre;
                        productoSuggestions.classList.add('hidden');
                        showProductoSelected(botaTactico.nombre, botaTactico.stock_actual, botaTactico.id);
                    } else {
                        // Si no se encuentra, usar el producto seleccionado originalmente
                        productoIdInput.value = id;
                        searchInput.value = nombre;
                        productoSuggestions.classList.add('hidden');
                        showProductoSelected(nombre, stock, id);
                    }
                })
                .catch(err => {
                    console.error('Error al buscar BOTA TIPO TACTICO:', err);
                    // Si hay error, usar el producto seleccionado originalmente
                    productoIdInput.value = id;
                    searchInput.value = nombre;
                    productoSuggestions.classList.add('hidden');
                    showProductoSelected(nombre, stock, id);
                });
        } else {
            // Para cualquier otro producto, seleccionar normalmente
            productoIdInput.value = id;
            searchInput.value = nombre;
            productoSuggestions.classList.add('hidden');
            showProductoSelected(nombre, stock, id);
        }
    });

    // Eventos input producto
    searchInput.addEventListener('focus', function() {
        if (this.value.trim() === '' && productoIdInput.value === '') {
            fetchProductos('', true);
        }
    });

    searchInput.addEventListener('input', function() {
        const q = this.value.trim();
        if (q.length < 2) {
            productoSuggestions.classList.add('hidden');
            return;
        }
        fetchProductos(q, false);
    });

    verTodosBtn.addEventListener('click', function() {
        searchInput.value = '';
        productoIdInput.value = '';
        fetchProductos('', true);
        searchInput.focus();
    });

    productoClearBtn.addEventListener('click', clearProductoSelection);

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !productoSuggestions.contains(e.target)) {
            productoSuggestions.classList.add('hidden');
        }
    });

    // ======== POLICIAS: BUSCAR / SUGERENCIAS / CREAR ========
    let policiaTimeout;
    function fetchPolicias(qNombre = '', qEmpleado = '', showAll = false, conEntregas = false) {
        clearTimeout(policiaTimeout);
        policiaTimeout = setTimeout(() => {
            // NO actualizar el nombre completo automáticamente - el usuario debe ingresarlo manualmente
            // Solo buscar, no pre-llenar campos
        
        let url;
        if (showAll) {
            url = `{{ route('api.policias.search') }}?all=true`;
                if (conEntregas) url += '&con_entregas=true';
            } else if (qEmpleado && qEmpleado.length > 0) {
                url = `{{ route('api.policias.search') }}?q=${encodeURIComponent(qEmpleado)}&empleado=true`;
                if (conEntregas) url += '&con_entregas=true';
            } else if (qNombre && qNombre.length > 0) {
                url = `{{ route('api.policias.search') }}?q=${encodeURIComponent(qNombre)}`;
                if (conEntregas) url += '&con_entregas=true';
        } else {
                policiaSuggestions.classList.add('hidden');
            return;
        }
        
            policiaLoading.classList.remove('hidden');
            ocultarMensajeBusqueda();
        fetch(url)
                .then(r => {
                    if (!r.ok) throw new Error('Error en la respuesta del servidor');
                    return r.json();
                })
                .then(data => {
                    policiaLoading.classList.add('hidden');
                    renderPoliciaSuggestions(data, qNombre, qEmpleado);
                })
                .catch(err => {
                    policiaLoading.classList.add('hidden');
                    console.error(err);
                    mostrarMensajeBusqueda('❌ Error al buscar policías. Intenta nuevamente.', 'error');
                    policiaSuggestions.innerHTML = `
                        <div class="p-4 bg-red-50 border-2 border-red-200 rounded-lg">
                            <div class="flex items-center">
                                <span class="text-red-600 text-xl mr-2">⚠️</span>
                                <p class="text-sm text-red-700 font-semibold">Error al buscar policías. Por favor, intenta nuevamente.</p>
                            </div>
                        </div>
                    `;
                    policiaSuggestions.classList.remove('hidden');
                });
        }, 300);
    }

    function renderPoliciaSuggestions(items, qNombre, qEmpleado) {
        ocultarMensajeBusqueda();
        
        if (!items || items.length === 0) {
            // Si el usuario escribió número de empleado y no obtuvo resultados, mostrar formulario de creación
            if (qEmpleado && qEmpleado.length > 0 && policiaCreateForm.classList.contains('hidden')) {
                mostrarMensajeBusqueda('ℹ️ No se encontró el policía. Puedes crearlo completando el formulario.', 'info');
                // Verificar si parece un número de empleado (solo números) o es un nombre completo
                // Si tiene letras (aunque sea corto) O tiene espacios, es un nombre completo
                const tieneEspacios = qEmpleado.trim().includes(' ');
                const tieneLetras = /[a-zA-ZáéíóúÁÉÍÓÚñÑ]/.test(qEmpleado.trim());
                const soloNumeros = /^\d+$/.test(qEmpleado.trim());
                // Si tiene espacios O tiene letras (no solo números), es un nombre completo
                const esNombreCompleto = tieneEspacios || (tieneLetras && !soloNumeros);
                
                if (esNombreCompleto) {
                    // Si parece un nombre completo, copiarlo SOLO al campo de nombre y limpiar número de empleado
                    document.getElementById('policia_nombre_completo').value = qEmpleado.trim();
                    document.getElementById('policia_numero_empleado').value = '';
                    document.getElementById('policia_numero_placa').value = '';
                    } else {
                    // Si parece un número de empleado (sin espacios, corto, o solo números), copiarlo al campo de número de empleado
                    document.getElementById('policia_numero_empleado').value = qEmpleado.trim();
                    document.getElementById('policia_numero_placa').value = qEmpleado.trim();
                    // Limpiar nombre completo para que el usuario lo ingrese
                    document.getElementById('policia_nombre_completo').value = '';
                }
                
                policiaCreateForm.classList.remove('hidden');
                policiaSuggestions.classList.add('hidden');
                policiaSelectedDiv.classList.add('hidden');
                // Activar campos required cuando se muestra el formulario
                toggleRequiredFields(true);
                    return;
                }

            // Si el formulario ya está visible, no mostrar mensaje de "no encontrado"
            if (!policiaCreateForm.classList.contains('hidden')) {
                policiaSuggestions.classList.add('hidden');
                    return;
                }

            policiaSuggestions.innerHTML = `<div class="p-3 text-sm text-gray-500">No se encontró un policía con ese número de empleado. Se mostrará el formulario de creación.</div>`;
            policiaSuggestions.classList.remove('hidden');
            return;
        }
        
        // Si hay resultados, ocultar el formulario de creación
        if (!policiaCreateForm.classList.contains('hidden')) {
            policiaCreateForm.classList.add('hidden');
            toggleRequiredFields(false);
        }

        // NO seleccionar automáticamente - siempre mostrar sugerencias para que el usuario confirme
        // Si hay múltiples resultados o un solo resultado, mostrar mensaje
        if (items.length === 1) {
            mostrarMensajeBusqueda('✅ Se encontró 1 resultado. Confirma la selección:', 'success');
        } else if (items.length > 1) {
            mostrarMensajeBusqueda(`🔍 Se encontraron ${items.length} resultados. Selecciona uno:`, 'info');
        }

        // Mostrar resultados en tarjetas visuales - siempre requiere confirmación del usuario
        const esUnSoloResultado = items.length === 1;
        policiaSuggestions.innerHTML = `
            <div class="p-4 ${esUnSoloResultado ? 'bg-green-50 border-b-2 border-green-300' : 'bg-gradient-to-r from-indigo-50 to-blue-50 border-b-2 border-indigo-200'}">
                <p class="text-sm font-bold ${esUnSoloResultado ? 'text-green-800' : 'text-gray-800'} flex items-center">
                    <span class="text-lg mr-2">${esUnSoloResultado ? '✅' : '🔍'}</span>
                    ${esUnSoloResultado 
                        ? 'Se encontró <span class="text-green-600 font-extrabold mx-1">1</span> resultado. <span class="font-normal">Confirma que es el correcto:</span>' 
                        : `Se encontraron <span class="text-indigo-600 font-extrabold mx-1">${items.length}</span> resultado(s). <span class="text-gray-600 font-normal">Haz clic en uno para seleccionarlo:</span>`}
                </p>
            </div>
            <div class="divide-y divide-gray-200">
                ${items.map((p, index) => `
                    <div class="p-5 ${esUnSoloResultado ? 'bg-white border-l-4 border-green-600 shadow-md' : 'hover:bg-gradient-to-r hover:from-indigo-50 hover:to-blue-50 border-l-4 border-transparent hover:border-indigo-500'} cursor-pointer transition-all policia-item active:bg-indigo-100" 
                         data-id="${p.id}" 
                         data-nombre="${p.nombre_completo}" 
                         data-placa="${p.numero_placa || ''}"
                         data-empleado="${p.numero_empleado || ''}"
                         data-rango="${p.rango || ''}"
                         data-area="${p.area || ''}"
                         style="cursor: pointer;">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-14 h-14 bg-gradient-to-br ${esUnSoloResultado ? 'from-green-400 to-emerald-500' : 'from-indigo-400 to-blue-500'} rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                                    👮
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-lg font-bold text-gray-900 mb-2">${p.nombre_completo}</h4>
                                <div class="flex flex-wrap gap-2">
                                    ${p.numero_empleado ? `
                                        <span class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-800 rounded-lg text-xs font-bold shadow-sm">
                                            🆔 Empleado: ${p.numero_empleado}
                                        </span>
                                    ` : ''}
                                    ${p.rango ? `
                                        <span class="inline-flex items-center px-3 py-1.5 bg-purple-100 text-purple-800 rounded-lg text-xs font-bold shadow-sm">
                                            ⭐ ${p.rango}
                                        </span>
                                    ` : ''}
                                    ${p.area ? `
                                        <span class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-800 rounded-lg text-xs font-bold shadow-sm">
                                            🏢 ${p.area}
                                        </span>
                                    ` : ''}
                                </div>
                            </div>
                            <div class="flex-shrink-0 flex items-center">
                                <button type="button" class="px-6 py-2.5 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transition-all text-sm transform hover:scale-105 border-2 relative z-10"
                                        style="${esUnSoloResultado 
                                            ? 'background-color: #166534 !important; color: #ffffff !important; border-color: #14532d !important; min-width: 140px;' 
                                            : 'background: linear-gradient(to right, #4f46e5, #2563eb) !important; color: #ffffff !important; border-color: #3730a3 !important;'}">
                                    ${esUnSoloResultado ? '✅ Aceptar' : 'Seleccionar →'}
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
        policiaSuggestions.classList.remove('hidden');
    }

    // Delegación: clic en sugerencias policía - mejorado para que toda la tarjeta sea clicable
    policiaSuggestions.addEventListener('click', function(e) {
        const item = e.target.closest('.policia-item');
        if (!item) return;
        
        // Prevenir que se dispare el submit del formulario
        e.preventDefault();
        e.stopPropagation();
        
        // Agregar efecto visual de selección
        item.style.transform = 'scale(0.98)';
        setTimeout(() => {
            item.style.transform = '';
        }, 150);
        
        const id = item.getAttribute('data-id');
        const nombre = item.getAttribute('data-nombre');
        const placa = item.getAttribute('data-placa') || '';
        const empleado = item.getAttribute('data-empleado') || '';
        const rango = item.getAttribute('data-rango') || '';
        const area = item.getAttribute('data-area') || '';
        
        // Eliminar mensajes anteriores
        const mensajeExiste = document.getElementById('mensaje_empleado_existe');
        if (mensajeExiste) mensajeExiste.remove();
        const mensajeProductos = document.getElementById('mensaje_productos_entregados');
        if (mensajeProductos) mensajeProductos.remove();
        
        // Mostrar mensaje de selección exitosa
        mostrarMensajeBusqueda('✅ Policía seleccionado correctamente', 'success');
        if (policiaSearchStatus) {
            policiaSearchStatus.classList.remove('hidden');
        }
        
        policiaIdInput.value = id;
        policiaNombreInput.value = nombre;
        policiaEmpleadoInput.value = '';
        policiaSuggestions.classList.add('hidden');
        policiaCreateForm.classList.add('hidden');
        showPoliciaSelected(nombre, placa, id, empleado, rango, area);
        
        return false;
    });

    // Campo de búsqueda por nombre está oculto - no necesita event listeners
    // Solo se usa búsqueda por número de empleado

    const verificarEmpleadoBtn = document.getElementById('verificar_empleado_btn');
    const verificacionEmpleadoDiv = document.getElementById('verificacion_empleado');
    
    if (verificarEmpleadoBtn) {
        verificarEmpleadoBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('=== BOTÓN VERIFICAR CLICKEADO ===');
            let numeroEmpleado = policiaEmpleadoInput ? policiaEmpleadoInput.value.trim() : '';
            console.log('Número de empleado del input:', numeroEmpleado);
            console.log('Policía ID:', policiaIdInput ? policiaIdInput.value : 'NO ENCONTRADO');
            console.log('Policía seleccionado visible:', policiaSelectedDiv ? !policiaSelectedDiv.classList.contains('hidden') : 'NO ENCONTRADO');
            
            // Si no hay número en el campo pero hay un policía seleccionado, buscar su número de empleado
            if (!numeroEmpleado || numeroEmpleado.length === 0) {
                if (policiaIdInput && policiaIdInput.value && policiaIdInput.value !== '' && 
                    policiaSelectedDiv && !policiaSelectedDiv.classList.contains('hidden')) {
                    // Obtener el número de empleado del policía seleccionado desde el atributo data
                    numeroEmpleado = policiaSelectedDiv.getAttribute('data-empleado') || '';
                    console.log('Número de empleado del atributo data:', numeroEmpleado);
                    
                    // Si no está en el atributo, intentar obtenerlo de la API
                    if (!numeroEmpleado || numeroEmpleado === '') {
                        const policiaId = policiaIdInput.value;
                        console.log('Buscando número de empleado en API para policía ID:', policiaId);
                        fetch(`{{ route('api.policias.search') }}?all=true`)
                            .then(r => r.json())
                            .then(data => {
                                console.log('Datos recibidos de API:', data);
                                const policiaEncontrado = data.find(p => p.id == policiaId);
                                console.log('Policía encontrado:', policiaEncontrado);
                                if (policiaEncontrado && policiaEncontrado.numero_empleado) {
                                    // Actualizar el atributo data para futuras verificaciones
                                    policiaSelectedDiv.setAttribute('data-empleado', policiaEncontrado.numero_empleado);
                                    verificarPorNumeroEmpleado(policiaEncontrado.numero_empleado);
                                } else {
                                    alert('⚠️ Este policía no tiene número de empleado registrado');
                                    verificarEmpleadoBtn.disabled = false;
                                    verificarEmpleadoBtn.textContent = '🔍 Verificar';
                                }
                            })
                            .catch(err => {
                                console.error('Error en fetch:', err);
                                alert('⚠️ Error al obtener información del policía');
                                verificarEmpleadoBtn.disabled = false;
                                verificarEmpleadoBtn.textContent = '🔍 Verificar';
                            });
            return;
                    }
                }
        }

            if (!numeroEmpleado || numeroEmpleado.length === 0) {
                alert('⚠️ Por favor, ingresa un número de empleado o selecciona un policía para verificar');
            return;
        }

            console.log('Llamando verificarPorNumeroEmpleado con:', numeroEmpleado);
            verificarPorNumeroEmpleado(numeroEmpleado);
        });
    } else {
        console.error('Botón verificar_empleado_btn no encontrado');
    }
    
    function verificarPorNumeroEmpleado(numeroEmpleado) {
        console.log('=== VERIFICAR POR NÚMERO DE EMPLEADO ===');
        console.log('Número de empleado:', numeroEmpleado);
        
        if (!verificarEmpleadoBtn || !verificacionEmpleadoDiv) {
            console.error('Elementos no encontrados:', { verificarEmpleadoBtn, verificacionEmpleadoDiv });
            alert('Error: Elementos del formulario no encontrados');
            return;
        }

        verificarEmpleadoBtn.disabled = true;
        verificarEmpleadoBtn.textContent = '⏳ Verificando...';
        verificacionEmpleadoDiv.classList.add('hidden');
        
        const url = `{{ route('api.policias.verificar-entregas') }}?numero_empleado=${encodeURIComponent(numeroEmpleado)}`;
        console.log('URL de verificación:', url);
        
        fetch(url)
            .then(r => {
                console.log('Respuesta recibida:', r);
                if (!r.ok) {
                    throw new Error(`HTTP error! status: ${r.status}`);
                }
                return r.json();
            })
            .then(data => {
                console.log('Datos de verificación:', data);
                verificarEmpleadoBtn.disabled = false;
                verificarEmpleadoBtn.textContent = '🔍 Verificar';
                
                if (!data.registrado) {
                    verificacionEmpleadoDiv.innerHTML = `
                        <div class="p-3 bg-yellow-50 border-2 border-yellow-400 rounded-md">
                            <div class="flex items-center">
                                <span class="text-xl mr-2">⚠️</span>
                                <div>
                                    <p class="text-sm font-semibold text-yellow-900">Número de empleado no registrado</p>
                                    <p class="text-xs text-yellow-700 mt-1">Este número de empleado no está en el sistema. Puedes crear un nuevo policía.</p>
                                </div>
                            </div>
                        </div>
                    `;
                    verificacionEmpleadoDiv.classList.remove('hidden');
                } else {
                    const entregas = data.entregas || [];
                    const entregasHtml = entregas.length > 0 ? entregas.map(e => `
                        <div class="text-xs text-gray-700 mt-1 pl-2 border-l-2 border-gray-300">
                            • <strong>${e.producto}</strong> (Cantidad: ${e.cantidad}) - Fecha: ${e.fecha} - Folio: ${e.folio}
                        </div>
                    `).join('') : '';
                    
                    verificacionEmpleadoDiv.innerHTML = `
                        <div class="p-4 bg-white border-2 ${entregas.length > 0 ? 'border-red-400' : 'border-green-400'} rounded-lg shadow-lg">
                            <div class="mb-3">
                                <div class="font-bold text-lg text-gray-900 mb-2">${data.policia.nombre_completo}</div>
                                <div class="flex flex-wrap gap-3 text-sm">
                                    ${data.policia.numero_empleado ? `<span class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-800 rounded-md font-medium">
                                        <span class="mr-1">🆔</span> Empleado: ${data.policia.numero_empleado}
                                    </span>` : ''}
                                    ${data.policia.rango ? `<span class="inline-flex items-center px-3 py-1.5 bg-purple-100 text-purple-800 rounded-md font-medium">
                                        <span class="mr-1">⭐</span> ${data.policia.rango}
                                    </span>` : ''}
                                </div>
                            </div>
                            ${entregas.length > 0 ? `
                                <div class="mt-3 pt-3 border-t-2 border-red-300">
                                    <p class="text-sm font-bold text-red-900 mb-2">⚠️ Ya tiene ${entregas.length} entrega(s) registrada(s):</p>
                                    <div class="mt-2">
                                        ${entregasHtml}
                                    </div>
                                </div>
                            ` : `
                                <div class="mt-3 pt-3 border-t-2 border-green-300">
                                    <p class="text-sm font-semibold text-green-900">✅ No tiene entregas registradas</p>
                                </div>
                            `}
                        </div>
                    `;
                    verificacionEmpleadoDiv.classList.remove('hidden');
                }
            })
            .catch(err => {
                console.error('Error en verificación:', err);
                if (verificarEmpleadoBtn) {
                    verificarEmpleadoBtn.disabled = false;
                    verificarEmpleadoBtn.textContent = '🔍 Verificar';
                }
                alert('Error al verificar el número de empleado: ' + (err.message || 'Error desconocido'));
            });
    }

    // Mensaje de estado de búsqueda
    const policiaSearchMessage = document.getElementById('policia_search_message');
    const policiaSearchStatus = document.getElementById('policia_search_status');
    
    function mostrarMensajeBusqueda(mensaje, tipo = 'info') {
        if (!policiaSearchMessage) return;
        policiaSearchMessage.textContent = mensaje;
        policiaSearchMessage.className = `mt-2 text-sm ${tipo === 'error' ? 'text-red-600' : tipo === 'success' ? 'text-green-600' : 'text-gray-600'}`;
        policiaSearchMessage.classList.remove('hidden');
    }
    
    function ocultarMensajeBusqueda() {
        if (policiaSearchMessage) policiaSearchMessage.classList.add('hidden');
        if (policiaSearchStatus) policiaSearchStatus.classList.add('hidden');
    }

    policiaEmpleadoInput.addEventListener('input', function() {
        const qEmpleado = this.value.trim();
        
        // Ocultar estados anteriores
        ocultarMensajeBusqueda();
        if (verificacionEmpleadoDiv) verificacionEmpleadoDiv.classList.add('hidden');
        
        // Ocultar mensaje de "empleado ya existe" si existe
        const mensajeExiste = policiaSelectedDiv.nextElementSibling;
        if (mensajeExiste && mensajeExiste.classList.contains('bg-blue-50')) {
            mensajeExiste.remove();
        }
        
        // Si el formulario de creación está visible, verificar qué tipo de dato se está escribiendo
        if (!policiaCreateForm.classList.contains('hidden') && qEmpleado.length > 0) {
            const tieneEspacios = qEmpleado.trim().includes(' ');
            const tieneLetras = /[a-zA-ZáéíóúÁÉÍÓÚñÑ]/.test(qEmpleado.trim());
            const soloNumeros = /^\d+$/.test(qEmpleado.trim());
            const esNombreCompleto = tieneEspacios || (tieneLetras && !soloNumeros);
            
            if (esNombreCompleto) {
                const nombreCompletoField = document.getElementById('policia_nombre_completo');
                if (nombreCompletoField) {
                    nombreCompletoField.value = qEmpleado;
                }
                const numeroEmpleadoField = document.getElementById('policia_numero_empleado');
                if (numeroEmpleadoField) {
                    const primeraPalabra = qEmpleado.trim().split(' ')[0];
                    if (numeroEmpleadoField.value === primeraPalabra || numeroEmpleadoField.value.includes(primeraPalabra)) {
                        numeroEmpleadoField.value = '';
                        document.getElementById('policia_numero_placa').value = '';
                    }
                }
            } else {
                document.getElementById('policia_numero_empleado').value = qEmpleado;
                document.getElementById('policia_numero_placa').value = qEmpleado;
            }
        }
        
        // Si hay un policía seleccionado y se está escribiendo, limpiar la selección
        if (policiaIdInput.value && qEmpleado.length > 0) {
            clearPoliciaSelection();
        }
        
        if (qEmpleado.length === 0) { 
            policiaSuggestions.classList.add('hidden');
            if (!policiaCreateForm.classList.contains('hidden')) {
                policiaCreateForm.classList.add('hidden');
                toggleRequiredFields(false);
            }
            ocultarMensajeBusqueda();
            return;
        }
        
        // Mostrar mensaje de búsqueda
        if (qEmpleado.length >= 2) {
            mostrarMensajeBusqueda('🔍 Buscando...', 'info');
        }
        
        // Buscar automáticamente después de escribir al menos 2 caracteres
        if (qEmpleado.length >= 2) {
            fetchPolicias('', qEmpleado, false, false);
        } else if (qEmpleado.length === 1) {
            mostrarMensajeBusqueda('💡 Escribe al menos 2 caracteres para buscar', 'info');
        }
    });

    // Eliminados los botones "Ver Todos" y "Con Entregas" - simplificado a solo búsqueda por número de empleado

    // Crear policía (rápido)
    policiaCreateBtn.addEventListener('click', function() {
        const nombreCompleto = document.getElementById('policia_nombre_completo').value.trim();
        const numeroEmpleado = document.getElementById('policia_numero_empleado').value.trim();
        const rango = document.getElementById('policia_rango').value.trim();
        const area = document.getElementById('policia_area').value.trim();

        // El número de placa se llena automáticamente igual al número de empleado
        const numeroPlaca = numeroEmpleado;
        document.getElementById('policia_numero_placa').value = numeroPlaca;

        if (!nombreCompleto || !numeroEmpleado) {
            alert('⚠️ El nombre completo y número de empleado son obligatorios');
            return;
        }

        const btn = this;
        const btnOriginalText = btn.innerHTML; // Guardar el texto original con emoji
        btn.disabled = true;
        btn.innerHTML = '⏳ Verificando...';

        // Primero verificar si el número de empleado ya existe
        fetch(`{{ route('api.policias.verificar-entregas') }}?numero_empleado=${encodeURIComponent(numeroEmpleado)}`)
            .then(r => r.json())
            .then(data => {
                if (data.success && data.registrado) {
                    // El número de empleado ya existe
                    btn.disabled = false;
                    btn.innerHTML = btnOriginalText; // Restaurar texto original
                    alert(`⚠️ ERROR: El número de empleado "${numeroEmpleado}" ya está registrado.\n\nPolicía existente: ${data.policia.nombre_completo}\n\nPor favor, usa un número de empleado diferente o selecciona el policía existente desde la búsqueda.`);
                    return;
                }
                
                // Si no existe, proceder a crear
                btn.innerHTML = '⏳ Creando...';
                return fetch('{{ route("api.policias.quick-create") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify({
                nombre_completo: nombreCompleto,
                numero_placa: numeroPlaca,
                numero_empleado: numeroEmpleado,
                rango: rango,
                area: area
            })
                });
        })
        .then(response => {
                if (!response) return null; // Si la respuesta es null, significa que ya se mostró el error
                
                // Verificar si la respuesta es un error
            if (!response.ok) {
                    return response.json().then(errData => {
                        throw new Error(errData.message || 'Error al crear el policía');
                });
            }
                
            return response.json();
        })
        .then(data => {
                if (!data) return; // Si data es null, significa que ya se mostró el error
                
                if (data && data.success) {
                    policiaIdInput.value = data.policia.id;
                    policiaIdInput.setAttribute('value', data.policia.id);
                    if (policiaNombreInput) policiaNombreInput.value = data.policia.nombre_completo;
                    policiaEmpleadoInput.value = '';
                policiaCreateForm.classList.add('hidden');
                    // Desactivar campos required cuando se oculta el formulario
                    toggleRequiredFields(false);
                    // Limpiar el campo de nombre completo del formulario de creación
                    document.getElementById('policia_nombre_completo').value = '';
                    showPoliciaSelected(data.policia.nombre_completo, data.policia.numero_placa, data.policia.id, data.policia.numero_empleado || '', data.policia.rango || '', data.policia.area || '');
                    
                    // Asegurar que el botón ACEPTAR sea visible y funcional
                    const btnRegistrar = document.getElementById('btnRegistrar');
                    if (btnRegistrar) {
                        btnRegistrar.style.display = 'inline-flex';
                        btnRegistrar.style.visibility = 'visible';
                        btnRegistrar.style.opacity = '1';
                        btnRegistrar.disabled = false;
                        btnRegistrar.classList.remove('hidden');
                        btnRegistrar.classList.remove('opacity-0');
                        btnRegistrar.style.pointerEvents = 'auto';
                        btnRegistrar.style.cursor = 'pointer';
                        console.log('Botón ACEPTAR habilitado después de crear policía');
            } else {
                        console.error('Botón btnRegistrar no encontrado');
                    }
                    
                    const successMsg = document.createElement('div');
                    successMsg.className = 'mt-2 p-3 bg-green-100 border-2 border-green-400 text-green-700 rounded text-sm font-semibold';
                    successMsg.textContent = '✓ Policía creado exitosamente';
                    policiaSelectedDiv.parentNode.insertBefore(successMsg, policiaSelectedDiv.nextSibling);
                    setTimeout(() => successMsg.remove(), 3000);
                } else {
                    // Si hay un policía existente en la respuesta, mostrar información
                    if (data && data.policia_existente) {
                        throw new Error(`El número de empleado "${numeroEmpleado}" ya está registrado.\n\nPolicía existente: ${data.policia_existente.nombre_completo}\n\nPor favor, usa un número de empleado diferente.`);
                    }
                    throw (data && data.message) ? new Error(data.message) : new Error('Error desconocido');
                }
            })
            .catch(err => {
                console.error(err);
                let msg = 'Error al crear el policía. ';
                
                // Verificar si es un error de validación (número de empleado duplicado)
                if (err.message && err.message.includes('numero_empleado')) {
                    msg += 'El número de empleado ya está registrado. Por favor, usa un número diferente.';
                } else if (err.message && err.message.includes('numero_placa')) {
                    msg += 'El número de placa ya está registrado. Por favor, usa un número diferente.';
            } else {
                    msg += (err.message || 'Verifica los datos.');
                }
                
                alert('⚠️ ' + msg);
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = btnOriginalText; // Restaurar texto original con emoji
        });
    });

    policiaCancelCreateBtn.addEventListener('click', function() {
        policiaCreateForm.classList.add('hidden');
        // Desactivar campos required cuando se cancela
        toggleRequiredFields(false);
        policiaNombreInput.focus();
    });

    policiaClearBtn.addEventListener('click', clearPoliciaSelection);

    // ======== FORM VALIDATION ========
    if (btnRegistrar) {
        btnRegistrar.disabled = false;
        btnRegistrar.style.opacity = '1';
        btnRegistrar.style.cursor = 'pointer';
    }

    if (formEntrega) {
        formEntrega.addEventListener('submit', function(e) {
            console.log('=== FORMULARIO INTENTANDO ENVIAR ===');
            console.log('Policía ID:', policiaIdInput ? policiaIdInput.value : 'NO ENCONTRADO');
            console.log('Producto ID:', productoIdInput ? productoIdInput.value : 'NO ENCONTRADO');
            
            const policiaIdVal = policiaIdInput ? policiaIdInput.value : '';
            const productoIdVal = productoIdInput ? productoIdInput.value : '';

            if (!policiaIdVal || policiaIdVal === '' || policiaIdVal === 'null') {
                e.preventDefault();
                e.stopPropagation();
                alert('⚠️ Por favor, selecciona o crea un policía antes de continuar.');
                if (policiaNombreInput && policiaNombreInput.style.display !== 'none') {
                    policiaNombreInput.focus();
                } else if (policiaClearBtn) {
                    policiaClearBtn.click();
                }
                return false;
            }

            if (!productoIdVal || productoIdVal === '' || productoIdVal === 'null') {
                e.preventDefault();
                e.stopPropagation();
                alert('⚠️ Por favor, selecciona un producto (uniforme/equipo) antes de continuar.');
                if (searchInput && searchInput.style.display !== 'none') {
                    searchInput.focus();
                } else if (productoClearBtn) {
                    productoClearBtn.click();
                }
                return false;
            }

            // Verificar si el policía ya tiene entregas antes de permitir el envío
            e.preventDefault();
            e.stopPropagation();
            
            console.log('🔍 Verificando entregas del policía...');
            console.log('Policía ID:', policiaIdVal);
            console.log('Policía Selected Div visible:', policiaSelectedDiv ? !policiaSelectedDiv.classList.contains('hidden') : 'NO ENCONTRADO');
            
            // Obtener el número de empleado del policía seleccionado
            let numeroEmpleado = '';
            let nombrePolicia = '';
            
            // Intentar obtener del atributo data-empleado del div seleccionado
            if (policiaSelectedDiv && !policiaSelectedDiv.classList.contains('hidden')) {
                numeroEmpleado = policiaSelectedDiv.getAttribute('data-empleado') || '';
                nombrePolicia = policiaSelectedNombre ? policiaSelectedNombre.textContent.trim() : '';
                console.log('Número de empleado del div:', numeroEmpleado);
                console.log('Nombre del policía:', nombrePolicia);
            }
            
            // Si no hay número de empleado, intentar obtenerlo del campo de número de empleado del formulario de creación
            if (!numeroEmpleado) {
                const numeroEmpleadoInput = document.getElementById('policia_numero_empleado');
                if (numeroEmpleadoInput && numeroEmpleadoInput.value) {
                    numeroEmpleado = numeroEmpleadoInput.value.trim();
                    console.log('Número de empleado del input:', numeroEmpleado);
                }
            }
            
            // Si aún no hay número de empleado pero tenemos el ID, buscar el policía
            if (!numeroEmpleado && policiaIdVal) {
                console.log('Buscando policía por ID para obtener número de empleado...');
                // Buscar todos los policías y encontrar el que coincide con el ID
                fetch(`{{ route('api.policias.search') }}?all=true`)
                    .then(r => r.json())
                    .then(policias => {
                        console.log('Policías encontrados:', policias.length);
                        const policia = policias.find(p => p.id == policiaIdVal || p.id == parseInt(policiaIdVal));
                        console.log('Policía encontrado:', policia);
                        if (policia) {
                            numeroEmpleado = policia.numero_empleado || '';
                            nombrePolicia = policia.nombre_completo || nombrePolicia;
                            console.log('Número de empleado obtenido:', numeroEmpleado);
                            verificarYEnviar(numeroEmpleado, nombrePolicia);
                        } else {
                            console.log('No se encontró el policía, procediendo sin verificación');
                            enviarFormulario();
                        }
                    })
                    .catch(err => {
                        console.error('Error al buscar policía:', err);
                        enviarFormulario();
                    });
            } else if (numeroEmpleado) {
                console.log('Tengo número de empleado, verificando entregas...');
                verificarYEnviar(numeroEmpleado, nombrePolicia);
            } else {
                console.log('No hay número de empleado disponible, procediendo sin verificación');
                enviarFormulario();
            }
            
            function verificarYEnviar(numEmpleado, nombre) {
                if (!numEmpleado || numEmpleado === '') {
                    console.log('No hay número de empleado para verificar, procediendo');
                    enviarFormulario();
                    return;
                }
                
                console.log('Verificando entregas para empleado:', numEmpleado);
                // Verificar entregas antes de enviar
                const url = `{{ route('api.policias.verificar-entregas') }}?numero_empleado=${encodeURIComponent(numEmpleado)}`;
                
                fetch(url)
                    .then(r => {
                        console.log('Respuesta de verificación recibida');
                        return r.json();
                    })
                    .then(data => {
                        console.log('Datos de verificación:', data);
                        
                        // Obtener el producto que se está intentando entregar
                        const productoIdActual = productoIdInput ? productoIdInput.value : '';
                        const productoNombreActual = productoSelectedNombre ? productoSelectedNombre.textContent.trim() : '';
                        
                        if (data.success && data.registrado && data.entregas && data.entregas.length > 0) {
                            // Verificar si el producto específico que se está intentando entregar ya fue entregado
                            const productoYaEntregado = data.entregas.find(e => {
                                // Comparar por ID si está disponible, o por nombre
                                return e.producto_id == productoIdActual || 
                                       e.producto.toLowerCase() === productoNombreActual.toLowerCase();
                            });
                            
                            if (productoYaEntregado) {
                                // El producto específico ya fue entregado - BLOQUEAR completamente
                                const productosEntregados = data.entregas.map(e => e.producto).join(', ');
                                const mensaje = `⚠️ NO SE PUEDE REALIZAR LA SALIDA\n\n` +
                                    `El producto "${productoNombreActual}" ya fue entregado anteriormente al policía:\n` +
                                    `• Nombre: ${nombre || 'N/A'}\n` +
                                    `• Número de Empleado: ${numEmpleado}\n\n` +
                                    `Productos ya entregados a este policía:\n` +
                                    `• ${productosEntregados}\n\n` +
                                    `Cada producto solo puede entregarse una vez por policía. ` +
                                    `Si necesitas entregar otro producto diferente, selecciona un producto que aún no se haya entregado.`;
                                
                                alert(mensaje);
                                
                                // Restaurar el botón
                                if (btnRegistrar) {
                                    btnRegistrar.disabled = false;
                                    btnRegistrar.style.opacity = '1';
                                    btnRegistrar.style.cursor = 'pointer';
                                    btnRegistrar.innerHTML = '<span style="color: #000000 !important;">ACEPTAR</span>';
                                }
                                
                                // NO permitir el envío - bloquear completamente
                                console.log('Producto ya entregado - bloqueando envío');
                                return false;
                            }
                        }
                        
                        // Si el producto no ha sido entregado, proceder con el envío
                        console.log('Producto no entregado anteriormente - procediendo con el envío');
                        enviarFormulario();
                    })
                    .catch(err => {
                        console.error('Error al verificar entregas:', err);
                        // Si hay error en la verificación, permitir el envío de todas formas
                        alert('⚠️ No se pudo verificar las entregas anteriores. Continuando con el registro...');
                        enviarFormulario();
                    });
            }
            
            function enviarFormulario() {
                console.log('✅ Enviando formulario...');
                
                if (btnRegistrar) {
                    btnRegistrar.disabled = true;
                    btnRegistrar.style.opacity = '0.7';
                    btnRegistrar.style.cursor = 'not-allowed';
                    btnRegistrar.innerHTML = '<span class="mr-2" style="color: #000000 !important;">⏳</span><span style="color: #000000 !important;">PROCESANDO...</span>';
                }
                
                // Enviar el formulario
                formEntrega.submit();
            }
            
            return false;
        });
    }

});
</script>
@endsection
