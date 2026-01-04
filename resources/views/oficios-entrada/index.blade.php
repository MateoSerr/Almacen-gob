@extends('layouts.app')

@section('title', 'Oficios de Entrada')

@section('content')
<div class="px-4 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Oficios de Entrada</h1>
        <a href="{{ route('oficios-entrada.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Nuevo Oficio de Entrada
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md mb-6">
        <form method="GET" action="{{ route('oficios-entrada.index') }}" class="p-4 border-b">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <input type="text" name="proveedor" value="{{ request('proveedor') }}" placeholder="Buscar por proveedor" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" placeholder="Fecha desde" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" placeholder="Fecha hasta" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">Buscar</button>
                    <a href="{{ route('oficios-entrada.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Limpiar</a>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
            @forelse($oficios as $oficio)
                <li>
                    <a href="{{ route('oficios-entrada.show', $oficio) }}" class="block hover:bg-gray-50">
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Oficio de Entrada
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $oficio->folio_completo }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Fecha: {{ $oficio->fecha_oficio->format('d/m/Y') }}
                                            • Proveedor: {{ $oficio->proveedor_nombre }}
                                            • Factura: {{ $oficio->numero_factura }}
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            {{ Str::limit($oficio->descripcion_material, 80) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">${{ number_format($oficio->importe_total, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
            @empty
                <li class="px-4 py-4 sm:px-6 text-center text-gray-500">
                    No se encontraron oficios de entrada.
                </li>
            @endforelse
        </ul>
    </div>

    <div class="mt-4">
        {{ $oficios->links() }}
    </div>
</div>
@endsection



