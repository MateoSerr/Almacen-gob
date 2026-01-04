<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;
use App\Models\Salida;
use Illuminate\Support\Facades\DB;

class MigrarBotasATactico extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'productos:migrar-botas-tactico';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migra todas las salidas de BOTAS/BOTA a BOTA TIPO TACTICO y elimina el producto BOTAS';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Migrando salidas de BOTAS a BOTA TIPO TACTICO...');
        $this->newLine();

        // Buscar productos BOTAS o BOTA
        $productosBotas = Producto::whereIn('nombre', ['BOTAS', 'BOTA'])
            ->where('nombre', '!=', 'BOTA TIPO TACTICO')
            ->get();

        if ($productosBotas->isEmpty()) {
            $this->info('✅ No se encontraron productos "BOTAS" o "BOTA" para migrar.');
            return Command::SUCCESS;
        }

        // Buscar BOTA TIPO TACTICO
        $botaTactico = Producto::where('nombre', 'BOTA TIPO TACTICO')->first();

        if (!$botaTactico) {
            $this->error('❌ No se encontró el producto "BOTA TIPO TACTICO" en la base de datos.');
            $this->warn('   Por favor, asegúrate de que el producto existe antes de continuar.');
            return Command::FAILURE;
        }

        $this->info("✓ Producto destino encontrado: BOTA TIPO TACTICO (ID: {$botaTactico->id})");
        $this->newLine();

        DB::beginTransaction();
        try {
            $totalSalidas = 0;
            $totalStockMigrado = 0;

            foreach ($productosBotas as $productoBota) {
                $this->info("📦 Procesando producto: {$productoBota->nombre} (ID: {$productoBota->id})");

                // Buscar todas las salidas que usan este producto
                $salidas = Salida::where('producto_id', $productoBota->id)->get();

                if ($salidas->isEmpty()) {
                    $this->line("   → No hay salidas registradas para este producto.");
                } else {
                    $this->line("   → Encontradas {$salidas->count()} salida(s) para migrar.");

                    foreach ($salidas as $salida) {
                        // Actualizar la salida para usar BOTA TIPO TACTICO
                        $salida->producto_id = $botaTactico->id;
                        $salida->save();
                        $totalSalidas++;
                        $this->line("      ✓ Salida ID {$salida->id} migrada correctamente");
                    }
                }

                // Sumar el stock del producto BOTAS al stock de BOTA TIPO TACTICO
                if ($productoBota->stock_actual > 0) {
                    $stockAnterior = $botaTactico->stock_actual;
                    $botaTactico->stock_actual += $productoBota->stock_actual;
                    $botaTactico->save();
                    $totalStockMigrado += $productoBota->stock_actual;
                    $this->line("   → Stock migrado: {$productoBota->stock_actual} unidades");
                    $this->line("      Stock anterior BOTA TIPO TACTICO: {$stockAnterior}");
                    $this->line("      Stock nuevo BOTA TIPO TACTICO: {$botaTactico->stock_actual}");
                }

                // Eliminar el producto BOTAS/BOTA
                $productoBota->delete();
                $this->line("   ✓ Producto '{$productoBota->nombre}' eliminado correctamente");
                $this->newLine();
            }

            DB::commit();

            $this->newLine();
            $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->info("✅ Migración completada exitosamente:");
            $this->info("   • Salidas migradas: {$totalSalidas}");
            $this->info("   • Stock migrado: {$totalStockMigrado} unidades");
            $this->info("   • Productos eliminados: {$productosBotas->count()}");
            $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

            // Asegurar que BOTA TIPO TACTICO esté marcado como uniforme
            $botaTactico->es_uniforme = true;
            $botaTactico->save();
            $this->info("✓ BOTA TIPO TACTICO marcado como uniforme/equipo para policías");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Error durante la migración: " . $e->getMessage());
            $this->error("   La transacción ha sido revertida. No se realizaron cambios.");
            return Command::FAILURE;
        }
    }
}




