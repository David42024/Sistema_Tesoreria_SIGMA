<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Constancia de Matrícula</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
        }

        @font-face {
            font-family: 'Arial';
            src: url('{{ str_replace('\\', '/', 'file:///' . storage_path('fonts/arial-unicode.ttf')) }}') format('truetype');
        }

        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
            width: 100%;
            height: 100%;
        }

        .document-container {
            width: 210mm; /* Force A4 width explicitly */
            /* A4 height in mm is 297mm. Using min-height ensures footer is at bottom. */
            min-height: 297mm; 
            padding: 20mm 25mm; /* Standard margins: Top/Bottom 20mm, Left/Right 25mm */
            position: relative;
            background-color: white;
            overflow: hidden; /* Prevent spillover */
        }

        .operator-watermark {
            position: absolute;
            left: 5mm;
            bottom: 30mm;
            transform: rotate(-90deg);
            transform-origin: left bottom;
            font-size: 9px;
            color: #ccc;
            white-space: nowrap;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header-top {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .college-name {
            font-size: 28px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 5px 0;
            color: #000;
        }

        .divider {
            border-bottom: 3px solid #000;
            margin: 15px 0;
            width: 100%;
        }

        .title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            text-decoration: underline;
            margin: 30px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .content {
            font-size: 16px;
            line-height: 1.6;
            text-align: justify;
            margin-bottom: 40px;
        }

        .student-name {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 18px;
            text-align: center;
            display: block;
            margin: 15px 0;
        }

        .academic-info {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #000;
            border-radius: 4px;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
            border-bottom: 1px dotted #ccc;
            padding-bottom: 4px;
        }
        
        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .label {
            display: table-cell;
            font-weight: bold;
            width: 150px;
            vertical-align: top;
        }

        .value {
            display: table-cell;
            font-weight: normal;
            vertical-align: top;
        }

        .closing {
            margin-top: 30px;
            text-indent: 30px;
        }

        .date {
            text-align: right;
            margin-top: 40px;
            font-size: 16px;
        }

        .signature-section {
            margin-top: 80px;
            text-align: center;
            width: 100%;
        }

        .signature-box {
            display: inline-block;
            text-align: center;
        }

        .signature-line {
            width: 250px;
            border-top: 1px solid #000;
            margin-bottom: 5px;
        }

        .footer {
            position: absolute;
            bottom: 15mm;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #444;
            border-top: 1px solid #aaa;
            padding-top: 10px;
            padding-left: 25mm;
            padding-right: 25mm;
        }
    </style>
</head>

<body>
    <div class="document-container">

        <div class="operator-watermark">Generado por {{ $operator_name }} ({{ $operator_username }}) el
            {{ now()->format('d/m/Y \a \l\a\s H:i:s') }}.
        </div>

        <div class="header">
            <div class="header-top">Ministerio de Educación</div>
            <div class="college-name">Institución Educativa SIGMA</div>
            <div class="divider"></div>
        </div>

        <div class="title">CONSTANCIA DE MATRÍCULA</div>

        <div class="content">
            <p>
                La Dirección de la Institución Educativa Privada <strong>SIGMA</strong>, hace constar por el presente
                documento que:
            </p>

            <span class="student-name">{{ $student_name }}</span>

            <p>
                Identificado(a) con DNI N° <strong>{{ $student_dni }}</strong>, culminó satisfactoriamente su
                proceso de matrícula correspondiente al Año Académico <strong>{{ $year }}</strong>
                en nuestra institución, quedando registrado con los siguientes datos:
            </p>

            <div class="academic-info">
                <div class="info-row">
                    <span class="label">Nivel Educativo:</span>
                    <span class="value">{{ $level }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Grado:</span>
                    <span class="value">{{ $grade }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Sección:</span>
                    <span class="value">{{ $section }}</span>
                </div>
            </div>

            <p class="closing">
                Se expide la presente a solicitud de la parte interesada para los fines que estime conveniente.
            </p>
        </div>

        <div class="date">
            Trujillo, {{ now()->format('d \d\e F \d\e\l Y') }}
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <strong>DIRECTOR(A)</strong><br>
                <span>Sello y Firma</span>
            </div>
        </div>
        <br>
        <div class="footer">
            Av. Juan Pablo II S/N Urb. San Andrés, Trujillo - La Libertad | Teléfono: (44) 538970 |
            colegiosigma.edu.pe
            <br>
            Este documento fue generado automáticamente por el Sistema de Gestión Académica.
        </div>
    </div>
</body>

</html>