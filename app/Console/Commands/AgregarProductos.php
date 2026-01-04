<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;
use Illuminate\Support\Str;

class AgregarProductos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'productos:agregar {--force : Forzar creación aunque el producto ya exista}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Agrega productos específicos al inventario (Botas y Tomate)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Agregando productos al inventario...');
        $this->newLine();

        $productos = [
            [
                'nombre' => 'BOTAS',
                'codigo' => 'BOTAS-001',
                'unidad_medida' => 'Par(es)',
                'categoria' => 'CALZADO DE SEGURIDAD',
                'precio_unitario' => 2500.00,
                'stock_actual' => 0,
                'stock_minimo' => 50,
                'es_uniforme' => true, // Es uniforme/equipo para policías
                'activo' => true,
            ],
            [
                'nombre' => 'TOMATE',
                'codigo' => 'TOMATE-001',
                'unidad_medida' => 'Kilogramo(s)',
                'categoria' => 'ABARROTES Y LATERIA',
                'precio_unitario' => 35.00,
                'stock_actual' => 0,
                'stock_minimo' => 100,
                'es_uniforme' => false, // No es uniforme
                'activo' => true,
            ],
        ];

        $creados = 0;
        $existentes = 0;
        $errores = 0;

        foreach ($productos as $productoData) {
            $nombre = $productoData['nombre'];
            
            // Verificar si ya existe
            $existe = Producto::where('nombre', $nombre)->first();
            
            if ($existe && !$this->option('force')) {
                $this->warn("⚠️  El producto '{$nombre}' ya existe (ID: {$existe->id})");
                $existentes++;
                continue;
            }

            if ($existe && $this->option('force')) {
                $this->info("🔄 Actualizando producto existente: {$nombre}");
                try {
                    $existe->update($productoData);
                    $this->info("   ✓ Producto actualizado correctamente");
                    $creados++;
                } catch (\Exception $e) {
                    $this->error("   ✗ Error al actualizar: " . $e->getMessage());
                    $errores++;
                }
                continue;
            }

            // Generar código único si no se proporcionó o si ya existe
            if (isset($productoData['codigo'])) {
                $codigoBase = $productoData['codigo'];
                $codigo = $codigoBase;
                $contador = 1;
                
                while (Producto::where('codigo', $codigo)->exists()) {
                    $codigo = $codigoBase . '-' . str_pad($contador, 3, '0', STR_PAD_LEFT);
                    $contador++;
                }
                $productoData['codigo'] = $codigo;
            } else {
                // Generar código automático
                $ultimoCodigo = Producto::max('id') ?? 0;
                $productoData['codigo'] = 'PROD-' . str_pad($ultimoCodigo + 1, 5, '0', STR_PAD_LEFT);
            }

            try {
                $producto = Producto::create($productoData);
                $this->info("✓ Producto creado: {$nombre} (Código: {$producto->codigo})");
                if ($productoData['es_uniforme']) {
                    $this->line("   → Marcado como uniforme/equipo para policías");
                }
                $creados++;
            } catch (\Exception $e) {
                $this->error("✗ Error al crear '{$nombre}': " . $e->getMessage());
                $errores++;
            }
        }

        $this->newLine();
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("📊 Resumen:");
        $this->info("   ✓ Creados/Actualizados: {$creados}");
        if ($existentes > 0) {
            $this->warn("   ⚠️  Existentes (omitidos): {$existentes}");
            $this->line("   💡 Usa --force para actualizar productos existentes");
        }
        if ($errores > 0) {
            $this->error("   ✗ Errores: {$errores}");
        }
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

        return Command::SUCCESS;
    }
}
