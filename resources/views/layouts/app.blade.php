<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema de Inventario') - Almacén Fiscalía</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('productos.index') }}" class="text-2xl font-bold text-blue-600">
                            Almacén Fiscalía
                        </a>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        @php
                            $currentRoute = Route::currentRouteName();
                            $isInventario = str_starts_with($currentRoute, 'inventario.');
                            $isProductos = str_starts_with($currentRoute, 'productos.');
                            $isEntradas = str_starts_with($currentRoute, 'entradas.');
                            $isSalidas = str_starts_with($currentRoute, 'salidas.') && !str_starts_with($currentRoute, 'salidas-policia.');
                            $isSalidasPolicia = str_starts_with($currentRoute, 'salidas-policia.');
                            $isOficios = str_starts_with($currentRoute, 'oficios-entrada.');
                            $isReportes = str_starts_with($currentRoute, 'reportes.');
                        @endphp
                        <a href="{{ route('inventario.index') }}" class="{{ $isInventario ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Inventario
                        </a>
                        <a href="{{ route('productos.index') }}" class="{{ $isProductos ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Productos
                        </a>
                        <a href="{{ route('entradas.index') }}" class="{{ $isEntradas ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Entradas
                        </a>
                        <a href="{{ route('salidas.index') }}" class="{{ $isSalidas ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Salidas
                        </a>
                        <a href="{{ route('salidas-policia.index') }}" class="{{ $isSalidasPolicia ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Entregas Policías
                        </a>
                        <a href="{{ route('oficios-entrada.index') }}" class="{{ $isOficios ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Oficios de Entrada
                        </a>
                        <a href="{{ route('reportes.index') }}" class="{{ $isReportes ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Reportes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>

