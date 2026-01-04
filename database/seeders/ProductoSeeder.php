<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use Illuminate\Support\Str;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar productos existentes si es necesario (opcional)
        // Producto::truncate();
        
        // Array completo de productos del inventario
        $productosData = $this->getProductosData();
        
        $contador = 1;
        $insertados = 0;
        
        // Productos que son uniformes/equipo para policías
        $productosUniforme = [
            'BOTA TIPO TACTICO',
            'CHAMARRA ROMPEVIENTOS',
            'PANTALON TACTICO',
            'PLAYERA TIPO POLO',
        ];
        
        foreach ($productosData as $producto) {
            // Generar código único
            $codigo = 'PROD-' . str_pad($contador, 5, '0', STR_PAD_LEFT);
            
            // Determinar si es uniforme
            $esUniforme = in_array($producto['nombre'], $productosUniforme);
            
            // Verificar si el producto ya existe por nombre
            $productoExistente = Producto::where('nombre', $producto['nombre'])->first();
            
            if (!$productoExistente) {
                try {
                    Producto::create([
                        'codigo' => $codigo,
                        'nombre' => $producto['nombre'],
                        'unidad_medida' => $producto['unidad_medida'],
                        'precio_unitario' => $producto['precio_unitario'],
                        'stock_actual' => (int)$producto['stock_actual'],
                        'stock_minimo' => isset($producto['stock_minimo']) ? $producto['stock_minimo'] : 100,
                        'categoria' => $producto['categoria'],
                        'activo' => true,
                        'es_uniforme' => $esUniforme,
                    ]);
                    $insertados++;
                } catch (\Exception $e) {
                    $this->command->warn("Error al insertar producto {$producto['nombre']}: " . $e->getMessage());
                }
            } else {
                // Si el producto ya existe, actualizar es_uniforme si es necesario
                if ($esUniforme && !$productoExistente->es_uniforme) {
                    $productoExistente->es_uniforme = true;
                    $productoExistente->save();
                    $this->command->info("✓ Producto {$producto['nombre']} marcado como uniforme");
                }
            }
            $contador++;
        }

        $this->command->info("Se cargaron {$insertados} productos exitosamente!");
    }

    private function getProductosData(): array
    {
        return [
            // ========== ABARROTES Y LATERIA ==========
            ['nombre' => 'AGUA PURFICADA', 'unidad_medida' => 'Frasco(s)', 'stock_actual' => 5428, 'precio_unitario' => 2.89, 'categoria' => 'ABARROTES Y LATERIA'],
            ['nombre' => 'AZUCAR PARA SERVIDORES PUBLICOS', 'unidad_medida' => 'Kilogramo(s)', 'stock_actual' => 339, 'precio_unitario' => 80.00, 'categoria' => 'ABARROTES Y LATERIA'],
            ['nombre' => 'CAFE SOLUBLE CLASICO 200 GRS', 'unidad_medida' => 'Frasco(s)', 'stock_actual' => 438, 'precio_unitario' => 107.60, 'categoria' => 'ABARROTES Y LATERIA'],
            ['nombre' => 'CHAROLA DESECHABLE PLATO UNICEL NO. 855 C/50 PZAS.', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 47, 'precio_unitario' => 36.00, 'categoria' => 'ABARROTES Y LATERIA'],
            ['nombre' => 'CUCHARA DESECHABLE CAFETERA', 'unidad_medida' => 'Paquete(s)', 'stock_actual' => 735, 'precio_unitario' => 50.00, 'categoria' => 'ABARROTES Y LATERIA'],
            ['nombre' => 'CUCHARA DESECHABLE SOPERA', 'unidad_medida' => 'Paquete(s)', 'stock_actual' => 709, 'precio_unitario' => 45.00, 'categoria' => 'ABARROTES Y LATERIA'],
            ['nombre' => 'PLASTICO PARA ENVOLTURA DE ALIMENTOS VITA FILM', 'unidad_medida' => 'Rollo(s)', 'stock_actual' => 19, 'precio_unitario' => 428.31, 'categoria' => 'ABARROTES Y LATERIA'],
            ['nombre' => 'PLATO DESECHABLE DE HIELO SECO POZOLERO NO. PH-6 EN PAQUETE CON 25 PZ', 'unidad_medida' => 'Paquete(s)', 'stock_actual' => 1432, 'precio_unitario' => 31.90, 'categoria' => 'ABARROTES Y LATERIA'],
            ['nombre' => 'SERVILLETA DESECHABLE PAQUETE CON 220 PIEZAS', 'unidad_medida' => 'Paquete(s)', 'stock_actual' => 350, 'precio_unitario' => 24.67, 'categoria' => 'ABARROTES Y LATERIA'],
            ['nombre' => 'VASO CONICO PAPEL LISO NO. 104 CON 250 PIEZAS', 'unidad_medida' => 'Paquete(s)', 'stock_actual' => 4934, 'precio_unitario' => 27.39, 'categoria' => 'ABARROTES Y LATERIA'],
            ['nombre' => 'VASO DESECHABLE PLASTICO NO. 12', 'unidad_medida' => 'Paquete(s)', 'stock_actual' => 533, 'precio_unitario' => 34.80, 'categoria' => 'ABARROTES Y LATERIA'],
            ['nombre' => 'VASO DESECHABLE PLASTICO NO. 16 CON 25 PIEZAS', 'unidad_medida' => 'Paquete(s)', 'stock_actual' => 1882, 'precio_unitario' => 27.49, 'categoria' => 'ABARROTES Y LATERIA'],
            ['nombre' => 'VASO DESECHABLE TERMICO', 'unidad_medida' => 'Paquete(s)', 'stock_actual' => 414, 'precio_unitario' => 23.28, 'categoria' => 'ABARROTES Y LATERIA'],
            
            // ========== LUBRICANTES ==========
            ['nombre' => 'ACEITE PARA MOTOR 15W40 MULTIGRADO', 'unidad_medida' => 'Liter(s)', 'stock_actual' => 26000, 'precio_unitario' => 105.00, 'categoria' => 'LUBRICANTES'],
            ['nombre' => 'ACEITE PARA MOTOR 20W50 MULTIGRADO', 'unidad_medida' => 'Liter(s)', 'stock_actual' => 12064, 'precio_unitario' => 105.00, 'categoria' => 'LUBRICANTES'],
            ['nombre' => 'ACEITE PARA MOTOR 5W40 FULLY SYNTHETIC', 'unidad_medida' => 'Liter(s)', 'stock_actual' => 9360, 'precio_unitario' => 158.00, 'categoria' => 'LUBRICANTES'],
            ['nombre' => 'ACEITE PARA MOTOR DIESEL 15W40 MULTIGRADO API', 'unidad_medida' => 'Liter(s)', 'stock_actual' => 570, 'precio_unitario' => 109.00, 'categoria' => 'LUBRICANTES'],
            ['nombre' => 'ACEITE PARA TRANSMISION AUTOMATICA DEXTRON 3', 'unidad_medida' => 'Liter(s)', 'stock_actual' => 1664, 'precio_unitario' => 117.00, 'categoria' => 'LUBRICANTES'],
            ['nombre' => 'ACEITE PARA TRANSMISION ESTANDAR 140', 'unidad_medida' => 'Liter(s)', 'stock_actual' => 832, 'precio_unitario' => 170.00, 'categoria' => 'LUBRICANTES'],
            
            // ========== ADITIVOS ==========
            ['nombre' => 'ANTICONGELANTE', 'unidad_medida' => 'Liter(s)', 'stock_actual' => 832, 'precio_unitario' => 44.95, 'categoria' => 'ADITIVOS'],
            
            // ========== LIQUIDOS DE FRENOS ==========
            ['nombre' => 'LIQUIDO PARA FRENOS', 'unidad_medida' => 'Frasco(s)', 'stock_actual' => 370, 'precio_unitario' => 129.00, 'categoria' => 'LIQUIDOS DE FRENOS'],
            
            // ========== CONSUMIBLES PARA EQUIPO DE COMPUTO ==========
            ['nombre' => 'CARTUCHO DE TINTA PARA IMPRESORA HP C 4911 A', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 6, 'precio_unitario' => 899.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'CARTUCHO DE TINTA PARA IMPRESORA HP C4810A', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 9, 'precio_unitario' => 2041.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'CARTUCHO DE TINTA PARA IMPRESORA HP C4811A', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 6, 'precio_unitario' => 1789.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'CARTUCHO DE TINTA PARA IMPRESORA HP C4812A', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 2, 'precio_unitario' => 1999.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'CARTUCHO DE TINTA PARA IMPRESORA HP C4813A', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 9, 'precio_unitario' => 1800.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'CARTUCHO DE TINTA PARA IMPRESORA HP C4838A', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 6, 'precio_unitario' => 586.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'CARTUCHO DE TINTA PARA PLOTTER HP 728 AMARILLO F9K15A DE 300 ML.', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 3, 'precio_unitario' => 5510.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'CARTUCHO DE TINTA PARA PLOTTER HP C9370A', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 15, 'precio_unitario' => 1920.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'CARTUCHO DE TINTA PARA PLOTTER HP C9371A', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 23, 'precio_unitario' => 1860.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'CARTUCHO DE TINTA PARA PLOTTER HP C9372A', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 23, 'precio_unitario' => 1860.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'CARTUCHO DE TINTA PARA PLOTTER HP C9373A', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 8, 'precio_unitario' => 1860.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'CARTUCHO DE TINTA PARA PLOTTER HP C9374A', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 23, 'precio_unitario' => 1860.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'CARTUCHO DE TINTA PARA PLOTTER HP C9380A', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 15, 'precio_unitario' => 2060.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'CARTUCHO DE TINTA PARA PLOTTER HP C9383A', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 15, 'precio_unitario' => 2060.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'CARTUCHO DE TINTA PARA PLOTTER HP C9384A', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 15, 'precio_unitario' => 1920.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'CARTUCHO DE TINTA PARA PLOTTER HP C9403A', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 30, 'precio_unitario' => 1800.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'DEPOSITO DE DESPERDICIOS', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 6, 'precio_unitario' => 950.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'DEPOSITO DE DESPERDICIOS PARA TONER LEXMARK 74C0W00', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 21, 'precio_unitario' => 213.79, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'DISCO VIRGEN DVD RW 4.7 ( DISCO )', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 2500, 'precio_unitario' => 19.69, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'ESPUMA LIMPIADORA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 50, 'precio_unitario' => 130.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'KIT DE MANTENIMIENTO PARA IMPRESORA HP CF064A', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 2, 'precio_unitario' => 7485.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'KIT DE MANTENIMIENTO PARA IMPRESORA HP LASERJET P4515 CB388A', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 4, 'precio_unitario' => 3999.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'KIT FOTOCONDUCTOR IMPRESORA LEXMARK C734X20G', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 8, 'precio_unitario' => 3587.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'PAPEL PARA PLOTTER BOND .61 X 50 MTS', 'unidad_medida' => 'Rollo(s)', 'stock_actual' => 10, 'precio_unitario' => 0.70, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'TONER PARA IMPRESORA HP CE401A CYAN', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 7, 'precio_unitario' => 4722.20, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'TONER PARA IMPRESORA HP CE402A AMARILLO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 7, 'precio_unitario' => 4722.20, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'TONER PARA IMPRESORA HP CE403A MAGENTA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 6, 'precio_unitario' => 4722.20, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'TONER PARA IMPRESORA HP LASER JET Q2670A', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 7, 'precio_unitario' => 2571.01, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'TONER PARA IMPRESORA HP OFFICEJET 200 C2P05AL', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 100, 'precio_unitario' => 840.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'TONER PARA IMPRESORA HP OFFICEJET 200 C2P07AL', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 100, 'precio_unitario' => 870.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'TONER PARA IMPRESORA LEXMARK 56F4X00', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1750, 'precio_unitario' => 1854.84, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'TONER PARA IMPRESORA LEXMARK 74C 4SCO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 66, 'precio_unitario' => 4670.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'TONER PARA IMPRESORA LEXMARK 74C 4SKO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 91, 'precio_unitario' => 4365.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'TONER PARA IMPRESORA LEXMARK 74C 4SMO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 66, 'precio_unitario' => 4670.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'TONER PARA IMPRESORA LEXMARK 74C4SY0', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 66, 'precio_unitario' => 4670.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'TONER PARA IMPRESORA LEXMARK AMARILLO 76C0HY0', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 5, 'precio_unitario' => 16400.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'TONER PARA IMPRESORA LEXMARK C746A1CG', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 6, 'precio_unitario' => 5899.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'TONER PARA IMPRESORA LEXMARK C746A1MG', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 7, 'precio_unitario' => 5899.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'TONER PARA IMPRESORA LEXMARK C746A1YG', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 7, 'precio_unitario' => 5949.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'TONER PARA IMPRESORA LEXMARK C746H1KG', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 8, 'precio_unitario' => 4409.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'TONER PARA IMPRESORA LEXMARK CIAN 76C0HC0', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 4, 'precio_unitario' => 16400.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'TONER PARA IMPRESORA LEXMARK MAGENTA 76C0HM0', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 5, 'precio_unitario' => 16400.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'TONER PARA IMPRESORA LEXMARK NEGRO 86C0HK0', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 8, 'precio_unitario' => 5455.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'UNIDAD DE IMAGEN PARA IMPRESORA LEXMARK 520ZA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 5, 'precio_unitario' => 1397.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'UNIDAD DE IMAGEN PARA IMPRESORA LEXMARK 56F0Z00', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1635, 'precio_unitario' => 790.70, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'UNIDAD DE IMAGEN PARA IMPRESORA LEXMARK 74C0ZK0', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 23, 'precio_unitario' => 2487.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'UNIDAD DE IMAGEN PARA IMPRESORA LEXMARK 74C0ZV0', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 24, 'precio_unitario' => 8256.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'UNIDAD DE IMAGEN PARA IMPRESORA LEXMARK 76C0PK0', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 22, 'precio_unitario' => 3500.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'UNIDAD DE IMAGEN PARA IMPRESORA LEXMARK 76C0PV0', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 16, 'precio_unitario' => 11340.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'WASTE TONER BOX LEXMARK C734X779', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 21, 'precio_unitario' => 560.00, 'categoria' => 'CONSUMIBLES PARA EQUIPO DE COMPUTO'],
            
            // ========== EQUIPO DE COMPUTO ==========
            ['nombre' => 'COMPUTADORA LAP TOP', 'unidad_medida' => 'Equipo(s)', 'stock_actual' => 15, 'precio_unitario' => 31783.00, 'categoria' => 'EQUIPO DE COMPUTO'],
            ['nombre' => 'COMPUTADORA PC', 'unidad_medida' => 'Equipo(s)', 'stock_actual' => 51, 'precio_unitario' => 26487.00, 'categoria' => 'EQUIPO DE COMPUTO'],
            ['nombre' => 'DISCO DURO INTERNO 480GB', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 350, 'precio_unitario' => 999.00, 'categoria' => 'EQUIPO DE COMPUTO'],
            ['nombre' => 'EQUIPO MULTIFUNCIONAL PARA IMPRESORA LEXMARK MX622ADHE', 'unidad_medida' => 'Equipo(s)', 'stock_actual' => 40, 'precio_unitario' => 1.00, 'categoria' => 'EQUIPO DE COMPUTO'],
            ['nombre' => 'IMPRESORA LASSER MOD. MS621DN', 'unidad_medida' => 'Equipo(s)', 'stock_actual' => 50, 'precio_unitario' => 1.00, 'categoria' => 'EQUIPO DE COMPUTO'],
            ['nombre' => 'KIT DE TECLADO Y MOUSE', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 800, 'precio_unitario' => 498.00, 'categoria' => 'EQUIPO DE COMPUTO'],
            
            // ========== REFACCIONES PARA EQUIPO DE COMPUTO ==========
            ['nombre' => 'MEMORIA USB 3.0 DE 64 GB', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 3000, 'precio_unitario' => 110.27, 'categoria' => 'REFACCIONES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'MEMORIA USB DE 128 GB', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1999, 'precio_unitario' => 189.10, 'categoria' => 'REFACCIONES PARA EQUIPO DE COMPUTO'],
            ['nombre' => 'MEMORIA USB DE 256 GB.', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 2000, 'precio_unitario' => 461.00, 'categoria' => 'REFACCIONES PARA EQUIPO DE COMPUTO'],
            
            // ========== DETECTORES DE METALES ==========
            ['nombre' => 'ARCO DETECTOR DE METALES', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 3, 'precio_unitario' => 23410.54, 'categoria' => 'DETECTORES DE METALES'],
            
            // ========== EQUIPO DE CONTROL Y VIGILANCIAS ==========
            ['nombre' => 'EQUIPO ESPECIAL PARA FRANCOTIRADOR ACCESORIO BIKINIS PARA BINOCULARES', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 9, 'precio_unitario' => 319.00, 'categoria' => 'EQUIPO DE CONTROL Y VIGILANCIAS'],
            ['nombre' => 'EQUIPO ESPECIAL PARA FRANCOTIRADOR ACCESORIO ESTUCHE RIGIDO PARA 1 ARMA LARGA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 6, 'precio_unitario' => 5520.98, 'categoria' => 'EQUIPO DE CONTROL Y VIGILANCIAS'],
            
            // ========== ESCUDOS TOLETES Y PARALIZADORES ==========
            ['nombre' => 'CARETA PROTECTORA FACIAL', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 20, 'precio_unitario' => 1711.21, 'categoria' => 'ESCUDOS TOLETES Y PARALIZADORES'],
            ['nombre' => 'CODERA', 'unidad_medida' => 'Par(es)', 'stock_actual' => 1341, 'precio_unitario' => 468.00, 'categoria' => 'ESCUDOS TOLETES Y PARALIZADORES'],
            ['nombre' => 'RODILLERA', 'unidad_medida' => 'Par(es)', 'stock_actual' => 1341, 'precio_unitario' => 468.00, 'categoria' => 'ESCUDOS TOLETES Y PARALIZADORES'],
            
            // ========== ESPOSAS ==========
            ['nombre' => 'CANDADO DE MANO DE NYLON PARA APREHENSION', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1354, 'precio_unitario' => 643.00, 'categoria' => 'ESPOSAS'],
            
            // ========== ROPA ANTIBALAS ==========
            ['nombre' => 'CASCO BALISTICO ANTIBALAS CON COBERTURA DE OREJA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 200, 'precio_unitario' => 10700.00, 'categoria' => 'ROPA ANTIBALAS'],
            ['nombre' => 'CASCO BALISTICO ANTIBALAS SIN COBERTURA DE OREJA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 150, 'precio_unitario' => 10060.00, 'categoria' => 'ROPA ANTIBALAS'],
            
            // ========== MOBILIARIO DE COCINA ==========
            ['nombre' => 'TARJA DE ACERO INOXIDABLE SENCILLA CON ESCURRIDOR DERECHO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 2, 'precio_unitario' => 969.35, 'categoria' => 'MOBILIARIO DE COCINA'],
            ['nombre' => 'TARJA DE ACERO INOXIDABLE SENCILLA CON ESCURRIDOR IZQUIERDO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 2, 'precio_unitario' => 919.00, 'categoria' => 'MOBILIARIO DE COCINA'],
            
            // ========== ACEROS ==========
            ['nombre' => 'ALAMBRE DE ACERO CALIBRE 12', 'unidad_medida' => 'Rollo(s)', 'stock_actual' => 1, 'precio_unitario' => 158.92, 'categoria' => 'ACEROS'],
            ['nombre' => 'ALAMBRE GALVANIZADO CALIBRE 14', 'unidad_medida' => 'Rollo(s)', 'stock_actual' => 13, 'precio_unitario' => 46.48, 'categoria' => 'ACEROS'],
            ['nombre' => 'ANGULO CON CLAVO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 300, 'precio_unitario' => 3.34, 'categoria' => 'ACEROS'],
            ['nombre' => 'ANGULO DE ACERO AL CARBON 1/8" X 1 1/2"', 'unidad_medida' => 'Tramo(s)', 'stock_actual' => 10, 'precio_unitario' => 313.20, 'categoria' => 'ACEROS'],
            ['nombre' => 'ANGULO DE ACERO AL CARBON TRAMO 1/8" X 1"', 'unidad_medida' => 'Tramo(s)', 'stock_actual' => 10, 'precio_unitario' => 422.00, 'categoria' => 'ACEROS'],
            ['nombre' => 'ANGULO PARA TABLAROCA', 'unidad_medida' => 'Tramo(s)', 'stock_actual' => 50, 'precio_unitario' => 49.50, 'categoria' => 'ACEROS'],
            ['nombre' => 'MALLA ELECTROSOLDADA DE ACERO INOXIDABLE', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 2, 'precio_unitario' => 890.00, 'categoria' => 'ACEROS'],
            ['nombre' => 'PTR 1 1/2 PULGADA CALIBRE 12', 'unidad_medida' => 'Tramo(s)', 'stock_actual' => 7, 'precio_unitario' => 605.50, 'categoria' => 'ACEROS'],
            ['nombre' => 'SOLERA FIERRO 1 X 1/8', 'unidad_medida' => 'Tramo(s)', 'stock_actual' => 10, 'precio_unitario' => 258.04, 'categoria' => 'ACEROS'],
            ['nombre' => 'SOLERA FIERRO 1/8" X 1 1/2"', 'unidad_medida' => 'Tramo(s)', 'stock_actual' => 10, 'precio_unitario' => 172.38, 'categoria' => 'ACEROS'],
            
            // ========== MADERA ==========
            ['nombre' => 'POLIN MADERA', 'unidad_medida' => 'Tramo(s)', 'stock_actual' => 7, 'precio_unitario' => 435.00, 'categoria' => 'MADERA'],
            ['nombre' => 'PUERTA DE MADERA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 2, 'precio_unitario' => 1429.00, 'categoria' => 'MADERA'],
            
            // ========== MATERIALES PARA CONSTRUCCION ==========
            ['nombre' => 'CANAL PARA TABLAROCA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 164, 'precio_unitario' => 294.64, 'categoria' => 'MATERIALES PARA CONSTRUCCION'],
            ['nombre' => 'CINTA PARA TABLAROCA', 'unidad_medida' => 'Rollo(s)', 'stock_actual' => 47, 'precio_unitario' => 94.83, 'categoria' => 'MATERIALES PARA CONSTRUCCION'],
            ['nombre' => 'COMPUESTO PARA TABLAROCA', 'unidad_medida' => 'Kilogramo(s)', 'stock_actual' => 130.80, 'precio_unitario' => 17.43, 'categoria' => 'MATERIALES PARA CONSTRUCCION'],
            ['nombre' => 'JUNTEADOR CON SELLADOR INTEGRADO GRIS', 'unidad_medida' => 'Kilogramo(s)', 'stock_actual' => 65, 'precio_unitario' => 1.00, 'categoria' => 'MATERIALES PARA CONSTRUCCION'],
            ['nombre' => 'LADRILLO DE AZOTEA MEDIDA 17 X 17', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1000, 'precio_unitario' => 5.90, 'categoria' => 'MATERIALES PARA CONSTRUCCION'],
            ['nombre' => 'LAVADERO DE CEMENTO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 3, 'precio_unitario' => 397.73, 'categoria' => 'MATERIALES PARA CONSTRUCCION'],
            ['nombre' => 'POSTE PARA TABLAROCA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 367, 'precio_unitario' => 320.16, 'categoria' => 'MATERIALES PARA CONSTRUCCION'],
            ['nombre' => 'YESO', 'unidad_medida' => 'Kilogramo(s)', 'stock_actual' => 480, 'precio_unitario' => 3.56, 'categoria' => 'MATERIALES PARA CONSTRUCCION'],
            
            // ========== ARTICULOS CONSUMIBLES DE LIMPIEZA ==========
            ['nombre' => 'ACEITE PARA MOP MAGNETIZADOR', 'unidad_medida' => 'Liter(s)', 'stock_actual' => 662, 'precio_unitario' => 14.43, 'categoria' => 'ARTICULOS CONSUMIBLES DE LIMPIEZA'],
            ['nombre' => 'AROMATIZANTE AMBIENTAL AEROSOL', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 2385, 'precio_unitario' => 40.00, 'categoria' => 'ARTICULOS CONSUMIBLES DE LIMPIEZA'],
            ['nombre' => 'CLORO AL 6% FRASCO DE 950 ML', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1685, 'precio_unitario' => 18.40, 'categoria' => 'ARTICULOS CONSUMIBLES DE LIMPIEZA'],
            ['nombre' => 'CLORO BLANQUEADOR EN GEL', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 176, 'precio_unitario' => 54.00, 'categoria' => 'ARTICULOS CONSUMIBLES DE LIMPIEZA'],
            ['nombre' => 'CLORO EN PASTILLA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 415, 'precio_unitario' => 2.43, 'categoria' => 'ARTICULOS CONSUMIBLES DE LIMPIEZA'],
            ['nombre' => 'DETERGENTE LIQUIDO LAVATRASTES', 'unidad_medida' => 'Frasco(s)', 'stock_actual' => 317, 'precio_unitario' => 57.50, 'categoria' => 'ARTICULOS CONSUMIBLES DE LIMPIEZA'],
            ['nombre' => 'GEL ANTIBACTERIAL PARA MANOS', 'unidad_medida' => 'Liter(s)', 'stock_actual' => 9961, 'precio_unitario' => 60.00, 'categoria' => 'ARTICULOS CONSUMIBLES DE LIMPIEZA'],
            ['nombre' => 'JABON DE BARRA DE TOCADOR NEUTRO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 6951, 'precio_unitario' => 11.57, 'categoria' => 'ARTICULOS CONSUMIBLES DE LIMPIEZA'],
            ['nombre' => 'JABON GEL LIQUIDO PARA MANOS', 'unidad_medida' => 'Liter(s)', 'stock_actual' => 5, 'precio_unitario' => 15.50, 'categoria' => 'ARTICULOS CONSUMIBLES DE LIMPIEZA'],
            ['nombre' => 'JABON GEL LIQUIDO PARA MANOS EN CARTUCHO DE 500 ML', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 603, 'precio_unitario' => 33.00, 'categoria' => 'ARTICULOS CONSUMIBLES DE LIMPIEZA'],
            ['nombre' => 'LIMPIADOR ABRILLANTADOR PARA MUEBLES EN AEROSOL DE 333 GRS.', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 3066, 'precio_unitario' => 71.05, 'categoria' => 'ARTICULOS CONSUMIBLES DE LIMPIEZA'],
            ['nombre' => 'LIMPIADOR EN POLVO BICLORO', 'unidad_medida' => 'Lata(s)', 'stock_actual' => 2352, 'precio_unitario' => 39.00, 'categoria' => 'ARTICULOS CONSUMIBLES DE LIMPIEZA'],
            ['nombre' => 'LIMPIADOR LIQUIDO AROMA PINO DE 828 ML.', 'unidad_medida' => 'Frasco(s)', 'stock_actual' => 4039, 'precio_unitario' => 34.39, 'categoria' => 'ARTICULOS CONSUMIBLES DE LIMPIEZA'],
            ['nombre' => 'LIMPIADOR LIQUIDO CON AMONIA PRESENTACION EN BOTELLA 1 LT.', 'unidad_medida' => 'Frasco(s)', 'stock_actual' => 425, 'precio_unitario' => 59.00, 'categoria' => 'ARTICULOS CONSUMIBLES DE LIMPIEZA'],
            ['nombre' => 'LIMPIADOR LIQUIDO MULTIUSOS', 'unidad_medida' => 'Liter(s)', 'stock_actual' => 4584, 'precio_unitario' => 24.00, 'categoria' => 'ARTICULOS CONSUMIBLES DE LIMPIEZA'],
            ['nombre' => 'LIMPIADOR LIQUIDO PARA VIDRIOS', 'unidad_medida' => 'Frasco(s)', 'stock_actual' => 2413, 'precio_unitario' => 91.17, 'categoria' => 'ARTICULOS CONSUMIBLES DE LIMPIEZA'],
            ['nombre' => 'LIMPIADOR SARRICIDA', 'unidad_medida' => 'Frasco(s)', 'stock_actual' => 698, 'precio_unitario' => 33.86, 'categoria' => 'ARTICULOS CONSUMIBLES DE LIMPIEZA'],
            ['nombre' => 'LIQUIDO AFLOJATODO', 'unidad_medida' => 'Botella(s)', 'stock_actual' => 183, 'precio_unitario' => 177.14, 'categoria' => 'ARTICULOS CONSUMIBLES DE LIMPIEZA'],
            ['nombre' => 'PAÑO MICROFIBRA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 58, 'precio_unitario' => 12.00, 'categoria' => 'ARTICULOS CONSUMIBLES DE LIMPIEZA'],
            ['nombre' => 'PAPEL HIGIENICO JUMBO JUNIOR', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 40157, 'precio_unitario' => 103.03, 'categoria' => 'ARTICULOS CONSUMIBLES DE LIMPIEZA'],
            ['nombre' => 'PASTILLA DESODORANTE PARA WC', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1225, 'precio_unitario' => 11.45, 'categoria' => 'ARTICULOS CONSUMIBLES DE LIMPIEZA'],
            ['nombre' => 'TOALLA INTERDOBLADA HOJA DOBLE BLANCA', 'unidad_medida' => 'Paquete(s)', 'stock_actual' => 10442, 'precio_unitario' => 12.00, 'categoria' => 'ARTICULOS CONSUMIBLES DE LIMPIEZA'],
            
            // ========== ARTICULOS DE LIMPIEZA P/MANT. INDUSTRIAL ==========
            ['nombre' => 'AFLOJATODO (USO MECANICO)', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 150, 'precio_unitario' => 157.36, 'categoria' => 'ARTICULOS DE LIMPIEZA P/MANT. INDUSTRIAL'],
            ['nombre' => 'CREMA DESENGRASANTE DE MANO', 'unidad_medida' => 'Liter(s)', 'stock_actual' => 100, 'precio_unitario' => 235.74, 'categoria' => 'ARTICULOS DE LIMPIEZA P/MANT. INDUSTRIAL'],
            ['nombre' => 'LAVADOR DE CUERPO DE ACELERACION', 'unidad_medida' => 'Liter(s)', 'stock_actual' => 200, 'precio_unitario' => 167.50, 'categoria' => 'ARTICULOS DE LIMPIEZA P/MANT. INDUSTRIAL'],
            ['nombre' => 'LIQUIDO DESENGRASANTE DE MOTOR', 'unidad_medida' => 'Liter(s)', 'stock_actual' => 100, 'precio_unitario' => 150.00, 'categoria' => 'ARTICULOS DE LIMPIEZA P/MANT. INDUSTRIAL'],
            ['nombre' => 'LIQUIDO LAVADOR DE MOTOR INTERNO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 100, 'precio_unitario' => 233.33, 'categoria' => 'ARTICULOS DE LIMPIEZA P/MANT. INDUSTRIAL'],
            
            // ========== ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA ==========
            ['nombre' => 'ATOMIZADOR REFORZADO CAPACIDAD 1 LT', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 545, 'precio_unitario' => 14.10, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'BASE PARA MOP DE 60 CMS (ARMAZON CON BASTON)', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 70, 'precio_unitario' => 72.00, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'BASE PARA MOP DE 90 CMS (ARMAZON CON BASTON)', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 193, 'precio_unitario' => 96.30, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'BOLSA DE POLIETILENO TRANSPARENTE 15 X 25 CMS', 'unidad_medida' => 'Rollo(s)', 'stock_actual' => 453, 'precio_unitario' => 109.93, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'BOLSA DE POLIETILENO TRANSPARENTE 25 X 35 CMS', 'unidad_medida' => 'Rollo(s)', 'stock_actual' => 1563, 'precio_unitario' => 140.00, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'BOLSA PARA CADAVER NEGRA CON CIERRE', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 20, 'precio_unitario' => 1.60, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'BOLSA POLIETILENO CHICA PARA BASURA DE 50 X 70 CMS', 'unidad_medida' => 'Kilogramo(s)', 'stock_actual' => 3189, 'precio_unitario' => 32.50, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'BOLSA POLIETILENO DE 60 CMS. X 90 CMS.', 'unidad_medida' => 'Kilogramo(s)', 'stock_actual' => 3750, 'precio_unitario' => 32.50, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'BOLSA POLIETILENO MEDIANA DE 90 X 120', 'unidad_medida' => 'Kilogramo(s)', 'stock_actual' => 6698, 'precio_unitario' => 46.42, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'BOMBA PARA WC MANGO DE MADERA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 254, 'precio_unitario' => 25.34, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'BOTA DE HULE ALTA PARA JARDINERO TALLA # 25', 'unidad_medida' => 'Par(es)', 'stock_actual' => 3, 'precio_unitario' => 141.00, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'BOTA DE HULE ALTA PARA JARDINERO TALLA # 26', 'unidad_medida' => 'Par(es)', 'stock_actual' => 3, 'precio_unitario' => 141.00, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'BOTA DE HULE ALTA PARA JARDINERO TALLA # 29', 'unidad_medida' => 'Par(es)', 'stock_actual' => 2, 'precio_unitario' => 549.00, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'BOTE PARA BASURA DE PLASTICO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 122, 'precio_unitario' => 90.00, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'BOTE PARA BASURA DE PLASTICO CON TAPA CAPACIDAD DE 60 LTS', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 20, 'precio_unitario' => 265.00, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'CEPILLO DE PLASTICO PARA WC', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 260, 'precio_unitario' => 27.50, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'CRUCETA O JALADOR DE AGUA CON BASTON DE 1.10', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 34, 'precio_unitario' => 52.10, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'CUBETA PLASTICO CAPACIDAD PARA 16 LTS', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 114, 'precio_unitario' => 95.80, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'ENVASE DE PLASTICO TIPO BOTELLA PET PLASTICO DE 500 ML', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 363, 'precio_unitario' => 50.00, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'ESCOBA DE CERDA PLASTICO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 2102, 'precio_unitario' => 48.78, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'FIBRA METALICA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 635, 'precio_unitario' => 18.82, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'FIBRA VERDE', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1003, 'precio_unitario' => 20.95, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'FIBRA VERDE CON ESPONJA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 953, 'precio_unitario' => 16.51, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'GUANTES AMA DE CASA CORTO', 'unidad_medida' => 'Par(es)', 'stock_actual' => 1413, 'precio_unitario' => 14.00, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'RECOGEDOR DE MEDIO BOTE MANGO DE MADERA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 70, 'precio_unitario' => 31.25, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'RECOGEDOR DE PLASTICO CON BASTON', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 88, 'precio_unitario' => 68.00, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'REPUESTO PARA MOP DE 60 CMS', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 70, 'precio_unitario' => 43.69, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'REPUESTO PARA MOP DE 90 CMS', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 160, 'precio_unitario' => 45.00, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'SACUDIDORES DE PARED CON MANGO LARGO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 2, 'precio_unitario' => 54.00, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'TAPA DE PLASTICO FLIPTOP', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 403, 'precio_unitario' => 1.03, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'TAPETE PARA MINGITORIO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 2081, 'precio_unitario' => 25.00, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'TAPETE PARA MINGITORIO CON PASTILLA DESODORANTE', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 634, 'precio_unitario' => 30.33, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            ['nombre' => 'TRAPEADOR DE HILAZA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 2628, 'precio_unitario' => 66.67, 'categoria' => 'ACCESORIOS UTENSILIOS Y EQ. DE LIMPIEZA'],
            
            // ========== ARTICULOS Y MATERIALES DE ESCRITORIO ==========
            ['nombre' => 'ARILLO PARA ENGARGOLAR METALICO DE 1"', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 78, 'precio_unitario' => 3.56, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'ARILLO PARA ENGARGOLAR METALICO DE 3/4 PULG', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 83, 'precio_unitario' => 2.68, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'ARILLO PARA ENGARGOLAR METALICO DE 5/16', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 3260, 'precio_unitario' => 22.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'ARILLO PARA ENGARGOLAR METALICO DE 5/8 PULG COLOR NEGRO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 2490, 'precio_unitario' => 3.29, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'ARILLO PARA ENGARGOLAR METALICO DE 7/16 PULG', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1955, 'precio_unitario' => 1.54, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'ARILLO PARA ENGARGOLAR METALICO DE 9/16', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 2900, 'precio_unitario' => 1.96, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'ARILLO PARA ENGARGOLAR METALICOS DE 3/8', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 2869, 'precio_unitario' => 4.25, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'ARILLO PARA ENGARGOLAR PLASTICO DE 1 PULG.', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 2, 'precio_unitario' => 1.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'ARILLO PARA ENGARGOLAR PLASTICO DE 1/2 DE PULG.', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 58, 'precio_unitario' => 4.52, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'ARILLO PARA ENGARGOLAR PLASTICO DE 3/4 DE PULG.', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 51, 'precio_unitario' => 22.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'ARILLO PARA ENGARGOLAR PLASTICO DE 3/8 DE PULG.', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1115, 'precio_unitario' => 12.37, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'BLOCK DE NOTAS ADHESIVAS 3 X 3', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 3650, 'precio_unitario' => 9.29, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'BOLIGRAFO DE GEL AZUL', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 995, 'precio_unitario' => 9.25, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'BOLIGRAFO DE GEL NEGRO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1, 'precio_unitario' => 26.15, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'BOLIGRAFO PUNTO MEDIO TINTA AZUL', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 93680, 'precio_unitario' => 3.25, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'BOLIGRAFO PUNTO MEDIO TINTA ROJA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 19358, 'precio_unitario' => 3.25, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'BOLIGRAFO PUNTO MEDIO TINTA VERDE', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 178, 'precio_unitario' => 2.36, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'BORRADOR TIPO WS 20 CUADRADO COLOR BCO.', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 701, 'precio_unitario' => 6.50, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'BROCHE 8 CMS', 'unidad_medida' => 'Caja(s)', 'stock_actual' => 4702, 'precio_unitario' => 33.87, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'CAJA DE CARTON PARA RESGUARDO ARMA BLANCA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 180, 'precio_unitario' => 81.73, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'CAJA DE CARTON PARA RESGUARDO ARMA CORTA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 250, 'precio_unitario' => 82.40, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'CAJA DE CARTON PARA RESGUARDO ARMA LARGA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 240, 'precio_unitario' => 99.90, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'CAJA PARA ARCHIVO MUERTO MIXTA DE CARTON', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 9472, 'precio_unitario' => 47.70, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'CERA PARA CONTAR PAPEL', 'unidad_medida' => 'Tarro(s)', 'stock_actual' => 2498, 'precio_unitario' => 12.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'CHINCHETAS CABEZA METALICA DE COLORES', 'unidad_medida' => 'Caja(s)', 'stock_actual' => 25, 'precio_unitario' => 26.73, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'CINTA ADHESIVA CANELA DE 48 MM', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 2671, 'precio_unitario' => 27.02, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'CINTA ADHESIVA MASKING TAPE DE 24 MM X 50 MTS. 1"', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1083, 'precio_unitario' => 34.75, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'CINTA ADHESIVA TRANSPARENTE DE 24 MM X 65 MTS.', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 5019, 'precio_unitario' => 33.27, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'CLIP MARIPOSA NO. 1', 'unidad_medida' => 'Caja(s)', 'stock_actual' => 1411, 'precio_unitario' => 25.29, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'CLIP MARIPOSA NO. 2', 'unidad_medida' => 'Caja(s)', 'stock_actual' => 1252, 'precio_unitario' => 34.46, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'CLIP NIQUELADO Y/O GALVANIZADO NO. 1', 'unidad_medida' => 'Caja(s)', 'stock_actual' => 3543, 'precio_unitario' => 11.22, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'CLIP NIQUELADO Y/O GALVANIZADO NO. 2', 'unidad_medida' => 'Caja(s)', 'stock_actual' => 1803, 'precio_unitario' => 11.71, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'CLIP NIQUELADO Y/O GALVANIZADO NO. 3', 'unidad_medida' => 'Caja(s)', 'stock_actual' => 3205, 'precio_unitario' => 9.93, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'COJIN PARA SELLO Nº 1', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 114, 'precio_unitario' => 27.29, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'COJIN PARA SELLO Nº 2', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 219, 'precio_unitario' => 49.21, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'CORRECTOR LIQUIDO DE 20 ML', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1975, 'precio_unitario' => 12.80, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'CUADERNO PROFESIONAL RAYA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1753, 'precio_unitario' => 24.45, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'CUADERNO TAQUIGRAFIA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1273, 'precio_unitario' => 25.20, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'CUTTER CHICO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 147, 'precio_unitario' => 9.45, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'CUTTER GRANDE', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 125, 'precio_unitario' => 9.90, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'DESENGRAPADORA TIPO UÑA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 633, 'precio_unitario' => 16.68, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'DESPACHADOR DE CINTA 24 X 65 MM.', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 34, 'precio_unitario' => 99.65, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'DESPACHADOR DE CINTA CANELA 48 MM', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 164, 'precio_unitario' => 36.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'ENGRAPADORA DE GOLPE', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 948, 'precio_unitario' => 162.32, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'ENGRAPADORA DE USO PESADO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 19, 'precio_unitario' => 842.75, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'ETIQUETA ADHESIVA FILE Nº 20', 'unidad_medida' => 'Paquete(s)', 'stock_actual' => 82, 'precio_unitario' => 35.40, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'ETIQUETA ADHESIVA Nº 21', 'unidad_medida' => 'Paquete(s)', 'stock_actual' => 42, 'precio_unitario' => 32.30, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'ETIQUETA ADHESIVA Nº 24', 'unidad_medida' => 'Paquete(s)', 'stock_actual' => 73, 'precio_unitario' => 24.86, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'ETIQUETA ADHESIVA NO. 25 PAQUETE', 'unidad_medida' => 'Paquete(s)', 'stock_actual' => 412, 'precio_unitario' => 32.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'FOLDER CARTA COLOR CREMA PAQUETE CON 100', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 69405, 'precio_unitario' => 1.50, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'FOLDER CARTA CON BROCHE COLOR ROJO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 628, 'precio_unitario' => 9.08, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'FOLDER CARTA CON PALANCA DE PRESION COLOR AZUL OSCURO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 501, 'precio_unitario' => 42.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'FOLDER CARTA CON PALANCA DE PRESION COLOR ROJO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 187, 'precio_unitario' => 20.44, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'FOLDER COLGANTE CARTA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 30, 'precio_unitario' => 26.50, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'FOLDER OFICIO CON BROCHE COLOR AZUL', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 210, 'precio_unitario' => 28.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'FOLDER OFICIO CON PALANCA DE PRESION AZUL OBSCURO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 2273, 'precio_unitario' => 50.10, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'FOLDER OFICIO CREMA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 317700, 'precio_unitario' => 2.80, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'FOLIADOR DE 6 DIGITOS', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 203, 'precio_unitario' => 337.98, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'GRAPAS ESTANDARD', 'unidad_medida' => 'Caja(s)', 'stock_actual' => 3372, 'precio_unitario' => 69.42, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'GRAPAS TRABAJO PESADO', 'unidad_medida' => 'Caja(s)', 'stock_actual' => 45, 'precio_unitario' => 35.15, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'GUIAS ALFABETICA TAMAÑO OFICIO', 'unidad_medida' => 'Juego(s)', 'stock_actual' => 104, 'precio_unitario' => 137.98, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'GUIAS ALFABETICAS TAMAÑO CARTA', 'unidad_medida' => 'Juego(s)', 'stock_actual' => 117, 'precio_unitario' => 116.22, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'LAPIZ ADHESIVO 8 GRS', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 2412, 'precio_unitario' => 9.28, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'LAPIZ BICOLOR', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1314, 'precio_unitario' => 9.48, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'LAPIZ GRAFITO DE GRAFITO NO. 2', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1121, 'precio_unitario' => 4.59, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'LIBRETA DE REGISTRO FORMA ITALIANA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 439, 'precio_unitario' => 298.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'LIBRETA PARA RECADOS TELEFONICOS', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 15, 'precio_unitario' => 13.50, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'LIBRETA PASTA DURA MEDIA CARTA FORMA FRANCESA 205 CON ABC', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 194, 'precio_unitario' => 400.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'LIBRETA PASTA DURA MEDIA CARTA FORMA FRANCESA 205 SIN ABC', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 165, 'precio_unitario' => 208.07, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'LIBRETA PASTA DURA OFICIO FORMA FRANCESA 505 CON ABC', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 89, 'precio_unitario' => 390.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'LIBRETA PASTA DURA OFICIO FORMA FRANCESA 505 SIN ABC', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 42, 'precio_unitario' => 428.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'LIGA COLOR CARNE NO. 18', 'unidad_medida' => 'Bolsa(s)', 'stock_actual' => 3032, 'precio_unitario' => 25.28, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'MARCADOR DE CERA ROJO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 6183, 'precio_unitario' => 10.42, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'MARCADOR DE TINTA PERMANENTE', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 3773, 'precio_unitario' => 8.67, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'MARCADOR DE TINTA PERMANENTE PUNTA CINCEL COLOR ROJO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 804, 'precio_unitario' => 13.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'MARCADOR DE TINTA PERMANENTE PUNTO FINO COLOR NEGRO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1479, 'precio_unitario' => 12.88, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'MARCADOR MARCATEXTOS AMARILLO PUNTA CINCEL', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 4807, 'precio_unitario' => 13.14, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'MARCADOR PUNTO FINO AZUL', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 4259, 'precio_unitario' => 8.50, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'MARCADORES DE COLORES EN ESTUCHE TINTA FUGAZ CON 4 PARA PIZARRON BLANCO/PINTARRON', 'unidad_medida' => 'Juego(s)', 'stock_actual' => 217, 'precio_unitario' => 67.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'MICA PROTECTORA TIPO BOLSA CARTA PARA RECOPILADOR', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1330, 'precio_unitario' => 0.84, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'MICA PROTECTORA TIPO BOLSA OFICIO PARA RECOPILADOR', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 5150, 'precio_unitario' => 10.50, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'MINAS O PUNTILLAS .5 MM', 'unidad_medida' => 'Tubo(s)', 'stock_actual' => 20, 'precio_unitario' => 11.11, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'PAPELERA 3 NIVELES TAMAÑO OFICIO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 37, 'precio_unitario' => 352.20, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'PASTAS PARA ENGARGOLAR TAMAÑO CARTA PLASTIFICADA AZUL CLARO C/50 PIEZAS', 'unidad_medida' => 'Paquete(s)', 'stock_actual' => 78, 'precio_unitario' => 95.50, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'PERFORADORA DE MANO SENCILLA TIPO PINZA MEDIANA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 19, 'precio_unitario' => 33.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'PERFORADORA TRABAJO PESADO DE 8 CMS DOBLE', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 97, 'precio_unitario' => 950.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'PERFORADORA TRIPLE', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 28, 'precio_unitario' => 135.31, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'PLASTICO AUTOADHERIBLE', 'unidad_medida' => 'Rollo(s)', 'stock_actual' => 129, 'precio_unitario' => 233.91, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'PLASTICO AUTOADHERIBLE PARA EMPLAYAR CALIBRE 80', 'unidad_medida' => 'Rollo(s)', 'stock_actual' => 34, 'precio_unitario' => 154.31, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'PLASTICO AUTOADHERIBLE TIPO MYLAR', 'unidad_medida' => 'Rollo(s)', 'stock_actual' => 48, 'precio_unitario' => 220.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'PORTA CLIP ACRILICO CON IMAN', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 9, 'precio_unitario' => 45.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'PORTA LAPIZ DE ACRILICO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 161, 'precio_unitario' => 32.30, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'PORTAMINAS O LAPICERO .5 MM', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1, 'precio_unitario' => 26.58, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'POSTE ARCHIVADOR ARCHIVO TIPO LEGAJO DE 2"', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1020, 'precio_unitario' => 8.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'POSTE ARCHIVADOR ARCHIVO TIPO LEGAJO DE 3"', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 2670, 'precio_unitario' => 9.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'POSTE ARCHIVADOR ARCHIVO TIPO LEGAJO DE 4"', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 3192, 'precio_unitario' => 8.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'RECOPILADOR DE PLASTICO CARTA DE 1 1/2 PULG COLOR BLANCO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 15, 'precio_unitario' => 76.81, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'RECOPILADOR DE PLASTICO CARTA DE 1 PULG', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1085, 'precio_unitario' => 40.89, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'RECOPILADOR DE PLASTICO CARTA DE 1/2 PULG', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 264, 'precio_unitario' => 70.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'RECOPILADOR DE PLASTICO CARTA DE 2 PULG', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 895, 'precio_unitario' => 99.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'RECOPILADOR DE PLASTICO CARTA DE 3 PULG', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 312, 'precio_unitario' => 144.28, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'RECOPILADOR DE PLASTICO CARTA DE 4 PULG', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 149, 'precio_unitario' => 168.34, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'RECOPILADOR DE PLASTICO CARTA DE 5 PULG', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 72, 'precio_unitario' => 249.71, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'RECOPILADOR PARA LEGAJO CARTA (LEFORT)', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 731, 'precio_unitario' => 68.11, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'RECOPILADOR PARA LEGAJO OFICIO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1559, 'precio_unitario' => 50.25, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'REGLA METALICA 30 CMS', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 510, 'precio_unitario' => 31.20, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'SELLO DE GOMA CON MANGUILLO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 7, 'precio_unitario' => 24.88, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'SELLO DE GOMA ENTINTAJE AUTOMATICO FECHADOR', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 644, 'precio_unitario' => 345.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'SELLO DE GOMA OFICIAL', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1, 'precio_unitario' => 660.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'SELLO ESCUDO OFICIAL AUTOMATICO 4.5 X 4.5', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 399, 'precio_unitario' => 450.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'SEPARADOR DE COLORES 5 DIVISIONES', 'unidad_medida' => 'Juego(s)', 'stock_actual' => 145, 'precio_unitario' => 33.26, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'SEPARADOR NUMERICO 10 COLORES SEPARADOR 10 DIVISIONES', 'unidad_medida' => 'Juego(s)', 'stock_actual' => 88, 'precio_unitario' => 32.50, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'SEPARADOR NUMERICO 15 COLORES', 'unidad_medida' => 'Juego(s)', 'stock_actual' => 432, 'precio_unitario' => 56.09, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'SOBRE BLANCO OFICIO PARA CORRESPONDENCIA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 6, 'precio_unitario' => 1.82, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'SOBRE MANILA BOLSA CARTA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 11590, 'precio_unitario' => 4.48, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'SOBRE MANILA BOLSA DOBLE OFICIO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 3, 'precio_unitario' => 14.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'SOBRE MANILA TAMAÑO RADIOGRAFIA 400X500', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 345, 'precio_unitario' => 15.42, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'SUJETADOR DE DOCUMENTOS CHICO CON 12', 'unidad_medida' => 'Caja(s)', 'stock_actual' => 1250, 'precio_unitario' => 12.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'SUJETADOR DE DOCUMENTOS GRANDE CON 12', 'unidad_medida' => 'Caja(s)', 'stock_actual' => 431, 'precio_unitario' => 50.64, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'SUJETADOR DE DOCUMENTOS MEDIANO CON 12', 'unidad_medida' => 'Caja(s)', 'stock_actual' => 640, 'precio_unitario' => 31.74, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'TABLA DE ACRILICO CON CLIP SUJETADOR CARTA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 381, 'precio_unitario' => 97.28, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'TABLA DE ACRILICO CON CLIP SUJETADOR OFICIO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 37, 'precio_unitario' => 79.44, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'TABLA DE FIBRACEL CON CLIP SUJETADOR OFICIO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 99, 'precio_unitario' => 43.70, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'TARJETA BRISTOL MEDIA CARTA', 'unidad_medida' => 'Paquete(s)', 'stock_actual' => 549, 'precio_unitario' => 49.90, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'TINTA PARA COJIN CON APLICADOR EN FRASCO 60 ML COLOR AZUL', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 794, 'precio_unitario' => 27.50, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'TINTA PARA COJIN CON APLICADOR EN FRASCO 60 ML COLOR NEGRO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 529, 'precio_unitario' => 60.91, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'TINTA PARA COJIN CON APLICADOR EN FRASCO 60 ML COLOR ROJO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 366, 'precio_unitario' => 42.00, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'TINTA PARA FECHADOR DE MAROMA COLOR NEGRO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 69, 'precio_unitario' => 106.93, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'TINTA PARA FECHADOR DE MAROMA COLOR ROJO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 66, 'precio_unitario' => 106.93, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            ['nombre' => 'TINTA PARA FOLIADOR Y CHECADOR EN FRASCO NEGRO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 214, 'precio_unitario' => 109.56, 'categoria' => 'ARTICULOS Y MATERIALES DE ESCRITORIO'],
            
            // ========== PAPEL ==========
            ['nombre' => 'CARTULINA BRISTOL CARTA AMARILLA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 8943, 'precio_unitario' => 1.84, 'categoria' => 'PAPEL'],
            ['nombre' => 'CARTULINA BRISTOL CARTA BLANCA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 758, 'precio_unitario' => 30.00, 'categoria' => 'PAPEL'],
            ['nombre' => 'CARTULINA OPALINA CARTA 125 GRS', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 4750, 'precio_unitario' => 0.68, 'categoria' => 'PAPEL'],
            ['nombre' => 'CARTULINA OPALINA CARTA 225 GRS', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 49342, 'precio_unitario' => 2.00, 'categoria' => 'PAPEL'],
            ['nombre' => 'PAPEL AUTOCOPIABLE CARTA AZUL', 'unidad_medida' => 'Millar(es)', 'stock_actual' => 3.50, 'precio_unitario' => 580.00, 'categoria' => 'PAPEL'],
            ['nombre' => 'PAPEL AUTOCOPIABLE OFICIO AZUL', 'unidad_medida' => 'Millar(es)', 'stock_actual' => 8.00, 'precio_unitario' => 8.00, 'categoria' => 'PAPEL'],
            ['nombre' => 'PAPEL AUTOCOPIABLE OFICIO ROSA', 'unidad_medida' => 'Millar(es)', 'stock_actual' => 11.50, 'precio_unitario' => 99.78, 'categoria' => 'PAPEL'],
            ['nombre' => 'PAPEL BOND CORTADO CARTA 37 KG BLANCO 216MM X 279MM', 'unidad_medida' => 'Millar(es)', 'stock_actual' => 2201, 'precio_unitario' => 146.81, 'categoria' => 'PAPEL'],
            ['nombre' => 'PAPEL BOND CORTADO OFICIO 50 KG BLANCO', 'unidad_medida' => 'Millar(es)', 'stock_actual' => 14695, 'precio_unitario' => 208.19, 'categoria' => 'PAPEL'],
            ['nombre' => 'PAPEL CARBON TAMAÑO CARTA PARA MAQUINA DE ESCRIBIR', 'unidad_medida' => 'Paquete(s)', 'stock_actual' => 28, 'precio_unitario' => 116.00, 'categoria' => 'PAPEL'],
            ['nombre' => 'PAPEL CARBON TAMAÑO OFICIO PARA MAQUINA DE ESCRIBIR', 'unidad_medida' => 'Paquete(s)', 'stock_actual' => 162, 'precio_unitario' => 133.46, 'categoria' => 'PAPEL'],
            ['nombre' => 'PAPEL TERMICO ROLLO DE 57MM', 'unidad_medida' => 'Rollo(s)', 'stock_actual' => 100, 'precio_unitario' => 35.98, 'categoria' => 'PAPEL'],
            
            // ========== EQUIPO CONTRA INCENDIO ==========
            ['nombre' => 'EXTINTOR CO2 2.3 KGS 5 LIBRAS', 'unidad_medida' => 'Equipo(s)', 'stock_actual' => 10, 'precio_unitario' => 1606.00, 'categoria' => 'EQUIPO CONTRA INCENDIO'],
            
            // ========== GUANTES ==========
            ['nombre' => 'GUANTES DE LATEX PARA EXPLORACIÓN NO ESTÉRILES T / GDE/L', 'unidad_medida' => 'Par(es)', 'stock_actual' => 1300, 'precio_unitario' => 17.85, 'categoria' => 'GUANTES'],
            
            // ========== LENTES, GOGLES Y CARETAS ==========
            ['nombre' => 'MONOGOGLE TRANSPARENTE CON VENTILA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 150, 'precio_unitario' => 130.00, 'categoria' => 'LENTES, GOGLES Y CARETAS'],
            
            // ========== SEÑALIZACION ==========
            ['nombre' => 'CINTA DELIMITADORA CON LEYENDA PELIGRO', 'unidad_medida' => 'Rollo(s)', 'stock_actual' => 670, 'precio_unitario' => 81.50, 'categoria' => 'SEÑALIZACION'],
            ['nombre' => 'CINTA DELIMITADORA CON LEYENDA PROHIBIDO EL PASO', 'unidad_medida' => 'Rollo(s)', 'stock_actual' => 283, 'precio_unitario' => 81.50, 'categoria' => 'SEÑALIZACION'],
            
            // ========== BOYAS Y ACCESORIOS ==========
            ['nombre' => 'NIPLE GALVANIZADO DE 1 1/2" X 6"', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 3, 'precio_unitario' => 8.40, 'categoria' => 'BOYAS Y ACCESORIOS'],
            
            // ========== REFACCIONES PARA SEMAFORIZACION ==========
            ['nombre' => 'PILA DE 3 V. (CR2032 LI MN)', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 11, 'precio_unitario' => 76.72, 'categoria' => 'REFACCIONES PARA SEMAFORIZACION'],
            
            // ========== EQUIPO MEDICO Y DE LABORATORIO ==========
            ['nombre' => 'KIT BASICO PARA LABORATORIO DE QUIMICA FORENSE', 'unidad_medida' => 'Juego(s)', 'stock_actual' => 22, 'precio_unitario' => 1650.00, 'categoria' => 'EQUIPO MEDICO Y DE LABORATORIO'],
            
            // ========== MATERIAL DE CURACION ==========
            ['nombre' => 'BOLSA CLINICA AMARILLA PARA RESIDUOS BIOLOGICOS', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 300, 'precio_unitario' => 3.67, 'categoria' => 'MATERIAL DE CURACION'],
            ['nombre' => 'BOLSA CLINICA ROJA PARA MATERIAL BIOLOGICO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 200, 'precio_unitario' => 9.19, 'categoria' => 'MATERIAL DE CURACION'],
            ['nombre' => 'CUBREBOCA KN-95', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 3400, 'precio_unitario' => 55.00, 'categoria' => 'MATERIAL DE CURACION'],
            ['nombre' => 'CUBREBOCAS PARA CIRUJANO', 'unidad_medida' => 'Paquete(s)', 'stock_actual' => 460, 'precio_unitario' => 3.70, 'categoria' => 'MATERIAL DE CURACION'],
            ['nombre' => 'GUANTE QUIRURGICO DESECHABLE PARA CIRUJANO ESTERIL', 'unidad_medida' => 'Par(es)', 'stock_actual' => 10, 'precio_unitario' => 5.00, 'categoria' => 'MATERIAL DE CURACION'],
            
            // ========== MEDICINAS ==========
            ['nombre' => 'SUERO 625 ML.', 'unidad_medida' => 'Frasco(s)', 'stock_actual' => 1248, 'precio_unitario' => 21.00, 'categoria' => 'MEDICINAS'],
            
            // ========== MATERIAL DE LIMPIEZA PARA HOSPITAL ==========
            ['nombre' => 'SANITIZANTE', 'unidad_medida' => 'Liter(s)', 'stock_actual' => 64440, 'precio_unitario' => 37.00, 'categoria' => 'MATERIAL DE LIMPIEZA PARA HOSPITAL'],
            
            // ========== ACCESORIOS ELECTRICOS (muestras principales) ==========
            ['nombre' => 'BALASTRA 2 X 75 127 WATTS', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 755, 'precio_unitario' => 107.79, 'categoria' => 'ACCESORIOS ELECTRICOS'],
            ['nombre' => 'BALASTRO ELECTRONICO 2 X 30, 2 X 32, 2 X 39', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 272, 'precio_unitario' => 184.48, 'categoria' => 'ACCESORIOS ELECTRICOS'],
            ['nombre' => 'PASTILLA TERMICA BIFASICA 30 DE AMPERS', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 93, 'precio_unitario' => 244.50, 'categoria' => 'ACCESORIOS ELECTRICOS'],
            ['nombre' => 'PASTILLA TERMICA TRIFASICA DE 40 AMPERS', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 20, 'precio_unitario' => 2120.99, 'categoria' => 'ACCESORIOS ELECTRICOS'],
            
            // ========== ACCESORIOS PARA HERRAMIENTAS ==========
            ['nombre' => 'FULMINANTE PARA PISTOLA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 4500, 'precio_unitario' => 312.00, 'categoria' => 'ACCESORIOS PARA HERRAMIENTAS'],
            ['nombre' => 'DISCO DE CORTE DE METAL DE 4 1/2', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 699, 'precio_unitario' => 5.00, 'categoria' => 'ACCESORIOS PARA HERRAMIENTAS'],
            
            // ========== CABLES Y ALAMBRES (muestras principales) ==========
            ['nombre' => 'CABLE BAJA TENSION CONSTRUCCION THW CALIBRE 6 COLOR NEGRO', 'unidad_medida' => 'Metro(s)', 'stock_actual' => 7700, 'precio_unitario' => 48.93, 'categoria' => 'CABLES Y ALAMBRES'],
            ['nombre' => 'CABLE BAJA TENSION CONSTRUCCION TIPO POT CALIBRE 12 COLOR BLANCO', 'unidad_medida' => 'Metro(s)', 'stock_actual' => 3200, 'precio_unitario' => 33.80, 'categoria' => 'CABLES Y ALAMBRES'],
            
            // ========== CHAPAS (muestras principales) ==========
            ['nombre' => 'CANDADO DE LATON GANCHO CORTO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 82, 'precio_unitario' => 344.00, 'categoria' => 'CHAPAS'],
            ['nombre' => 'CERRADURA MODELO 400 PHILLIPS', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 9, 'precio_unitario' => 407.00, 'categoria' => 'CHAPAS'],
            ['nombre' => 'LLAVE FORJA PH4D', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 500, 'precio_unitario' => 3.41, 'categoria' => 'CHAPAS'],
            
            // ========== CUERDAS Y SOGAS ==========
            ['nombre' => 'BOLA DE IXTLE', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 330, 'precio_unitario' => 350.00, 'categoria' => 'CUERDAS Y SOGAS'],
            
            // ========== ESTOPA ==========
            ['nombre' => 'ESTOPA BLANCA DE 1 KG.', 'unidad_medida' => 'Bolsa(s)', 'stock_actual' => 394, 'precio_unitario' => 68.97, 'categoria' => 'ESTOPA'],
            
            // ========== FOCOS (muestras principales) ==========
            ['nombre' => 'FOCO AHORRADOR 100 W 220 V', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 245, 'precio_unitario' => 47.04, 'categoria' => 'FOCOS'],
            ['nombre' => 'LAMPARA DE LED DE 18 W DE 2 PIN', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 150, 'precio_unitario' => 241.58, 'categoria' => 'FOCOS'],
            ['nombre' => 'LAMPARA FLUORECENTE 32 WATS DE 1 PIN', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 626, 'precio_unitario' => 62.50, 'categoria' => 'FOCOS'],
            
            // ========== LIJAS ==========
            ['nombre' => 'LIJA DE LONA GRANO 100', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 130, 'precio_unitario' => 11.00, 'categoria' => 'LIJAS'],
            
            // ========== MANGUERAS ==========
            ['nombre' => 'MANGUERA PARA JARDIN REFORZADA 3/4', 'unidad_medida' => 'Metro(s)', 'stock_actual' => 170, 'precio_unitario' => 12.00, 'categoria' => 'MANGUERAS'],
            
            // ========== PEGAMENTOS Y SELLADORES ==========
            ['nombre' => 'PEGAMENTO BLANCO 850 ENVASE DE 1 KG.', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 38, 'precio_unitario' => 113.77, 'categoria' => 'PEGAMENTOS Y SELLADORES'],
            ['nombre' => 'SELLADOR PARA JUNTAS USO GENERAL', 'unidad_medida' => 'Tubo(s)', 'stock_actual' => 394, 'precio_unitario' => 67.83, 'categoria' => 'PEGAMENTOS Y SELLADORES'],
            
            // ========== PIJA, CHILILLOS, CLAVOS Y TAQUETES (muestras principales) ==========
            ['nombre' => 'PIJA PUNTA DE BROCA DE 1/2"', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 39700, 'precio_unitario' => 18.56, 'categoria' => 'PIJA, CHILILLOS, CLAVOS Y TAQUETES'],
            ['nombre' => 'PIJA PARA TABLAROCA 1"', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 16700, 'precio_unitario' => 0.29, 'categoria' => 'PIJA, CHILILLOS, CLAVOS Y TAQUETES'],
            
            // ========== PILAS ==========
            ['nombre' => 'PILA CONVENCIONAL TIPO AA ALCALINA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 594, 'precio_unitario' => 20.00, 'categoria' => 'PILAS'],
            ['nombre' => 'PILA CONVENCIONAL TIPO D', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1050, 'precio_unitario' => 19.60, 'categoria' => 'PILAS'],
            
            // ========== EQUIPO PARA SOLDAR Y ACCESORIOS ==========
            ['nombre' => 'SOLDADURA DE ESTAÑO DE 3 MM 95/5', 'unidad_medida' => 'Rollo(s)', 'stock_actual' => 12, 'precio_unitario' => 376.60, 'categoria' => 'EQUIPO PARA SOLDAR Y ACCESORIOS'],
            
            // ========== TORNILLOS TUERCAS Y ROLDANAS ==========
            ['nombre' => 'TORNILLO MARIPOSA DE 1/4', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1256, 'precio_unitario' => 3.33, 'categoria' => 'TORNILLOS TUERCAS Y ROLDANAS'],
            
            // ========== TUBERIAS, CONEXIONES Y ACCESORIOS (muestras principales) ==========
            ['nombre' => 'CODO DE CPVC DE 1"', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 56, 'precio_unitario' => 9.18, 'categoria' => 'TUBERIAS, CONEXIONES Y ACCESORIOS'],
            ['nombre' => 'NUDO DE CPVC DE 1"', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 173, 'precio_unitario' => 38.00, 'categoria' => 'TUBERIAS, CONEXIONES Y ACCESORIOS'],
            ['nombre' => 'TUBO DE PVC SANITARIO DE 6 PULG.', 'unidad_medida' => 'Tramo(s)', 'stock_actual' => 29, 'precio_unitario' => 630.75, 'categoria' => 'TUBERIAS, CONEXIONES Y ACCESORIOS'],
            
            // ========== ACCESORIOS Y MUEBLES PARA BAÑO ==========
            ['nombre' => 'FLUXOMETRO PARA WC DE PEDAL MOD: 310-32', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 26, 'precio_unitario' => 12347.85, 'categoria' => 'ACCESORIOS Y MUEBLES PARA BAÑO'],
            ['nombre' => 'LAVABO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 7, 'precio_unitario' => 775.49, 'categoria' => 'ACCESORIOS Y MUEBLES PARA BAÑO'],
            
            // ========== HERRAMIENTAS (muestras principales) ==========
            ['nombre' => 'GATO PATIN LEVANTE RAPIDO DE 3.5 TONELADAS', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 6, 'precio_unitario' => 7089.97, 'categoria' => 'HERRAMIENTAS'],
            ['nombre' => 'PISTOLA DE IMPACTO INALAMBRICA DE CUADRO 1/2', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 6, 'precio_unitario' => 16665.15, 'categoria' => 'HERRAMIENTAS'],
            ['nombre' => 'ESCANER AUTOMOTRIZ PANTALLA DE 10.1", 1280*800', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1, 'precio_unitario' => 37901.00, 'categoria' => 'HERRAMIENTAS'],
            
            // ========== ACCESORIOS PARA EQUIPO DE FOTOGRAFIA ==========
            ['nombre' => 'TRIPIE PARA CAMARA Y VIDEO CAMARA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 4, 'precio_unitario' => 340.17, 'categoria' => 'ACCESORIOS PARA EQUIPO DE FOTOGRAFIA'],
            
            // ========== FUNGICIDAS, HERBICIDAS E INSECTICIDAS ==========
            ['nombre' => 'RATICIDA', 'unidad_medida' => 'Kilogramo(s)', 'stock_actual' => 20, 'precio_unitario' => 135.40, 'categoria' => 'FUNGICIDAS, HERBICIDAS E INSECTICIDAS'],
            
            // ========== IMPERMEABILIZANTES ==========
            ['nombre' => 'MEMBRANA PARA IMPERMEABILIZACION', 'unidad_medida' => 'Metro(s)', 'stock_actual' => 2400, 'precio_unitario' => 1246.55, 'categoria' => 'IMPERMEABILIZANTES'],
            
            // ========== SOLVENTES ==========
            ['nombre' => 'THINNER AMERICANO', 'unidad_medida' => 'Liter(s)', 'stock_actual' => 200, 'precio_unitario' => 42.00, 'categoria' => 'SOLVENTES'],
            
            // ========== PINTURAS Y ANTICORROSIVOS ==========
            ['nombre' => 'PINTURA VINILICA COLOR ROJO', 'unidad_medida' => 'Liter(s)', 'stock_actual' => 1216, 'precio_unitario' => 750.00, 'categoria' => 'PINTURAS Y ANTICORROSIVOS'],
            ['nombre' => 'PINTURA ESMALTE BLANCO', 'unidad_medida' => 'Liter(s)', 'stock_actual' => 418, 'precio_unitario' => 170.00, 'categoria' => 'PINTURAS Y ANTICORROSIVOS'],
            ['nombre' => 'PINTURA ESMALTE AZUL OBSCURO', 'unidad_medida' => 'Liter(s)', 'stock_actual' => 798, 'precio_unitario' => 71.20, 'categoria' => 'PINTURAS Y ANTICORROSIVOS'],
            
            // ========== TELAS ==========
            ['nombre' => 'FRANELA GRIS', 'unidad_medida' => 'Metro(s)', 'stock_actual' => 1695, 'precio_unitario' => 14.00, 'categoria' => 'TELAS'],
            
            // ========== ROPERIA PARA TRABAJO ==========
            ['nombre' => 'CHAMARRA ROMPEVIENTOS', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 276, 'precio_unitario' => 1150.00, 'categoria' => 'ROPERIA PARA TRABAJO'],
            ['nombre' => 'PLAYERA TIPO POLO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 144, 'precio_unitario' => 1747.00, 'categoria' => 'ROPERIA PARA TRABAJO'],
            ['nombre' => 'PANTALON TACTICO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 111, 'precio_unitario' => 1655.00, 'categoria' => 'ROPERIA PARA TRABAJO'],
            ['nombre' => 'OVEROL DE PROTECCION DESECHABLE', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 390, 'precio_unitario' => 250.00, 'categoria' => 'ROPERIA PARA TRABAJO'],
            
            // ========== EQUIPO DIVERSO ==========
            ['nombre' => 'BASCULA ELECTRONICA CON INDICADOR DIGITAL TIPO GRAMERA CON CAPACIDAD DE 5KG.', 'unidad_medida' => 'Equipo(s)', 'stock_actual' => 16, 'precio_unitario' => 258.63, 'categoria' => 'EQUIPO DIVERSO'],
            ['nombre' => 'BOMBA PARA FUMIGAR MANUAL DE 15 LTS. TIPO MOCHILA', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 7, 'precio_unitario' => 1200.00, 'categoria' => 'EQUIPO DIVERSO'],
            
            // ========== EQUIPAMIENTO ESCOLAR ==========
            ['nombre' => 'BOTIQUIN DE PRIMEROS AUXILIOS', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 14, 'precio_unitario' => 11063.00, 'categoria' => 'EQUIPAMIENTO ESCOLAR'],
            
            // ========== MOBILIARIO DE OFICINA ==========
            ['nombre' => 'PIZARRON DE CORCHO DE 60 CM. DE ANCHO X 90 CM. DE LARGO', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 1, 'precio_unitario' => 800.00, 'categoria' => 'MOBILIARIO DE OFICINA'],
            
            // ========== PUBLICACIONES PROMOCIONALES ==========
            ['nombre' => 'LETRERO EN RECORTE DE VINIL', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 2000, 'precio_unitario' => 143.00, 'categoria' => 'PUBLICACIONES PROMOCIONALES'],
            ['nombre' => 'VINIL ESMERILADO O ESCOGRAPH', 'unidad_medida' => 'Rollo(s)', 'stock_actual' => 8, 'precio_unitario' => 2617.15, 'categoria' => 'PUBLICACIONES PROMOCIONALES'],
            
            // ========== CALZADO DE SEGURIDAD ==========
            ['nombre' => 'BOTA TIPO TACTICO', 'unidad_medida' => 'Par(es)', 'stock_actual' => 1136, 'precio_unitario' => 2842.00, 'categoria' => 'CALZADO DE SEGURIDAD'],
            ['nombre' => 'CUBRE ZAPATO', 'unidad_medida' => 'Par(es)', 'stock_actual' => 750, 'precio_unitario' => 21.65, 'categoria' => 'CALZADO DE SEGURIDAD'],
            
            // ========== REF. Y ACC. PARA VEHICULOS TERRESTRES ==========
            ['nombre' => 'ACUMULADOR BCI 24R', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 185, 'precio_unitario' => 3465.09, 'categoria' => 'REF. Y ACC. PARA VEHICULOS TERRESTRES'],
            ['nombre' => 'ACUMULADOR BCI 48/91', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 160, 'precio_unitario' => 4069.99, 'categoria' => 'REF. Y ACC. PARA VEHICULOS TERRESTRES'],
            ['nombre' => 'ACUMULADOR 47-600', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 80, 'precio_unitario' => 3523.98, 'categoria' => 'REF. Y ACC. PARA VEHICULOS TERRESTRES'],
            ['nombre' => 'BALATA DELANTERA PARA DODGE RAM 2500', 'unidad_medida' => 'Juego(s)', 'stock_actual' => 198, 'precio_unitario' => 446.20, 'categoria' => 'REF. Y ACC. PARA VEHICULOS TERRESTRES'],
            
            // ========== LLANTAS, CAMARAS, CORBATAS Y LODERAS ==========
            ['nombre' => 'LLANTA PARA CAMIONETA 245/65 R17', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 346, 'precio_unitario' => 4400.00, 'categoria' => 'LLANTAS, CAMARAS, CORBATAS Y LODERAS'],
            ['nombre' => 'LLANTA PARA CAMIONETA 285/65 R18 ALL TERRAIN', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 220, 'precio_unitario' => 7900.00, 'categoria' => 'LLANTAS, CAMARAS, CORBATAS Y LODERAS'],
            ['nombre' => 'LLANTA PARA CAMIONETA 285/70/R17', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 134, 'precio_unitario' => 11226.38, 'categoria' => 'LLANTAS, CAMARAS, CORBATAS Y LODERAS'],
            ['nombre' => 'LLANTA PARA AUTOMOVIL 185/60 R 15', 'unidad_medida' => 'Pieza(s)', 'stock_actual' => 18, 'precio_unitario' => 1405.55, 'categoria' => 'LLANTAS, CAMARAS, CORBATAS Y LODERAS'],
        ];
    }
}
