<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Traslado - {{ $nombre_completo }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
            padding: 40px 50px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }

        .logo {
            font-size: 16pt;
            font-weight: bold;
            color: #000;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header h1 {
            font-size: 14pt;
            font-weight: bold;
            color: #000;
            margin: 10px 0;
            text-transform: uppercase;
        }

        .header p {
            font-size: 10pt;
            color: #000;
            font-style: italic;
        }

        .codigo-solicitud {
            border: 2px solid #000;
            padding: 12px;
            text-align: center;
            margin: 25px 0;
            font-size: 12pt;
            font-weight: bold;
            background: #f5f5f5;
        }

        .estado-badge {
            display: inline-block;
            border: 1px solid #000;
            padding: 4px 12px;
            margin-left: 15px;
            font-size: 9pt;
            background: #fff;
        }

        .section {
            margin-bottom: 25px;
            padding: 15px;
            border: 1px solid #000;
        }

        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #000;
            margin-bottom: 15px;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            letter-spacing: 0.5px;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
            page-break-inside: avoid;
        }

        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 35%;
            color: #000;
            padding: 4px 0;
        }

        .info-value {
            display: table-cell;
            width: 65%;
            padding: 4px 0;
            color: #000;
        }

        .motivo-box {
            background: #fff;
            padding: 15px;
            border: 1px solid #000;
            margin-top: 10px;
            min-height: 100px;
            text-align: justify;
        }

        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #000;
            font-size: 9pt;
        }

        .signature-section {
            margin-top: 80px;
            page-break-inside: avoid;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            margin: 0 auto;
            margin-top: 70px;
        }

        .signature-label {
            margin-top: 8px;
            font-weight: bold;
            font-size: 10pt;
        }

        .fecha-generacion {
            text-align: right;
            font-size: 9pt;
            color: #000;
            margin-top: 20px;
            font-style: italic;
        }

        .importante {
            border: 2px solid #000;
            padding: 15px;
            margin: 25px 0;
            background: #f5f5f5;
        }

        .importante-title {
            font-weight: bold;
            color: #000;
            margin-bottom: 10px;
            text-transform: uppercase;
            font-size: 10pt;
        }

        .importante-text {
            color: #000;
            font-size: 10pt;
            line-height: 1.6;
            text-align: justify;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">SISTEMA DE TESORERIA</div>
        <h1>SOLICITUD DE TRASLADO DE ALUMNO</h1>
        <p>Institucion Educativa</p>
    </div>

    <!-- Código de Solicitud -->
    <div class="codigo-solicitud">
        CODIGO DE SOLICITUD: {{ $solicitud->codigo_solicitud }}
        <span class="estado-badge">{{ strtoupper($solicitud->estado) }}</span>
    </div>

    <!-- Información del Alumno -->
    <div class="section">
        <div class="section-title">INFORMACION DEL ALUMNO</div>

        <div class="info-row">
            <div class="info-label">Codigo de Educando:</div>
            <div class="info-value">{{ $alumno->codigo_educando }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Nombre Completo:</div>
            <div class="info-value">{{ $nombre_completo }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">DNI:</div>
            <div class="info-value">{{ $alumno->dni ?? 'No registrado' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Grado Actual:</div>
            <div class="info-value">{{ $grado_actual }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Fecha de Nacimiento:</div>
            <div class="info-value">{{ $alumno->fecha_nacimiento ? \Carbon\Carbon::parse($alumno->fecha_nacimiento)->format('d/m/Y') : 'No registrado' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Direccion:</div>
            <div class="info-value">{{ $alumno->direccion ?? 'No registrado' }}</div>
        </div>
    </div>

    <!-- Información del Traslado -->
    <div class="section">
        <div class="section-title">INFORMACION DEL TRASLADO</div>

        <div class="info-row">
            <div class="info-label">Colegio de Destino:</div>
            <div class="info-value">{{ $solicitud->colegio_destino }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Direccion del Colegio:</div>
            <div class="info-value">{{ $solicitud->direccion_nuevo_colegio ?? 'No especificado' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Telefono de Contacto:</div>
            <div class="info-value">{{ $solicitud->telefono_nuevo_colegio ?? 'No especificado' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Fecha de Traslado:</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($solicitud->fecha_traslado)->format('d/m/Y') }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Fecha de Solicitud:</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($solicitud->fecha_solicitud)->format('d/m/Y H:i:s') }}</div>
        </div>
    </div>

    <!-- Motivo del Traslado -->
    <div class="section">
        <div class="section-title">MOTIVO DEL TRASLADO</div>
        <div class="motivo-box">
            {{ $solicitud->motivo_traslado }}
        </div>
    </div>

    <!-- Observaciones -->
    @if($solicitud->observaciones)
    <div class="section">
        <div class="section-title">OBSERVACIONES ADICIONALES</div>
        <div class="motivo-box">
            {{ $solicitud->observaciones }}
        </div>
    </div>
    @endif

    <!-- Información Importante -->
    <div class="importante">
        <div class="importante-title">INFORMACION IMPORTANTE</div>
        <div class="importante-text">
            Esta solicitud de traslado debe ser presentada junto con los siguientes documentos:<br><br>
            1. Copia del DNI del alumno y del padre o apoderado<br>
            2. Constancia de no adeudar de la institucion actual<br>
            3. Certificado de estudios original<br>
            4. Partida de nacimiento original<br>
            5. Ficha de matricula del colegio de destino<br><br>
            La solicitud sera revisada y procesada en un plazo maximo de 15 dias habiles.
        </div>
    </div>

    <!-- Firmas -->
    <div class="signature-section">
        <table>
            <tr>
                <td style="width: 50%; text-align: center;">
                    <div class="signature-line"></div>
                    <div class="signature-label">PADRE/MADRE/APODERADO</div>
                    <div style="font-size: 9pt; color: #000; margin-top: 3px;">Firma y DNI</div>
                </td>
                <td style="width: 50%; text-align: center;">
                    <div class="signature-line"></div>
                    <div class="signature-label">DIRECCION ACADEMICA</div>
                    <div style="font-size: 9pt; color: #000; margin-top: 3px;">Firma y Sello</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p style="text-align: center; font-weight: bold;">
            Institucion Educativa - Sistema de Tesoreria
        </p>
        <p style="text-align: center; font-size: 8pt; margin-top: 5px;">
            Este documento es valido con la firma y sello de la institucion
        </p>
    </div>

    <!-- Fecha de Generación -->
    <div class="fecha-generacion">
        Documento generado el: {{ $fecha_generacion }}
    </div>
</body>
</html>
