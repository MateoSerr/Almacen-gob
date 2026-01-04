@extends('layouts.app')

@section('title', 'Editar Oficio de Entrada')

@section('content')
<div class="px-4 sm:px-0">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Editar Oficio de Entrada</h1>

    <div class="bg-white shadow sm:rounded-lg">
        <form method="POST" action="{{ route('oficios-entrada.update', $oficioEntrada) }}" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="fecha_oficio" class="block text-sm font-medium text-gray-700">Fecha del Oficio *</label>
                    <input type="date" name="fecha_oficio" id="fecha_oficio" value="{{ old('fecha_oficio', $oficioEntrada->fecha_oficio->format('Y-m-d')) }}" required
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('fecha_oficio')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="descripcion_material" class="block text-sm font-medium text-gray-700">Descripción del Material Recibido *</label>
                    <p class="text-xs text-gray-500 mb-2">Ejemplo: (122 PIEZA) CAJAS DE PLASTICO DE 60X40X28 TIPO ZAMORA CON TAPA</p>
                    <textarea name="descripcion_material" id="descripcion_material" rows="3" required
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('descripcion_material', $oficioEntrada->descripcion_material) }}</textarea>
                    @error('descripcion_material')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="fecha_recepcion" class="block text-sm font-medium text-gray-700">Fecha de Recepción *</label>
                    <input type="date" name="fecha_recepcion" id="fecha_recepcion" value="{{ old('fecha_recepcion', $oficioEntrada->fecha_recepcion->format('Y-m-d')) }}" required
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('fecha_recepcion')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="proveedor_nombre" class="block text-sm font-medium text-gray-700">Nombre del Proveedor *</label>
                    <input type="text" name="proveedor_nombre" id="proveedor_nombre" value="{{ old('proveedor_nombre', $oficioEntrada->proveedor_nombre) }}" required
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('proveedor_nombre')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="numero_factura" class="block text-sm font-medium text-gray-700">Número de Factura *</label>
                    <input type="text" name="numero_factura" id="numero_factura" value="{{ old('numero_factura', $oficioEntrada->numero_factura) }}" required
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('numero_factura')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="importe_total" class="block text-sm font-medium text-gray-700">Importe Total *</label>
                    <input type="number" step="0.01" name="importe_total" id="importe_total" value="{{ old('importe_total', $oficioEntrada->importe_total) }}" required min="0"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    <p class="mt-1 text-xs text-gray-500">El importe se convertirá automáticamente a letras.</p>
                    @error('importe_total')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('oficios-entrada.show', $oficioEntrada) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Actualizar Oficio de Entrada
                </button>
            </div>
        </form>
    </div>
</div>
@endsection



