@extends('layouts.app')

@section('title', 'Comprobante de Entrega a Policía')

@section('content')
<div class="px-4 sm:px-0">
    <!-- Botones de acción -->
    <div class="flex justify-between items-center mb-6 no-print">
        <h1 class="text-3xl font-bold text-gray-900">Comprobante de Entrega a Policía</h1>
        <div class="space-x-2">
            <button onclick="window.print()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                🖨️ Imprimir
            </button>
            <a href="{{ route('salidas-policia.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                Volver
            </a>
        </div>
    </div>

    <!-- Formato oficial para impresión - EXACTAMENTE COMO SALIDAS NORMALES -->
    <div class="print-only">
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 30px;
                font-size: 12px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            .header-table td {
                vertical-align: top;
                padding-bottom: 5px;
            }

            .main-title {
                text-align: center;
                font-size: 16px;
                font-weight: bold;
                margin: 15px 0;
            }

            .info-table td {
                border: 1px solid black;
                padding: 5px;
                font-size: 11px;
                vertical-align: top;
            }

            .info-title {
                font-weight: bold;
                font-size: 11px;
                display: block;
                margin-bottom: 2px;
            }

            .articulos th {
                background: #e6e6e6;
                border: 1px solid black;
                padding: 5px;
                text-align: center;
            }

            .articulos td {
                border: 1px solid black;
                padding: 5px;
            }

            .signatures {
                margin-top: 40px;
                width: 100%;
            }

            .signature-cell {
                text-align: center;
                padding: 10px;
            }

            .signature-line {
                border-bottom: 2px solid #000;
                height: 40px;
                margin: 5px 0;
                width: 100%;
            }

            .signature-label {
                font-weight: bold;
                font-size: 12px;
            }

            .signature-name {
                font-size: 12px;
                font-weight: bold;
                margin-top: 8px;
                min-height: 20px;
                color: #000;
            }

            @media print {
                @page {
                    margin: 0;
                    size: letter;
                }
                
                body {
                    margin: 0;
                    padding: 0;
                    background: white !important;
                }
                
                .no-print,
                .no-print *,
                .navbar,
                nav,
                nav *,
                footer {
                    display: none !important;
                    visibility: hidden !important;
                }
                
                .print-only {
                    width: 100%;
                    max-width: 900px;
                    margin: 0 auto !important;
                    padding: 40px 60px !important;
                    display: block !important;
                    visibility: visible !important;
                    background: white !important;
                    border: none !important;
                    box-shadow: none !important;
                }
                
                .print-only *,
                .print-only table,
                .print-only thead,
                .print-only tbody,
                .print-only tr,
                .print-only td,
                .print-only th {
                    display: revert !important;
                    visibility: visible !important;
                }
            }
        </style>

        <!-- ENCABEZADO -->
        <table class="header-table">
            <tr>
                <td style="width: 20%;">
                    <img src="{{ asset('fondo-salida.png') }}" width="120">
                </td>
                <td>
                    <div style="font-size: 15px; font-weight:bold;">GOBIERNO DE JALISCO</div>
                    <div>PODER EJECUTIVO</div>
                    <div style="font-weight:bold;">16-FISCALÍA ESTATAL</div>
                </td>
            </tr>
        </table>

        <div class="main-title">Salida de almacén</div>

        <!-- NÚMERO DE OFICIO -->
        <div style="text-align: center; margin: 10px 0; font-size: 12px; font-weight: bold;">
            OFICIO: {{ $salida->folio ?? 'FISCALÍA ESTATAL (FÍSICO).OUT/' . $salida->fecha->format('Y') . '/' . str_pad($salida->id, 5, '0', STR_PAD_LEFT) }}.
        </div>

        <!-- TABLA DE INFORMACIÓN -->
        <table class="info-table">
            <tr>
                <td>
                    <span class="info-title">Almacén:</span>
                    16-FISCALÍA ESTATAL
                </td>

                <td>
                    <span class="info-title">Tipo Movimiento:</span>
                    SALIDA DE INSUMOS
                </td>

                <td style="text-align: right; font-weight:bold;">
                    <span class="info-title">Folio:</span>
                    {{ $salida->folio ?? 'FISCALÍA ESTATAL (FÍSICO).OUT/' . $salida->fecha->format('Y') . '/' . str_pad($salida->id, 5, '0', STR_PAD_LEFT) }}
                </td>
            </tr>

            <tr>
                <td>
                    <span class="info-title">Dependencia:</span>
                    Fiscalía Estatal
                </td>

                <td>
                    <span class="info-title">Estatus:</span>
                    Hecho
                </td>

                <td>
                    <span class="info-title">Fecha:</span>
                    {{ $salida->fecha->format('d/m/Y') }}
                </td>
            </tr>

            <tr>
                <td>
                    <span class="info-title">No. Solicitud:</span>
                    {{ $salida->motivo ?: 'VALE MANUAL POR CORREO' }}
                </td>

                <td>
                    <span class="info-title">Empleado:</span>
                    {{ $salida->usuario->name ?? '' }}
                </td>

                <td style="text-align:center; font-size:11px;">
                    HOJA 1 DE 1
                </td>
            </tr>

            <tr>
                <td>
                    <span class="info-title">Área:</span>
                    @if($salida->policia && $salida->policia->area)
                        {{ $salida->policia->area }}
                    @elseif($salida->destino)
                        {{ $salida->destino }}
                    @else
                        Oficina del Fiscal
                    @endif
                </td>

                <td>
                    <span class="info-title">Observación:</span>
                    @if($salida->policia)
                        Entrega a Policía: {{ $salida->policia->nombre_completo }}@if($salida->policia->numero_empleado) (Empleado: {{ $salida->policia->numero_empleado }})@endif
                        @if($salida->observaciones)
                            - {{ $salida->observaciones }}
                        @endif
                    @else
                        {{ $salida->observaciones ?: '' }}
                    @endif
                </td>

                <td></td>
            </tr>
        </table>

        <br>

        <!-- TABLA DE ARTÍCULOS -->
        <table class="articulos">
            <thead>
                <tr>
                    <th style="width:10%;">CANT.</th>
                    <th style="width:10%;">U.M.</th>
                    <th>DESCRIPCIÓN</th>
                    <th style="width:15%;">Precio Unitario</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td style="text-align:center;">{{ number_format($salida->cantidad, 2, '.', ',') }}</td>
                    <td style="text-align:center;">{{ $salida->producto->unidad_medida }}</td>
                    <td>{{ strtoupper($salida->producto->nombre) }}</td>
                    <td style="text-align:right;">${{ number_format($salida->producto->precio_unitario ?? 0, 2, '.', ',') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- FIRMAS -->
        <table class="signatures">
            <tr>
                <td class="signature-cell">
                    <div class="signature-label">Entregó</div>
                    <div class="signature-line"></div>
                    <div class="signature-name" style="font-weight: bold; font-size: 12px; margin-top: 8px;">
                        {{ $salida->entrega_nombre ?: '_________________________' }}
                    </div>
                </td>

                <td class="signature-cell">
                    <div class="signature-label">Recibió</div>
                    <div class="signature-line"></div>
                    <div class="signature-name" style="font-weight: bold; font-size: 12px; margin-top: 8px;">
                        @if($salida->recibe_nombre)
                            {{ $salida->recibe_nombre }}
                        @elseif($salida->policia)
                            {{ $salida->policia->nombre_completo }}
                        @else
                            _________________________
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Solo estilos para mostrar en pantalla */
    .print-only {
        display: block !important;
        max-width: 900px;
        margin: 20px auto;
        padding: 20px 50px;
        background: white;
        font-family: Arial, sans-serif;
        border: 1px solid #ddd;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
</style>
@endpush
