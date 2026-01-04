@extends('layouts.app')

@section('title', 'Policías')

@section('content')
<div class="px-4 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Gestión de Policías</h1>
        <a href="{{ route('policias.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Nuevo Policía
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow overflow-hidden sm:rounded-md mb-6">
        <form method="GET" action="{{ route('policias.index') }}" class="p-4 border-b">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por nombre, placa, número de empleado o rango..." class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <select name="activo" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Todos</option>
                        <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activos</option>
                        <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">Buscar</button>
                    <a href="{{ route('policias.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Limpiar</a>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
            @forelse($policias as $policia)
                <li>
                    <a href="{{ route('policias.show', $policia) }}" class="block hover:bg-gray-50">
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $policia->activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $policia->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $policia->nombre_completo }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Placa: {{ $policia->numero_placa }}
                                            @if($policia->numero_empleado)
                                                • Núm. Empleado: {{ $policia->numero_empleado }}
                                            @endif
                                            @if($policia->rango)
                                                • Rango: {{ $policia->rango }}
                                            @endif
                                            @if($policia->area)
                                                • Área: {{ $policia->area }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('policias.edit', $policia) }}" class="text-blue-600 hover:text-blue-800 text-sm">Editar</a>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
            @empty
                <li class="px-4 py-4 sm:px-6 text-center text-gray-500">
                    No se encontraron policías.
                </li>
            @endforelse
        </ul>
    </div>

    <div class="mt-4">
        {{ $policias->links() }}
    </div>
</div>
@endsection

