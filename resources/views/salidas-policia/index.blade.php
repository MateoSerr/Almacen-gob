@extends('layouts.app')

@section('title', 'Salidas a Policías')

@section('content')
<div class="px-4 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Entregas de Uniformes/Equipo a Policías</h1>
        <a href="{{ route('salidas-policia.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Nueva Entrega
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
        @php
            // Limpiar el mensaje de error de la sesión después de mostrarlo
            session()->forget('error');
        @endphp
    @endif

    <div class="bg-white shadow overflow-hidden sm:rounded-md mb-6">
        <form method="GET" action="{{ route('salidas-policia.index') }}" class="p-4 border-b">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Policía</label>
                    <select name="policia_id" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Todos los policías</option>
                        @foreach($policias as $policia)
                            <option value="{{ $policia->id }}" {{ request('policia_id') == $policia->id ? 'selected' : '' }}>
                                {{ $policia->nombre_completo }}@if($policia->numero_empleado) (Empleado: {{ $policia->numero_empleado }})@endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Producto</label>
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha desde</label>
                    <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha hasta</label>
                    <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">Buscar</button>
                    <a href="{{ route('salidas-policia.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Limpiar</a>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
            @forelse($salidas as $salida)
                <li>
                    <div class="block hover:bg-gray-50">
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <a href="{{ route('salidas-policia.imprimir', $salida) }}" class="flex items-center flex-1">
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Entrega Policía
                                        </span>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $salida->producto->nombre }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            @if($salida->policia)
                                                Policía: <strong>{{ $salida->policia->nombre_completo }}</strong>@if($salida->policia->numero_empleado) (Empleado: {{ $salida->policia->numero_empleado }})@endif
                                            @elseif($salida->policia_id)
                                                Policía: <strong>ID: {{ $salida->policia_id }}</strong> (Policía no encontrado en el sistema)
                                            @else
                                                Policía: <strong>No asignado</strong>
                                            @endif
                                            • Cantidad: {{ $salida->cantidad }} {{ $salida->producto->unidad_medida }}
                                            • Fecha: {{ $salida->fecha->format('d/m/Y') }}
                                            @if($salida->folio)
                                                • Folio: {{ $salida->folio }}
                                            @endif
                                        </div>
                                    </div>
                                </a>
                                <div class="ml-4 flex gap-2">
                                    <a href="{{ route('salidas-policia.imprimir', $salida) }}" 
                                       class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2 px-3 rounded transition-all" 
                                       title="Imprimir">
                                        🖨️ Imprimir
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-4 py-4 sm:px-6 text-center text-gray-500">
                    No se encontraron entregas a policías.
                </li>
            @endforelse
        </ul>
    </div>

    <div class="mt-4">
        {{ $salidas->links() }}
    </div>
</div>
@endsection



