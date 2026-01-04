<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Policia;
use App\Models\Salida;
use Illuminate\Support\Facades\DB;

class BorrarTodosPolicias extends Command
{
    protected $signature = 'policias:borrar-todos {--force : Forzar eliminación sin confirmación}';
    protected $description = 'Borra todos los registros de policías de la base de datos';

    public function handle()
    {
        $total = Policia::count();
        
        if ($total === 0) {
            $this->info('✅ No hay policías registrados en la base de datos.');
            return Command::SUCCESS;
        }

        if (!$this->option('force')) {
            $this->warn("⚠️  ADVERTENCIA: Se eliminarán {$total} policía(s) de la base de datos.");
            $this->warn("   Esto también eliminará todas las entregas asociadas a estos policías.");
            
            if (!$this->confirm('¿Estás seguro de que deseas continuar?', true)) {
                $this->info('Operación cancelada.');
                return Command::SUCCESS;
            }
        }

        // Desactivar temporalmente las claves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        try {
            // Primero eliminar las salidas asociadas
            $salidasCount = Salida::where('es_entrega_policia', true)->count();
            Salida::where('es_entrega_policia', true)->delete();
            
            // Luego eliminar los policías
            Policia::truncate();
            
            $this->info("✅ Eliminación completada:");
            $this->line("   - {$total} policía(s) eliminado(s)");
            $this->line("   - {$salidasCount} entrega(s) eliminada(s)");
        } finally {
            // Reactivar las claves foráneas
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $this->info("\n🎉 Base de datos de policías limpiada. Puedes empezar de cero.");
        
        return Command::SUCCESS;
    }
}

