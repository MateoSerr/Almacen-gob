<?php

namespace App\Helpers;

class NumerosEnLetras
{
    /**
     * Convierte un número a letras en español
     */
    public static function convertir($numero, $moneda = 'pesos', $centavos = 'centavos')
    {
        $numero = floatval($numero);
        
        // Separar parte entera y decimal
        $parte_entera = floor($numero);
        $parte_decimal = round(($numero - $parte_entera) * 100);
        
        $texto_entero = self::convertirNumero($parte_entera);
        $texto_decimal = self::convertirNumero($parte_decimal);
        
        // Formato: CINCUENTA MIL QUINIENTOS OCHO PESOS 49/100 M.N.
        $resultado = strtoupper(trim($texto_entero)) . ' ' . strtoupper($moneda);
        
        if ($parte_decimal > 0) {
            $resultado .= ' ' . $parte_decimal . '/100';
        }
        
        $resultado .= ' M.N.';
        
        return $resultado;
    }
    
    /**
     * Convierte un número entero a letras
     */
    private static function convertirNumero($numero)
    {
        if ($numero == 0) {
            return 'cero';
        }
        
        if ($numero >= 1000000) {
            $millones = floor($numero / 1000000);
            $resto = $numero % 1000000;
            
            if ($resto == 0) {
                return self::convertirGrupo($millones) . ' millón' . ($millones > 1 ? 'es' : '');
            } else {
                return self::convertirGrupo($millones) . ' millón' . ($millones > 1 ? 'es' : '') . ' ' . self::convertirNumero($resto);
            }
        }
        
        return self::convertirGrupo($numero);
    }
    
    /**
     * Convierte un grupo de hasta 6 dígitos
     */
    private static function convertirGrupo($numero)
    {
        $unidades = [
            '', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve',
            'diez', 'once', 'doce', 'trece', 'catorce', 'quince', 'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve'
        ];
        
        $decenas = [
            '', '', 'veinte', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 'ochenta', 'noventa'
        ];
        
        $centenas = [
            '', 'ciento', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos',
            'seiscientos', 'setecientos', 'ochocientos', 'novecientos'
        ];
        
        if ($numero == 0) {
            return '';
        }
        
        if ($numero == 100) {
            return 'cien';
        }
        
        $texto = '';
        
        // Miles
        if ($numero >= 1000) {
            $miles = floor($numero / 1000);
            $resto = $numero % 1000;
            
            if ($miles == 1) {
                $texto = 'mil';
            } else {
                $texto = self::convertirCentenas($miles) . ' mil';
            }
            
            if ($resto > 0) {
                $texto .= ' ' . self::convertirCentenas($resto);
            }
            
            return trim($texto);
        }
        
        return self::convertirCentenas($numero);
    }
    
    /**
     * Convierte centenas (0-999)
     */
    private static function convertirCentenas($numero)
    {
        $unidades = [
            '', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve',
            'diez', 'once', 'doce', 'trece', 'catorce', 'quince', 'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve'
        ];
        
        $decenas = [
            '', '', 'veinte', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 'ochenta', 'noventa'
        ];
        
        $centenas = [
            '', 'ciento', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos',
            'seiscientos', 'setecientos', 'ochocientos', 'novecientos'
        ];
        
        if ($numero == 0) {
            return '';
        }
        
        if ($numero == 100) {
            return 'cien';
        }
        
        $texto = '';
        
        // Centenas
        $centena = floor($numero / 100);
        if ($centena > 0) {
            $texto = $centenas[$centena];
        }
        
        // Decenas y unidades
        $resto = $numero % 100;
        if ($resto > 0) {
            if ($resto < 20) {
                if ($centena > 0) {
                    $texto .= ' ' . $unidades[$resto];
                } else {
                    $texto = $unidades[$resto];
                }
            } else {
                $decena = floor($resto / 10);
                $unidad = $resto % 10;
                
                if ($decena == 2 && $unidad > 0) {
                    $texto .= ($centena > 0 ? ' ' : '') . 'veinti' . $unidades[$unidad];
                } else {
                    if ($centena > 0 || $decena > 0) {
                        $texto .= ($centena > 0 ? ' ' : '') . $decenas[$decena];
                    } else {
                        $texto = $decenas[$decena];
                    }
                    
                    if ($unidad > 0) {
                        $texto .= ' y ' . $unidades[$unidad];
                    }
                }
            }
        }
        
        return trim($texto);
    }
}


