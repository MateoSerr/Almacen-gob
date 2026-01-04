<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Salida;

class VerificarSalidasBotas extends Command
{
    protected $signature = 'salidas:verificar-botas';
    protected $description = 'Verifica salidas que usan productos relacionados con BOTA';

    public function handle()
    {
        $salidas = Salida::whereHas('producto', function($q) {
            $q->where('nombre', 'like', '%BOTA%');
        })->with('producto')->get();
        
        $this->info('Salidas con productos relacionados con BOTA:');
        $this->newLine();
        
        foreach($salidas as $s) {
            $this->line("Salida ID: {$s->id} - Producto: {$s->producto->nombre} - Cantidad: {$s->cantidad}");
        }
        
        $this->newLine();
        $this->info("Total: {$salidas->count()} salida(s)");
        
        return Command::SUCCESS;
    }
}




