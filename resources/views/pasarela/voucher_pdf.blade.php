<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Voucher de Pago</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #ffffff;
            padding: 10px;
        }

        .voucher-container {
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            background: white;
        }

        .header {
            padding: 20px 15px;
            text-align: center;
            color: white;
        }

        .voucher-yape .header {
            background: #722282;
        }

        .voucher-plin .header {
            background: #00C1A2;
        }

        .voucher-transferencia .header {
            background: #0066cc;
        }

        .voucher-tarjeta .header {
            background: #ff6600;
        }

        .voucher-paypal .header {
            background: #003087;
        }

        .header .logo {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .header .subtitle {
            font-size: 12px;
            opacity: 0.9;
        }

        .check-icon {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 50%;
            margin: 10px auto;
            line-height: 50px;
            font-size: 30px;
            color: #27ae60;
            text-align: center;
        }

        .content {
            padding: 15px;
        }

        .status {
            text-align: center;
            margin-bottom: 15px;
        }

        .status h2 {
            color: #27ae60;
            font-size: 18px;
            margin-bottom: 5px;
        }

        .status p {
            color: #666;
            font-size: 11px;
        }

        .amount-box {
            background: #f8f9fa;
            padding: 12px;
            text-align: center;
            margin-bottom: 12px;
            border: 2px solid #e0e0e0;
        }

        .amount-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .amount-value {
            font-size: 26px;
            font-weight: bold;
            color: #2c3e50;
        }

        .info-row {
            padding: 6px 0;
            border-bottom: 1px solid #f0f0f0;
            overflow: hidden;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-size: 10px;
            color: #888;
            font-weight: 500;
            float: left;
        }

        .info-value {
            font-size: 10px;
            color: #2c3e50;
            font-weight: 600;
            float: right;
        }

        .operation-number {
            background: #fffbea;
            border: 2px dashed #ffc107;
            padding: 10px;
            text-align: center;
            margin: 12px 0;
        }

        .operation-number .label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .operation-number .value {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            letter-spacing: 2px;
            font-family: 'Courier New', monospace;
        }

        .footer {
            background: #f8f9fa;
            padding: 12px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
            margin-top: 15px;
        }

        .footer p {
            font-size: 9px;
            color: #999;
            line-height: 1.4;
            margin: 2px 0;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <div class="voucher-container voucher-{{ $metodo }}">
        <!-- Header -->
        <div class="header">
            @if($metodo == 'yape')
                <img src="https://i.postimg.cc/0NXYG6gD/logo-Yape.png" alt="Yape" style="height: 60px; margin-bottom: 10px;">
            @elseif($metodo == 'plin')
                <img src="https://marketingperu.beglobal.biz/wp-content/uploads/2024/09/logo-plin-fondo-transparente.png" alt="Plin" style="height: 60px; margin-bottom: 10px;">
            @else
                <div class="logo">
                    @if($metodo == 'transferencia')
                        {{ strtoupper($datos_adicionales['banco'] ?? 'BANCO') }}
                    @elseif($metodo == 'tarjeta')
                        VISA/MASTERCARD
                    @elseif($metodo == 'paypal')
                        PayPal
                    @endif
                </div>
            @endif
            <div class="subtitle">Comprobante de Operación</div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="status">
                <h2>¡Pago Exitoso!</h2>
                <p>Tu transacción se realizó correctamente</p>
            </div>

            <!-- Monto -->
            <div class="amount-box">
                <div class="amount-label">Monto Pagado</div>
                <div class="amount-value">S/ {{ number_format($monto, 2) }}</div>
            </div>

            <!-- Número de Operación -->
            <div class="operation-number">
                <div class="label">Número de Operación</div>
                <div class="value">{{ $numero_operacion }}</div>
            </div>

            <!-- Detalles -->
            <div class="info-row clearfix">
                <span class="info-label">Fecha y Hora</span>
                <span class="info-value">{{ $fecha->format('d/m/Y H:i') }}</span>
            </div>

            <div class="info-row clearfix">
                <span class="info-label">Destinatario</span>
                <span class="info-value">Institución SIGMA</span>
            </div>

            @if(isset($datos_adicionales['celular']))
            <div class="info-row clearfix">
                <span class="info-label">Número {{ strtoupper($metodo) }}</span>
                <span class="info-value">+51 {{ $datos_adicionales['celular'] }}</span>
            </div>
            @endif

            @if(isset($datos_adicionales['banco']))
            <div class="info-row clearfix">
                <span class="info-label">Banco Origen</span>
                <span class="info-value">{{ $datos_adicionales['banco'] }}</span>
            </div>
            @endif

            @if(isset($datos_adicionales['numero_cuenta_origen']))
            <div class="info-row clearfix">
                <span class="info-label">Cuenta Origen</span>
                <span class="info-value">{{ $datos_adicionales['numero_cuenta_origen'] }}</span>
            </div>
            @endif

            @if(isset($datos_adicionales['ultimos_4_digitos']))
            <div class="info-row clearfix">
                <span class="info-label">Tarjeta</span>
                <span class="info-value">**** {{ $datos_adicionales['ultimos_4_digitos'] }}</span>
            </div>
            @endif

            @if(isset($datos_adicionales['email']))
            <div class="info-row clearfix">
                <span class="info-label">Email PayPal</span>
                <span class="info-value">{{ $datos_adicionales['email'] }}</span>
            </div>
            @endif

            <div class="info-row clearfix">
                <span class="info-label">Orden de Pago</span>
                <span class="info-value">{{ $codigo_orden }}</span>
            </div>

            <div class="info-row clearfix">
                <span class="info-label">Alumno</span>
                <span class="info-value">{{ $nombre_alumno }}</span>
            </div>

            <div class="info-row clearfix">
                <span class="info-label">Grado y Sección</span>
                <span class="info-value">{{ $grado }} - Sección {{ $seccion }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Este comprobante es válido como constancia de pago.</p>
            <p>Generado el {{ now()->format('d/m/Y H:i:s') }}</p>
            <p>Institución Educativa SIGMA</p>
            <p><strong>ID Transacción:</strong> {{ $pago_id }}</p>
        </div>
    </div>
</body>
</html>
