<?php

namespace App\Console\Commands;

use App\Models\Entrada;
use App\Models\Salida;
use App\Models\Producto;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LimpiarInventario extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventario:limpiar {--force : Ejecutar sin confirmación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina todas las entradas y salidas del inventario, revirtiendo el stock de los productos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $entradasCount = Entrada::count();
        $salidasCount = Salida::count();

        if ($entradasCount === 0 && $salidasCount === 0) {
            $this->info('✅ No hay entradas ni salidas para eliminar.');
            return 0;
        }

        $this->info('📊 Estado actual:');
        $this->line("   - Entradas: {$entradasCount}");
        $this->line("   - Salidas: {$salidasCount}");

        if (!$this->option('force')) {
            if (!$this->confirm('⚠️  ¿Estás seguro de que quieres eliminar TODAS las entradas y salidas? Esta acción no se puede deshacer.', false)) {
                $this->info('❌ Operación cancelada.');
                return 0;
            }
        }

        $this->info('');
        $this->info('🔄 Procesando...');

        try {
            // Revertir stock de entradas (disminuir stock)
            $this->info('   Revertiendo stock de entradas...');
            $entradas = Entrada::with('producto')->get();
            foreach ($entradas as $entrada) {
                if ($entrada->producto) {
                    $entrada->producto->decrement('stock_actual', $entrada->cantidad);
                }
            }
            $this->info("   ✓ Stock revertido para {$entradas->count()} entradas");

            // Revertir stock de salidas (aumentar stock)
            $this->info('   Revertiendo stock de salidas...');
            $salidas = Salida::with('producto')->get();
            foreach ($salidas as $salida) {
                if ($salida->producto) {
                    $salida->producto->increment('stock_actual', $salida->cantidad);
                }
            }
            $this->info("   ✓ Stock revertido para {$salidas->count()} salidas");

            // Eliminar todas las entradas
            $this->info('   Eliminando entradas...');
            DB::table('entradas')->delete();
            $this->info("   ✓ {$entradasCount} entradas eliminadas");

            // Eliminar todas las salidas
            $this->info('   Eliminando salidas...');
            DB::table('salidas')->delete();
            $this->info("   ✓ {$salidasCount} salidas eliminadas");

            $this->info('');
            $this->info('✅ ¡Inventario limpiado exitosamente!');
            $this->info('   Todas las entradas y salidas han sido eliminadas.');
            $this->info('   El stock de los productos ha sido revertido.');
            
            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error al limpiar el inventario: ' . $e->getMessage());
            $this->error('   Detalles: ' . $e->getTraceAsString());
            return 1;
        }
    }
}
