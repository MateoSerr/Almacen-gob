<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="UTF-8">
    <meta name="ProgId" content="Word.Document">
    <meta name="Generator" content="Microsoft Word">
    <meta name="Originator" content="Microsoft Word">
    <title>Oficio de Entrada - {{ $oficioEntrada->folio_completo }}</title>
    <!--[if gte mso 9]>
    <xml>
        <w:WordDocument>
            <w:View>Print</w:View>
            <w:Zoom>100</w:Zoom>
            <w:DoNotOptimizeForBrowser/>
        </w:WordDocument>
    </xml>
    <![endif]-->
    <style>
        @page {
            size: letter;
            margin: 2.5cm 2cm;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            background: #fff;
        }

        .header {
            background-color: #0d7377;
            color: white;
            padding: 20px;
            margin: -2.5cm -2cm 30px -2cm;
            display: flex;
            align-items: center;
        }

        .header-logo {
            width: 60px;
            height: 60px;
            background-color: white;
            border-radius: 5px;
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0d7377;
            font-weight: bold;
            font-size: 24pt;
        }

        .header-text {
            flex: 1;
        }

        .header-text h1 {
            margin: 0;
            font-size: 20pt;
            font-weight: bold;
        }

        .header-text h2 {
            margin: 0;
            font-size: 14pt;
            font-weight: normal;
        }

        .oficio-number {
            text-align: center;
            margin: 20px 0;
            font-size: 12pt;
            font-weight: bold;
        }

        .asunto {
            text-align: center;
            margin: 20px 0;
            font-size: 12pt;
            font-weight: bold;
        }

        .destinatario {
            margin: 30px 0;
            font-size: 12pt;
        }

        .destinatario-line {
            margin-bottom: 5px;
        }

        .presente {
            margin-top: 15px;
            letter-spacing: 3px;
        }

        .fecha-lugar {
            text-align: right;
            margin: 30px 0;
            font-size: 12pt;
        }

        .cuerpo {
            text-align: justify;
            margin: 30px 0;
            font-size: 12pt;
            line-height: 1.8;
        }

        .cuerpo p {
            margin-bottom: 15px;
            text-indent: 0;
        }

        .firma-section {
            margin-top: 60px;
        }

        .firma-atentamente {
            margin-bottom: 50px;
            font-size: 12pt;
        }

        .firma-nombre {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 5px;
        }

        .firma-cargo {
            font-size: 11pt;
            margin-top: 3px;
        }

        .copia-section {
            margin-top: 40px;
            font-size: 11pt;
        }

        .copia-line {
            margin-bottom: 5px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #374151;
            color: white;
            padding: 15px 20px;
            font-size: 9pt;
            margin: 0 -2cm;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
    </style>
</head>
<body>
    <!-- Header con logo y nombre -->
    <div class="header">
        <div class="header-logo">FE</div>
        <div class="header-text">
            <h1>FISCALÍA</h1>
            <h2>del Estado</h2>
        </div>
    </div>

    <!-- Número de oficio -->
    <div class="oficio-number">
        OFICIO: {{ $oficioEntrada->folio_completo }}.
    </div>

    <!-- Asunto -->
    <div class="asunto">
        ASUNTO: Material Recibido.
    </div>

    <!-- Destinatario -->
    <div class="destinatario">
        <div class="destinatario-line"><strong>LIC. MAURICIO IVÁN LÓPEZ MUÑIZ.</strong></div>
        <div class="destinatario-line"><strong>DIRECTOR DE RECURSOS MATERIALES</strong></div>
        <div class="destinatario-line"><strong>DE LA FISCALÍA ESTATAL.</strong></div>
        <div class="destinatario-line presente"><strong>P R E S E N T E.</strong></div>
    </div>

    <!-- Fecha y lugar -->
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

    <!-- Cuerpo del oficio -->
    <div class="cuerpo">
        <p>
            Aunado a un cordial saludo me permito informarle a Usted que se recibieron, {{ $oficioEntrada->descripcion_material }}. el día 
            @php
                $fechaRecepcion = $oficioEntrada->fecha_recepcion;
                $diaRecepcion = str_pad($fechaRecepcion->format('d'), 2, '0', STR_PAD_LEFT);
                $mesRecepcion = strtoupper($meses[(int)$fechaRecepcion->format('m')]);
            @endphp
            {{ $diaRecepcion }} DE {{ $mesRecepcion }} DEL PRESENTE AÑO, del proveedor denominado {{ strtoupper($oficioEntrada->proveedor_nombre) }}, con la Factura No. {{ $oficioEntrada->numero_factura }}, por un importe total de ${{ number_format($oficioEntrada->importe_total, 2, '.', ',') }} ({{ strtoupper($oficioEntrada->importe_total_letra) }})
        </p>

        <p>
            Sin otro particular de momento, me despido quedando a sus órdenes para cualquier duda o comentario.
        </p>
    </div>

    <!-- Firma -->
    <div class="firma-section">
        <div class="firma-atentamente">
            Atentamente
        </div>
        
        <div style="margin-top: 50px;"></div>
        
        <div class="firma-nombre">
            LIC. SANDRA ESMERALDA QUEZADA AGUIAR.
        </div>
        <div class="firma-cargo">
            JEFE "A" DE UNIDAD DEPARTAMENTAL, ENCARGADA
        </div>
        <div class="firma-cargo">
            DEL ALMACÉN GENERAL DE LA FISCALÍA ESTATAL.
        </div>
    </div>

    <!-- Copias -->
    <div class="copia-section">
        <div class="copia-line">
            <strong>C. c. p.</strong> L. C. P. Manuel Vázquez Rodríguez - Director General Administrativo de la Fiscalía del Estado.
        </div>
        <div class="copia-line">
            <strong>C. c. p.</strong> C. Carlos Gerardo Flores Gámez. – Encargado del Área de Adquisiciones.
        </div>
        <div style="margin-top: 10px; font-size: 10pt;">
            SEQA/ms♫
        </div>
    </div>

    <!-- Footer -->
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
