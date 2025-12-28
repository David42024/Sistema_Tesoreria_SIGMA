<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher de Pago - {{ $numero_operacion }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica', 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .page-container {
            max-width: 650px;
            width: 100%;
        }

        .btn-download {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #27ae60;
            color: white;
            padding: 15px 25px;
            border-radius: 50px;
            border: none;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 5px 20px rgba(39, 174, 96, 0.4);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .btn-download:hover {
            background: #229954;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(39, 174, 96, 0.5);
        }

        .btn-download:active {
            transform: translateY(0);
        }

        .voucher-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
        }

        /* YAPE */
        .voucher-yape .header {
            background: linear-gradient(135deg, #722282, #9b4fa8);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }

        /* PLIN */
        .voucher-plin .header {
            background: linear-gradient(135deg, #00C1A2, #00E6C0);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }

        /* TRANSFERENCIA */
        .voucher-transferencia .header {
            background: linear-gradient(135deg, #0066cc, #0099ff);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }

        /* TARJETA */
        .voucher-tarjeta .header {
            background: linear-gradient(135deg, #ff6600, #ff9933);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }

        /* PAYPAL */
        .voucher-paypal .header {
            background: linear-gradient(135deg, #003087, #0070ba);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }

        .header .logo {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .header .subtitle {
            font-size: 14px;
            opacity: 0.9;
        }

        .check-icon {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            margin: 20px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: #27ae60;
        }

        .content {
            padding: 30px 20px;
        }

        .status {
            text-align: center;
            margin-bottom: 30px;
        }

        .status h2 {
            color: #27ae60;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .status p {
            color: #666;
            font-size: 14px;
        }

        .amount-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            border: 2px solid #e0e0e0;
        }

        .amount-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .amount-value {
            font-size: 36px;
            font-weight: bold;
            color: #2c3e50;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-size: 13px;
            color: #888;
            font-weight: 500;
        }

        .info-value {
            font-size: 13px;
            color: #2c3e50;
            font-weight: 600;
            text-align: right;
        }

        .operation-number {
            background: #fffbea;
            border: 2px dashed #ffc107;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            margin: 20px 0;
        }

        .operation-number .label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .operation-number .value {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            letter-spacing: 2px;
            font-family: 'Courier New', monospace;
        }

        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
        }

        .footer p {
            font-size: 11px;
            color: #999;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <!-- Voucher -->
        <div class="voucher-container voucher-{{ $metodo }}">
        <!-- Header -->
        <div class="header">
            @if($metodo == 'yape')
                <img src="https://i.postimg.cc/0NXYG6gD/logo-Yape.png" alt="Yape" style="height: 80px; margin-bottom: 15px; object-fit: contain;">
            @elseif($metodo == 'plin')
                <img src="https://marketingperu.beglobal.biz/wp-content/uploads/2024/09/logo-plin-fondo-transparente.png" alt="Plin" style="height: 80px; margin-bottom: 15px; object-fit: contain;">
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
                <div class="check-icon">✓</div>
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
            <div class="info-row">
                <span class="info-label">Fecha y Hora</span>
                <span class="info-value">{{ $fecha->format('d/m/Y H:i') }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Destinatario</span>
                <span class="info-value">Institución Educativa SIGMA</span>
            </div>

            @if(isset($datos_adicionales['celular']))
            <div class="info-row">
                <span class="info-label">Número {{ strtoupper($metodo) }}</span>
                <span class="info-value">+51 {{ $datos_adicionales['celular'] }}</span>
            </div>
            @endif

            @if(isset($datos_adicionales['banco']))
            <div class="info-row">
                <span class="info-label">Banco Origen</span>
                <span class="info-value">{{ $datos_adicionales['banco'] }}</span>
            </div>
            @endif

            @if(isset($datos_adicionales['numero_cuenta_origen']))
            <div class="info-row">
                <span class="info-label">Cuenta Origen</span>
                <span class="info-value">{{ $datos_adicionales['numero_cuenta_origen'] }}</span>
            </div>
            @endif

            @if(isset($datos_adicionales['ultimos_4_digitos']))
            <div class="info-row">
                <span class="info-label">Tarjeta</span>
                <span class="info-value">**** **** **** {{ $datos_adicionales['ultimos_4_digitos'] }}</span>
            </div>
            @endif

            @if(isset($datos_adicionales['email']))
            <div class="info-row">
                <span class="info-label">Email PayPal</span>
                <span class="info-value">{{ $datos_adicionales['email'] }}</span>
            </div>
            @endif

            <div class="info-row">
                <span class="info-label">Orden de Pago</span>
                <span class="info-value">{{ $codigo_orden }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Alumno</span>
                <span class="info-value">{{ $nombre_alumno }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Grado y Sección</span>
                <span class="info-value">{{ $grado }} - Sección {{ $seccion }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Este comprobante es válido como constancia de pago.<br>
            Generado el {{ now()->format('d/m/Y H:i:s') }}<br>
            Institución Educativa SIGMA<br>
            <strong>ID Transacción:</strong> {{ $pago_id }}</p>
        </div>
    </div>
    </div>

    <!-- Botón flotante para descargar (solo si NO está en iframe) -->
    <button class="btn-download" onclick="descargarComoImagen()" id="btnDescargar">
        📥 Descargar como PNG
    </button>

    <!-- Script html2canvas desde CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        // Ocultar botón si está en iframe
        if (window.self !== window.top) {
            const btn = document.getElementById('btnDescargar');
            if (btn) btn.style.display = 'none';
        }

        function descargarComoImagen() {
            const btn = document.querySelector('.btn-download');
            btn.textContent = '⏳ Generando...';
            btn.disabled = true;

            // Capturar solo el voucher
            const voucher = document.querySelector('.voucher-container');
            
            html2canvas(voucher, {
                backgroundColor: '#ffffff',
                scale: 2, // Mayor calidad
                logging: false,
                width: voucher.offsetWidth,
                height: voucher.offsetHeight
            }).then(canvas => {
                // Convertir a blob y descargar
                canvas.toBlob(blob => {
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.download = 'voucher_{{ $numero_operacion }}.png';
                    link.href = url;
                    link.click();
                    URL.revokeObjectURL(url);

                    // Restaurar botón
                    btn.textContent = '✅ ¡Descargado!';
                    setTimeout(() => {
                        btn.textContent = '📥 Descargar como PNG';
                        btn.disabled = false;
                    }, 2000);
                }, 'image/png');
            }).catch(error => {
                console.error('Error al generar imagen:', error);
                btn.textContent = '❌ Error';
                setTimeout(() => {
                    btn.textContent = '📥 Descargar como PNG';
                    btn.disabled = false;
                }, 2000);
            });
        }

        // También permitir descarga con Ctrl+S
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                descargarComoImagen();
            }
        });
    </script>
</body>
</html>
