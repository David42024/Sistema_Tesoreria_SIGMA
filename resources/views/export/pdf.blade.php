<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Exportar a PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 15px;
        }

        .header h1 {
            color: #4F46E5;
            margin: 0 0 10px 0;
            font-size: 24px;
        }

        .header h2 {
            color: #4F46E5;
            margin: 0 0 10px 0;
            font-size: 18px;
        }

        .info-section {
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #4F46E5;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Sistema SIGMA</h1>
        @if(!empty($title))
            <h2>{{ $title }}</h2>
        @endif
    </div>

    <div class="info-section">
        <p><strong>Total de registros:</strong> {{ count($data) }}</p>
        <p><strong>Fecha de generación:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    @include('export.table')

    <div class="footer">
        <p>Sistema de Gestión Académica SIGMA - Generado automáticamente</p>
    </div>
</body>

</html>