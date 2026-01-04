@extends('layouts.app')

@section('title', 'Detalle de Oficio de Entrada')

@section('content')
<div class="px-4 sm:px-0">
    <div class="flex justify-between items-center mb-6 no-print">
        <h1 class="text-3xl font-bold text-gray-900">Detalle de Oficio de Entrada</h1>
        <div class="flex gap-3">
            <a href="{{ route('oficios-entrada.descargar-word', ['oficioEntrada' => $oficioEntrada->id]) }}" 
               class="inline-flex items-center px-6 py-3 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 border-2"
               style="background-color: #7c3aed !important; color: #ffffff !important; border-color: #6d28d9 !important;">
                📄 Descargar Word
            </a>
            <a href="{{ route('oficios-entrada.imprimir', ['oficioEntrada' => $oficioEntrada->id]) }}" 
               target="_blank" 
               class="inline-flex items-center px-6 py-3 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 border-2"
               style="background-color: #166534 !important; color: #ffffff !important; border-color: #14532d !important;">
                🖨️ Imprimir
            </a>
            <a href="{{ route('oficios-entrada.edit', ['oficioEntrada' => $oficioEntrada->id]) }}" 
               class="inline-flex items-center px-6 py-3 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 border-2"
               style="background-color: #2563eb !important; color: #ffffff !important; border-color: #1e40af !important;">
                ✏️ Editar
            </a>
            <a href="{{ route('oficios-entrada.index') }}" 
               class="inline-flex items-center px-6 py-3 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 border-2"
               style="background-color: #6b7280 !important; color: #ffffff !important; border-color: #4b5563 !important;">
                ← Volver
            </a>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Información del Oficio</h3>
        </div>
        <dl>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Número de Oficio</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-bold">{{ $oficioEntrada->numero_oficio }}</dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Folio Completo</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-bold">{{ $oficioEntrada->folio_completo }}</dd>
            </div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Fecha del Oficio</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $oficioEntrada->fecha_oficio->format('d/m/Y') }}</dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Descripción del Material</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $oficioEntrada->descripcion_material }}</dd>
            </div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Fecha de Recepción</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $oficioEntrada->fecha_recepcion->format('d/m/Y') }}</dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Proveedor</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $oficioEntrada->proveedor_nombre }}</dd>
            </div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Número de Factura</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $oficioEntrada->numero_factura }}</dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Importe Total</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-bold text-lg">${{ number_format($oficioEntrada->importe_total, 2) }}</dd>
            </div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Importe en Letras</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-bold">{{ $oficioEntrada->importe_total_letra }}</dd>
            </div>
        </dl>
    </div>
</div>
@endsection



