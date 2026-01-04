@extends('layouts.app')

@section('title', 'Detalle de Entrega a Policía')

@section('content')
<div class="px-4 sm:px-0">
    <!-- Botones de acción -->
    <div class="flex justify-between items-center mb-6 no-print">
        <h1 class="text-3xl font-bold text-gray-900">Detalle de Entrega a Policía</h1>
        <div class="space-x-2">
            <button onclick="window.print()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                🖨️ Imprimir
            </button>
            <a href="{{ route('salidas-policia.edit', ['salida' => $salida->id]) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                ✏️ Editar
            </a>
            <a href="{{ route('salidas-policia.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                Volver
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Información de la entrega -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Información de la Entrega</h3>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Folio</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $salida->folio ?? 'N/A' }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Policía</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <strong>{{ $salida->policia->nombre_completo }}</strong>
                        @if($salida->policia->numero_empleado)
                            <br>
                            <span class="text-gray-600">Número de Empleado: {{ $salida->policia->numero_empleado }}</span>
                        @endif
                        @if($salida->policia->rango)
                            <br>
                            <span class="text-gray-600">Rango: {{ $salida->policia->rango }}</span>
                        @endif
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Producto</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <strong>{{ $salida->producto->nombre }}</strong>
                        <br>
                        <span class="text-gray-600">Código: {{ $salida->producto->codigo }}</span>
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Cantidad</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $salida->cantidad }} {{ $salida->producto->unidad_medida }}
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Fecha</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $salida->fecha->format('d/m/Y') }}</dd>
                </div>
                @if($salida->motivo)
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Motivo</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $salida->motivo }}</dd>
                </div>
                @endif
                @if($salida->observaciones)
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Observaciones</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $salida->observaciones }}</dd>
                </div>
                @endif
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Registrado por</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $salida->usuario->name ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Información de entrega y recepción -->
    @if($salida->entrega_nombre || $salida->recibe_nombre)
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Información de Entrega y Recepción</h3>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                @if($salida->entrega_nombre)
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Nombre del que Entrega</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $salida->entrega_nombre }}</dd>
                </div>
                @endif
                @if($salida->recibe_nombre)
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Nombre del que Recibe</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $salida->recibe_nombre }}</dd>
                </div>
                @endif
            </dl>
        </div>
    </div>
    @endif
</div>

@if(session('autoPrint'))
<script>
    window.onload = function() {
        window.print();
    };
</script>
@endif

<style>
    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>
@endsection



