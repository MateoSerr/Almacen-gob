<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Entrada - Almacén Fiscalía</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 20px;
            color: #666;
        }

        .info-section {
            margin-bottom: 25px;
        }

        .info-section h3 {
            background-color: #f0f0f0;
            padding: 8px;
            margin-bottom: 10px;
            font-size: 14px;
            border-left: 4px solid #dc2626;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .info-item {
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: bold;
            font-size: 12px;
            color: #666;
            margin-bottom: 3px;
        }

        .info-value {
            font-size: 14px;
            padding: 5px;
            border-bottom: 1px solid #ccc;
            min-height: 25px;
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .product-table th,
        .product-table td {
            border: 1px solid #333;
            padding: 10px;
            text-align: left;
        }

        .product-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 12px;
        }

        .product-table td {
            font-size: 14px;
        }

        .signatures {
            margin-top: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .signature-box {
            background-color: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 20px;
            min-height: 120px;
            text-align: left;
        }

        .signature-label {
            font-weight: bold;
            margin-top: 5px;
            font-size: 13px;
        }

        .signature-line {
            border-top: 1px solid #666;
            margin-top: 60px;
            padding-top: 5px;
        }

        .observaciones {
            margin-top: 30px;
            padding: 10px;
            border: 1px solid #ccc;
            min-height: 80px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }

        @media print {
            body {
                padding: 10px;
            }

            .no-print {
                display: none;
            }

            @page {
                margin: 1cm;
            }
        }

        .print-button {
            background-color: #dc2626;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .print-button:hover {
            background-color: #b91c1c;
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button type="button" onclick="window.print(); return false;" class="print-button">🖨️ Imprimir Comprobante</button>
        <a href="{{ route('entradas.index') }}" style="margin-left: 10px; padding: 10px 20px; background-color: #6b7280; color: white; text-decoration: none; border-radius: 5px;">← Volver a Entradas</a>
    </div>

    <div class="header">
        <h1>FISCALÍA ESTATAL</h1>
        <h2>ALMACÉN FÍSICO</h2>
        <p style="margin-top: 10px; font-size: 16px; font-weight: bold;">COMPROBANTE DE ENTRADA DE INVENTARIO</p>
    </div>

    <div class="info-section">
        <h3>INFORMACIÓN GENERAL</h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Número de Entrada:</div>
                <div class="info-value">ENT-{{ str_pad($entrada->id, 6, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Folio:</div>
                <div class="info-value">{{ $entrada->folio ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Fecha:</div>
                <div class="info-value">{{ $entrada->fecha ? $entrada->fecha->format('d/m/Y') : 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Hora de Registro:</div>
                <div class="info-value">{{ $entrada->created_at ? $entrada->created_at->format('H:i:s') : 'N/A' }}</div>
            </div>
            @if($entrada->proveedor)
            <div class="info-item">
                <div class="info-label">Proveedor:</div>
                <div class="info-value">{{ $entrada->proveedor }}</div>
            </div>
            @endif
            @if($entrada->numero_factura)
            <div class="info-item">
                <div class="info-label">Número de Factura:</div>
                <div class="info-value">{{ $entrada->numero_factura }}</div>
            </div>
            @endif
            @if($entrada->oficioEntrada)
            <div class="info-item">
                <div class="info-label">Oficio de Entrada:</div>
                <div class="info-value">{{ $entrada->oficioEntrada->folio_completo ?? $entrada->oficioEntrada->numero_oficio ?? 'N/A' }}</div>
            </div>
            @endif
            @if($entrada->usuario)
            <div class="info-item">
                <div class="info-label">Registrado por:</div>
                <div class="info-value">{{ $entrada->usuario->name ?? 'N/A' }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="info-section">
        <h3>DETALLE DEL PRODUCTO</h3>
        <table class="product-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Unidad</th>
                    <th>Precio Unitario</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ isset($entrada->producto) ? $entrada->producto->nombre : 'N/A' }}</td>
                    <td style="text-align: center;">{{ $entrada->cantidad ?? 0 }}</td>
                    <td>{{ isset($entrada->producto) ? $entrada->producto->unidad_medida : 'N/A' }}</td>
                    <td style="text-align: right;">${{ number_format($entrada->precio_unitario ?? 0, 2, '.', ',') }}</td>
                    <td style="text-align: right; font-weight: bold;">${{ number_format($entrada->total ?? 0, 2, '.', ',') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- VERSION 2.0 - SIN CODIGO, STOCK ANTERIOR, STOCK ACTUAL -->

    @if($entrada->observaciones)
    <div class="observaciones">
        <strong>Observaciones:</strong><br>
        {{ $entrada->observaciones }}
    </div>
    @endif
    
    <div class="info-section">
        <h3>ENTREGA Y RECEPCIÓN</h3>
        <div class="signatures">
            <div class="signature-box">
                <div style="font-size: 11px; color: #666; text-transform: uppercase; margin-bottom: 20px; font-weight: normal;">ENTREGÓ (PROVEEDOR)</div>
                <div style="border-top: 1px solid #333; padding-top: 5px; margin-top: 40px;">
                    <em style="font-size: 12px; color: #333;">Firma</em>
                </div>
                @if($entrada->entrega_nombre)
                <div style="margin-top: 10px; font-size: 12px; font-weight: bold;">{{ $entrada->entrega_nombre }}</div>
                @endif
                @if($entrada->entrega_firma)
                <div style="margin-top: 5px; font-size: 11px; color: #666; font-style: italic;">{{ $entrada->entrega_firma }}</div>
                @endif
            </div>
            <div class="signature-box">
                <div style="font-size: 11px; color: #666; text-transform: uppercase; margin-bottom: 20px; font-weight: normal;">RECIBIÓ (ALMACÉN)</div>
                <div style="border-top: 1px solid #333; padding-top: 5px; margin-top: 40px;">
                    <em style="font-size: 12px; color: #333;">Firma</em>
                </div>
                @if($entrada->recibe_nombre)
                <div style="margin-top: 10px; font-size: 12px; font-weight: bold;">{{ $entrada->recibe_nombre }}</div>
                @endif
                @if($entrada->recibe_firma)
                <div style="margin-top: 5px; font-size: 11px; color: #666; font-style: italic;">{{ $entrada->recibe_firma }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Documento generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Este comprobante es válido como documento oficial del almacén de la Fiscalía Estatal</p>
    </div>

    <script>
        // Auto-imprimir si se accede directamente después de crear
        if (window.location.search.includes('autoprint=1')) {
            window.addEventListener('load', function() {
                setTimeout(function() {
                    window.print();
                }, 500);
            });
        }
    </script>
</body>
</html>
