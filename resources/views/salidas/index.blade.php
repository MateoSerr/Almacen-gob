@extends('layouts.app')

@section('title', 'Salidas')

@section('content')
<div class="px-4 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Salidas de Inventario</h1>
        <a href="{{ route('salidas.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Nueva Salida
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md mb-6">
        <form method="GET" action="{{ route('salidas.index') }}" class="p-4 border-b">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <select name="producto_id" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Todos los productos</option>
                        @foreach($productos as $producto)
                            <option value="{{ $producto->id }}" {{ request('producto_id') == $producto->id ? 'selected' : '' }}>
                                {{ $producto->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" placeholder="Fecha desde" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" placeholder="Fecha hasta" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">Buscar</button>
                    <a href="{{ route('salidas.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Limpiar</a>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
            @forelse($salidas as $salida)
                <li>
                    <a href="{{ route('salidas.show', $salida) }}" class="block hover:bg-gray-50">
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Salida
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $salida->producto->nombre }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Cantidad: {{ $salida->cantidad }} {{ $salida->producto->unidad_medida }}
                                            • Fecha: {{ $salida->fecha->format('d/m/Y') }}
                                            @if($salida->motivo)
                                                • Motivo: {{ $salida->motivo }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
            @empty
                <li class="px-4 py-4 sm:px-6 text-center text-gray-500">
                    No se encontraron salidas.
                </li>
            @endforelse
        </ul>
    </div>

    <div class="mt-4">
        {{ $salidas->links() }}
    </div>
</div>
@endsection


