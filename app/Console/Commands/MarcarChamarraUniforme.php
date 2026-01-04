<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;

class MarcarChamarraUniforme extends Command
{
    protected $signature = 'productos:marcar-chamarra-uniforme';
    protected $description = 'Marca CHAMARRA ROMPEVIENTOS como uniforme';

    public function handle()
    {
        $chamarra = Producto::where('nombre', 'like', '%CHAMARRA%ROMPEVIENTO%')->first();
        
        if (!$chamarra) {
            $this->error('❌ No se encontró CHAMARRA ROMPEVIENTOS');
            return Command::FAILURE;
        }
        
        $chamarra->es_uniforme = true;
        $chamarra->save();
        
        $this->info("✅ CHAMARRA ROMPEVIENTOS marcada como uniforme (ID: {$chamarra->id})");
        
        return Command::SUCCESS;
    }
}




