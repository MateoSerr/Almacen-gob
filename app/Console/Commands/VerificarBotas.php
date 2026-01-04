<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;

class VerificarBotas extends Command
{
    protected $signature = 'productos:verificar-botas';
    protected $description = 'Verifica productos relacionados con BOTA';

    public function handle()
    {
        $productos = Producto::where('nombre', 'like', '%BOTA%')->get(['id', 'nombre', 'codigo', 'stock_actual', 'es_uniforme']);
        
        $this->info('Productos relacionados con BOTA:');
        foreach($productos as $p) {
            $this->line("ID: {$p->id} - Nombre: {$p->nombre} - Stock: {$p->stock_actual} - Uniforme: " . ($p->es_uniforme ? 'Sí' : 'No'));
        }
        
        return Command::SUCCESS;
    }
}




