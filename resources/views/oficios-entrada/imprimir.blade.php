<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Oficio de Entrada - {{ $oficioEntrada->folio_completo }}</title>
    <style>
        @page {
            size: letter;
            margin: 2cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
            background: #fff;
        }

        .header {
            background-color: #0d7377;
            color: white;
            padding: 20px;
            margin: -2cm -2cm 30px -2cm;
            display: flex;
            align-items: center;
            border-radius: 0;
        }

        .logo-section {
            display: flex;
            align-items: center;
        }

        .logo {
            width: 60px;
            height: 60px;
            background-color: white;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: #0d7377;
            font-weight: bold;
            font-size: 24pt;
        }

        .fiscalia-title {
            color: white;
            font-size: 20pt;
            font-weight: bold;
            margin: 0;
        }

        .fiscalia-subtitle {
            color: white;
            font-size: 14pt;
            margin: 0;
            font-weight: normal;
        }

        .oficio-info {
            text-align: right;
            margin-bottom: 15px;
            font-size: 10pt;
        }

        .oficio-info strong {
            font-weight: bold;
        }

        .asunto {
            margin-bottom: 20px;
            font-size: 11pt;
            font-weight: bold;
        }

        .destinatario {
            margin-bottom: 20px;
        }

        .destinatario-line {
            margin-bottom: 5px;
            font-size: 11pt;
        }

        .destinatario-line.presente {
            margin-top: 10px;
            font-size: 11pt;
            letter-spacing: 2px;
            font-weight: normal;
        }

        .fecha-lugar {
            text-align: right;
            margin-bottom: 30px;
            font-size: 11pt;
        }

        .cuerpo {
            text-align: justify;
            margin-bottom: 30px;
            font-size: 11pt;
            line-height: 1.8;
        }

        .cuerpo p {
            margin-bottom: 15px;
        }

        .importe-destacado {
            background-color: #e5e7eb;
            padding: 8px;
            font-weight: bold;
            display: inline-block;
        }

        .firma-section {
            margin-top: 50px;
            margin-bottom: 30px;
        }

        .firma-line {
            margin-bottom: 5px;
            font-size: 11pt;
            font-weight: bold;
        }

        .firma-subtitle {
            font-size: 10pt;
            margin-top: 5px;
        }

        .copia-section {
            margin-top: 40px;
            font-size: 10pt;
        }

        .copia-line {
            margin-bottom: 3px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #374151;
            color: white;
            padding: 15px 2cm;
            font-size: 9pt;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 0 -2cm;
        }

        .footer-left {
            display: flex;
            align-items: center;
        }

        .footer-logo-text {
            margin-left: 10px;
        }

        .footer-right {
            text-align: right;
        }

        .footer-right p {
            margin: 2px 0;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #059669;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .print-button:hover {
            background-color: #047857;
        }

        .export-word-button {
            position: fixed;
            top: 70px;
            right: 20px;
            background-color: #2563eb;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            display: inline-block;
        }

        .export-word-button:hover {
            background-color: #1d4ed8;
        }

        @media print {
            .print-button,
            .export-word-button {
                display: none;
            }

            .footer {
                position: fixed;
            }
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button">🖨️ Imprimir</button>
    <a href="{{ url('oficios-entrada/' . $oficioEntrada->id . '/descargar-word') }}" class="export-word-button">📄 Exportar a Word</a>

    <div class="header">
        <div class="logo-section">
            <div class="logo">FE</div>
            <div>
                <div class="fiscalia-title">FISCALÍA</div>
                <div class="fiscalia-subtitle">del Estado</div>
            </div>
        </div>
    </div>

    <div class="oficio-info">
        <strong>OFICIO:</strong> {{ $oficioEntrada->folio_completo }}.
    </div>

    <div class="asunto">
        <strong>ASUNTO:</strong> Material Recibido.
    </div>

    <div class="destinatario">
        <div class="destinatario-line"><strong>LIC. MAURICIO IVÁN LÓPEZ MUÑIZ.</strong></div>
        <div class="destinatario-line"><strong>DIRECTOR DE RECURSOS MATERIALES</strong></div>
        <div class="destinatario-line"><strong>DE LA FISCALÍA ESTATAL.</strong></div>
        <div class="destinatario-line presente"><strong>P R E S E N T E.</strong></div>
    </div>

    <div class="fecha-lugar">
        @php
            $meses = [
                1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
                5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
                9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
            ];
            $fecha = $oficioEntrada->fecha_oficio;
            $dia = $fecha->format('d');
            $mes = $meses[(int)$fecha->format('m')];
            $anio = $fecha->format('Y');
        @endphp
        Guadalajara, Jal., {{ $dia }} de {{ $mes }} del {{ $anio }}.
    </div>

    <div class="cuerpo">
        <p>
            @php
                $fechaRecepcion = $oficioEntrada->fecha_recepcion;
                $diaRecepcion = str_pad($fechaRecepcion->format('d'), 2, '0', STR_PAD_LEFT);
                $mesRecepcion = strtoupper($meses[(int)$fechaRecepcion->format('m')]);
            @endphp
            Aunado a un cordial saludo me permito informarle a Usted que se recibieron, {{ $oficioEntrada->descripcion_material }}. el día 
            {{ $diaRecepcion }} DE {{ $mesRecepcion }} DEL PRESENTE AÑO, del proveedor denominado {{ strtoupper($oficioEntrada->proveedor_nombre) }}, con la Factura No. {{ $oficioEntrada->numero_factura }}, por un importe total de ${{ number_format($oficioEntrada->importe_total, 2, '.', ',') }} ({{ strtoupper($oficioEntrada->importe_total_letra) }})
        </p>

        <p>
            Sin otro particular de momento, me despido quedando a sus órdenes para cualquier duda o comentario.
        </p>
    </div>

    <div class="firma-section">
        <div class="firma-line">Atentamente</div>
        <div style="margin-top: 30px;"></div>
        <div class="firma-line">LIC. SANDRA ESMERALDA QUEZADA AGUIAR</div>
        <div class="firma-subtitle">JEFE "A" DE UNIDAD DEPARTAMENTAL, ENCARGADA</div>
        <div class="firma-subtitle">DEL ALMACÉN GENERAL DE LA FISCALÍA ESTATAL</div>
    </div>

    <div class="copia-section">
        <div class="copia-line"><strong>C. c. p.</strong> L. C. P. Manuel Vázquez Rodríguez - Director General Administrativo de la Fiscalía del Estado.</div>
        <div class="copia-line"><strong>C. c. p.</strong> C. Carlos Gerardo Flores Gámez. – Encargado del Área de Adquisiciones.</div>
        <div style="margin-top: 10px; font-size: 9pt;">SEQA/mrl</div>
    </div>

    <div class="footer">
        <div class="footer-left">
            <div class="footer-logo-text">
                <strong>JALISCO</strong><br>
                GOBIERNO DEL ESTADO
            </div>
        </div>
        <div class="footer-right">
            <p>Calle 14 #2567, Col. Zona Industrial,</p>
            <p>Guadalajara, Jalisco. C.P. 44940</p>
            <p>33 3837 6000 | fiscalia.jalisco.gob.mx</p>
        </div>
    </div>
</body>
</html>

