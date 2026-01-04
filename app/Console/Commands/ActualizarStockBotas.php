<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;

class ActualizarStockBotas extends Command
{
    protected $signature = 'productos:actualizar-stock-botas {stock=100 : Cantidad de stock a asignar}';
    protected $description = 'Actualiza el stock del producto BOTAS';

    public function handle()
    {
        $stock = (int) $this->argument('stock');
        
        $botas = Producto::where('nombre', 'BOTAS')->first();
        
        if (!$botas) {
            $this->error('❌ El producto BOTAS no existe en la base de datos.');
            return Command::FAILURE;
        }
        
        $stockAnterior = $botas->stock_actual;
        $botas->stock_actual = $stock;
        $botas->save();
        
        $this->info("✅ Stock de BOTAS actualizado exitosamente!");
        $this->line("   Stock anterior: {$stockAnterior}");
        $this->line("   Stock nuevo: {$stock}");
        
        return Command::SUCCESS;
    }
}




