@extends('layouts.app')

@section('title', 'Inventario')

@section('content')
<div class="px-4 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">📦 Inventario Completo</h1>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <div class="text-sm font-medium text-gray-500">Total Productos</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalProductos) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <div class="text-sm font-medium text-gray-500">Productos con Alerta</div>
            <div class="text-2xl font-bold text-yellow-600 mt-1">{{ number_format($productosAlerta) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <div class="text-sm font-medium text-gray-500">Valor Total</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">${{ number_format($valorTotal, 2) }}</div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md mb-6">
        <form method="GET" action="{{ route('inventario.index') }}" class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar producto..." class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <select name="categoria" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Todas las categorías</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria }}" {{ request('categoria') == $categoria ? 'selected' : '' }}>{{ $categoria }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="solo_alerta" value="1" {{ request('solo_alerta') ? 'checked' : '' }} class="mr-2">
                        <span>Solo con alerta (stock < 100)</span>
                    </label>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Buscar</button>
                    <a href="{{ route('inventario.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Limpiar</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabla de Inventario -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ route('inventario.index', array_merge(request()->all(), ['orden' => 'codigo', 'direccion' => request('orden') == 'codigo' && request('direccion') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-gray-700">
                                Código ↕
                            </a>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ route('inventario.index', array_merge(request()->all(), ['orden' => 'nombre', 'direccion' => request('orden') == 'nombre' && request('direccion') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-gray-700">
                                Producto ↕
                            </a>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Categoría
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ route('inventario.index', array_merge(request()->all(), ['orden' => 'stock_actual', 'direccion' => request('orden') == 'stock_actual' && request('direccion') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-gray-700">
                                Stock Actual ↕
                            </a>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Unidad
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Precio Unitario
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Valor Total
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($productos as $producto)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $producto->codigo }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $producto->nombre }}</div>
                                @if($producto->descripcion)
                                    <div class="text-sm text-gray-500">{{ \Illuminate\Support\Str::limit($producto->descripcion, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $producto->categoria ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold {{ $producto->stock_actual < 100 ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ number_format($producto->stock_actual) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $producto->unidad_medida }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($producto->precio_unitario > 0)
                                        ${{ number_format($producto->precio_unitario, 2) }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    @if($producto->precio_unitario > 0)
                                        ${{ number_format($producto->stock_actual * $producto->precio_unitario, 2) }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($producto->stock_actual < 100)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        ⚠️ Alerta
                                    </span>
                                @elseif($producto->stock_actual < ($producto->stock_minimo ?: 100))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        ⚠️ Bajo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        ✓ OK
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                No se encontraron productos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $productos->links() }}
    </div>
</div>
@endsection

