@extends('layouts.app')

@section('title', 'Nuevo Policía')

@section('content')
<div class="px-4 sm:px-0">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Registrar Nuevo Policía</h1>

    <div class="bg-white shadow sm:rounded-lg">
        <form method="POST" action="{{ route('policias.store') }}" class="p-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="numero_placa" class="block text-sm font-medium text-gray-700">Número de Placa *</label>
                    <input type="text" name="numero_placa" id="numero_placa" value="{{ old('numero_placa') }}" required
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('numero_placa')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="numero_empleado" class="block text-sm font-medium text-gray-700">Número de Empleado (Único)</label>
                    <input type="text" name="numero_empleado" id="numero_empleado" value="{{ old('numero_empleado') }}"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        placeholder="Debe ser único">
                    <p class="mt-1 text-xs text-gray-500">El número de empleado debe ser único. Se usará para verificar duplicados.</p>
                    @error('numero_empleado')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nombre_completo" class="block text-sm font-medium text-gray-700">Nombre Completo *</label>
                    <input type="text" name="nombre_completo" id="nombre_completo" value="{{ old('nombre_completo') }}" required
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('nombre_completo')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="rango" class="block text-sm font-medium text-gray-700">Rango</label>
                    <input type="text" name="rango" id="rango" value="{{ old('rango') }}"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div>
                    <label for="area" class="block text-sm font-medium text-gray-700">Área</label>
                    <input type="text" name="area" id="area" value="{{ old('area') }}"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div>
                    <label for="activo" class="flex items-center">
                        <input type="checkbox" name="activo" id="activo" value="1" {{ old('activo', true) ? 'checked' : '' }}
                            class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">Activo</span>
                    </label>
                </div>

                <div class="sm:col-span-2">
                    <label for="observaciones" class="block text-sm font-medium text-gray-700">Observaciones</label>
                    <textarea name="observaciones" id="observaciones" rows="3"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('observaciones') }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end space-x-3">
                <a href="{{ route('policias.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Registrar Policía
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

