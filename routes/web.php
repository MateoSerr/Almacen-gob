<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\EntradaController;
use App\Http\Controllers\SalidaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\OficioEntradaController;
use App\Http\Controllers\SalidaPoliciaController;
use App\Http\Controllers\PoliciaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('productos.index');
});

/* ------------------------------ Productos ------------------------------ */
Route::resource('productos', ProductoController::class);
Route::get('inventario', [ProductoController::class, 'inventario'])->name('inventario.index');
Route::get('api/productos/search', [ProductoController::class, 'search'])->name('api.productos.search');
Route::post('api/productos/quick-create', [ProductoController::class, 'quickCreate'])->name('api.productos.quick-create');

/* ------------------------------ Entradas ------------------------------- */
Route::resource('entradas', EntradaController::class);
Route::get('entradas/{entrada}/imprimir', [EntradaController::class, 'imprimir'])
    ->name('entradas.imprimir');

/* ------------------------------- Salidas ------------------------------- */
Route::resource('salidas', SalidaController::class);
Route::get('salidas/{salida}/imprimir', [SalidaController::class, 'imprimir'])
    ->name('salidas.imprimir');
Route::get('salidas/{salida}/descargar-word', [SalidaController::class, 'descargarWord'])
    ->name('salidas.descargar-word');

/* --------------------------- Oficios de Entrada ------------------------- */
Route::resource('oficios-entrada', OficioEntradaController::class)->parameters([
    'oficios-entrada' => 'oficioEntrada'
]);

Route::get('oficios-entrada/{oficioEntrada}/imprimir',
    [OficioEntradaController::class, 'imprimir'])
    ->name('oficios-entrada.imprimir');

Route::get('oficios-entrada/{oficioEntrada}/descargar-word',
    [OficioEntradaController::class, 'descargarWord'])
    ->name('oficios-entrada.descargar-word');

/* ------------------------------- Reportes ------------------------------- */
Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
Route::get('reportes/kardex', [ReporteController::class, 'kardex'])->name('reportes.kardex');
Route::get('reportes/kardex/excel', [ReporteController::class, 'exportarKardexExcel'])->name('reportes.kardex.excel');

/* --------------------------- Salidas a Policías ------------------------- */
Route::resource('salidas-policia', SalidaPoliciaController::class);
Route::get('salidas-policia/{salida}/imprimir', [SalidaPoliciaController::class, 'imprimir'])
    ->name('salidas-policia.imprimir');

/* ------------------------------- Policías ------------------------------- */
Route::resource('policias', PoliciaController::class);
Route::get('api/policias/search', [PoliciaController::class, 'search'])->name('api.policias.search');
Route::get('api/policias/verificar-entregas', [PoliciaController::class, 'verificarEntregas'])->name('api.policias.verificar-entregas');
Route::post('api/policias/quick-create', [PoliciaController::class, 'quickCreate'])->name('api.policias.quick-create');
