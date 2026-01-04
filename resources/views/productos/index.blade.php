@extends('layouts.app')

@section('title', 'Productos')

@section('content')
<div class="px-4 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Productos</h1>
        <a href="{{ route('productos.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Nuevo Producto
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md mb-6">
        <form method="GET" action="{{ route('productos.index') }}" class="p-4 border-b">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por nombre, código o categoría" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="solo_alerta" value="1" {{ request('solo_alerta') ? 'checked' : '' }} class="mr-2">
                        <span>Solo productos con alerta (stock < 100)</span>
                    </label>
                </div>
                <div>
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">Buscar</button>
                    <a href="{{ route('productos.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Limpiar</a>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
            @forelse($productos as $producto)
                <li>
                    <a href="{{ route('productos.show', $producto) }}" class="block hover:bg-gray-50">
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @if($producto->stock_actual < 100)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                ⚠️ Alerta
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                OK
                                            </span>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $producto->nombre }}
                                            <span class="text-gray-500">({{ $producto->codigo }})</span>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            @if($producto->categoria)
                                                {{ $producto->categoria }} •
                                            @endif
                                            Stock: {{ $producto->stock_actual }} {{ $producto->unidad_medida }}
                                            @if($producto->precio_unitario > 0)
                                                • Precio: ${{ number_format($producto->precio_unitario, 2) }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('productos.edit', $producto) }}" class="text-blue-600 hover:text-blue-900 text-sm">Editar</a>
                                    <form method="POST" action="{{ route('productos.destroy', $producto) }}" class="inline" onsubmit="return confirm('¿Está seguro de eliminar este producto?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Eliminar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
            @empty
                <li class="px-4 py-4 sm:px-6 text-center text-gray-500">
                    No se encontraron productos.
                </li>
            @endforelse
        </ul>
    </div>

    <div class="mt-4">
        {{ $productos->links() }}
    </div>

    @if(isset($productosAlerta) && $productosAlerta->count() > 0)
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mt-6" role="alert">
            <p class="font-bold">⚠️ Alerta: Productos con stock menor a 100 unidades</p>
            <ul class="mt-2 list-disc list-inside">
                @foreach($productosAlerta as $producto)
                    <li>{{ $producto->nombre }} - Stock: {{ $producto->stock_actual }} {{ $producto->unidad_medida }} (Pronto a acabar)</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection


