@extends('layouts.pasarela')

@section('title', 'Comprobante de Pago')

@section('styles')
<style>
    .comprobante-container {
        background: white;
        border-radius: 15px;
        overflow: hidden;
    }

    .comprobante-header {
        background: linear-gradient(135deg, #27ae60, #229954);
        color: white;
        padding: 3rem 2rem;
        text-align: center;
    }

    .checkmark {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: white;
        color: #27ae60;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        margin-bottom: 1rem;
        animation: scaleIn 0.5s ease-out;
    }

    @keyframes scaleIn {
        from { transform: scale(0); }
        to { transform: scale(1); }
    }

    .numero-operacion {
        font-size: 2rem;
        font-weight: 700;
        letter-spacing: 3px;
        background: rgba(255, 255, 255, 0.2);
        padding: 1rem;
        border-radius: 10px;
        margin-top: 1rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin: 2rem 0;
    }

    .info-box {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 10px;
        border-left: 4px solid var(--secondary-color);
    }

    .info-box .label {
        font-size: 0.85rem;
        color: #666;
        margin-bottom: 0.5rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .info-box .value {
        font-size: 1.1rem;
        color: #2c3e50;
        font-weight: 700;
    }

    .conceptos-detalle {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 10px;
    }

    .concepto-item {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px dashed #dee2e6;
    }

    .concepto-item:last-child {
        border-bottom: none;
    }

    .total-final {
        background: var(--primary-color);
        color: white;
        padding: 1.5rem;
        border-radius: 10px;
        margin-top: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .estado-badge {
        display: inline-block;
        padding: 0.5rem 1.5rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1rem;
    }

    .estado-pendiente {
        background: #fff3cd;
        color: #856404;
    }

    .estado-validado {
        background: #d4edda;
        color: #155724;
    }

    .accion-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    @media print {
        body * {
            visibility: hidden;
        }
        .comprobante-container, .comprobante-container * {
            visibility: visible;
        }
        .comprobante-container {
            position: absolute;
            left: 0;
            top: 0;
        }
        .accion-buttons {
            display: none !important;
        }
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-10">
        <div class="comprobante-container card">
            <!-- Header de Éxito -->
            <div class="comprobante-header">
                <div class="checkmark">
                    <i class="fas fa-check"></i>
                </div>
                <h1 class="mb-3">¡Pago Registrado Exitosamente!</h1>
                <p class="mb-0">Tu pago está siendo validado por nuestro equipo</p>
                <div class="numero-operacion">
                    N° {{ $transaccion->numero_operacion }}
                </div>
            </div>

            <div class="card-body p-4">
                <!-- Información Principal -->
                <div class="info-grid">
                    <div class="info-box">
                        <div class="label"><i class="fas fa-calendar me-1"></i>Fecha de Pago</div>
                        <div class="value">{{ $transaccion->fecha_transaccion->format('d/m/Y H:i') }}</div>
                    </div>

                    <div class="info-box">
                        <div class="label"><i class="fas fa-barcode me-1"></i>Código de Orden</div>
                        <div class="value">{{ $orden->codigo_orden }}</div>
                    </div>

                    <div class="info-box">
                        <div class="label"><i class="fas fa-wallet me-1"></i>Método de Pago</div>
                        <div class="value">{{ strtoupper($transaccion->metodo_pago) }}</div>
                    </div>

                    <div class="info-box">
                        <div class="label"><i class="fas fa-flag me-1"></i>Estado</div>
                        <div class="value">
                            <span class="estado-badge estado-pendiente">
                                Pendiente de Validación
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Información del Estudiante -->
                <div class="mt-4">
                    <h5><i class="fas fa-user-graduate me-2"></i>Datos del Estudiante</h5>
                    <div class="info-grid">
                        <div class="info-box">
                            <div class="label">Nombre Completo</div>
                            <div class="value">{{ $orden->alumno->primer_nombre }} {{ $orden->alumno->otros_nombres }} {{ $orden->alumno->apellido_paterno }} {{ $orden->alumno->apellido_materno }}</div>
                        </div>

                        <div class="info-box">
                            <div class="label">DNI</div>
                            <div class="value">{{ $orden->alumno->dni }}</div>
                        </div>

                        @if($orden->matricula)
                        <div class="info-box">
                            <div class="label">Grado y Sección</div>
                            <div class="value">
                                {{ $orden->matricula->grado->nombre_grado ?? 'N/A' }} - Sección {{ $orden->matricula->nombreSeccion ?? 'N/A' }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Conceptos Pagados -->
                <div class="mt-4">
                    <h5><i class="fas fa-list-ul me-2"></i>Detalle de la Orden</h5>
                    <div class="conceptos-detalle">
                        @foreach($orden->detalles as $detalle)
                            <div class="concepto-item">
                                <span>{{ $detalle->deuda->conceptoPago->nombre }}</span>
                                <strong>S/ {{ number_format($detalle->monto_subtotal, 2) }}</strong>
                            </div>
                        @endforeach

                        <div class="total-final">
                            <span style="font-size: 1.2rem;">MONTO PAGADO</span>
                            <span style="font-size: 2rem; font-weight: 700;">S/ {{ number_format($transaccion->monto, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Información Adicional -->
                @if($transaccion->observaciones)
                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Observaciones:</strong> {{ $transaccion->observaciones }}
                </div>
                @endif

                <!-- Alert de Validación -->
                <div class="alert alert-warning mt-4">
                    <h6><i class="fas fa-clock me-2"></i>Proceso de Validación</h6>
                    <p class="mb-0">
                        Tu pago está siendo revisado por nuestro equipo de tesorería. 
                        Recibirás una confirmación por correo electrónico una vez validado. 
                        Este proceso puede tomar entre <strong>24 a 48 horas hábiles</strong>.
                    </p>
                </div>

                <!-- Botones de Descarga de Voucher -->
                <div class="voucher-download-section mt-4" style="background: #d1f2eb; border: 2px solid #1abc9c; border-radius: 10px; padding: 20px;">
                    <h6 style="color: #16a085; margin-bottom: 15px;">
                        <i class="fas fa-receipt me-2"></i>Descargar Voucher de Pago
                    </h6>
                    <p class="mb-3" style="color: #27ae60;">
                        El voucher es una constancia simulada de tu transacción. Descárgalo en el formato que prefieras:
                    </p>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('pasarela.voucher.pdf', $transaccion->id_transaccion) }}" 
                           class="btn btn-danger btn-lg">
                            <i class="fas fa-file-pdf me-2"></i>Descargar PDF
                        </a>
                        <button onclick="descargarVoucherPNG()" class="btn btn-primary btn-lg">
                            <i class="fas fa-download me-2"></i>Descargar PNG
                        </button>
                    </div>
                    <small class="text-muted d-block mt-3">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>PDF:</strong> Descarga directa del voucher. 
                        <strong>PNG:</strong> Descarga automática como imagen.
                    </small>
                </div>

                <div class="text-center mt-3">
                    <a href="{{ route('pasarela.index') }}" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-home me-2"></i>Realizar Otro Pago
                    </a>
                </div>

                <!-- Información de Contacto -->
                <div class="text-center mt-4 pt-4 border-top">
                    <h6>¿Necesitas ayuda?</h6>
                    <p class="text-muted mb-0">
                        <i class="fas fa-phone me-1"></i>(01) 234-5678 | 
                        <i class="fas fa-envelope me-1 ms-2"></i>tesoreria@sigma.edu.pe
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
function descargarVoucherPNG() {
    // Mostrar indicador de carga
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generando...';
    btn.disabled = true;

    // Crear iframe completamente oculto
    const iframe = document.createElement('iframe');
    iframe.style.cssText = 'position: fixed; left: -99999px; top: -99999px; width: 650px; height: 1000px; border: none; visibility: hidden; opacity: 0;';
    iframe.src = "{{ route('pasarela.voucher.html', $transaccion->id_transaccion) }}";
    document.body.appendChild(iframe);

    // Esperar a que el iframe cargue completamente
    iframe.onload = function() {
        try {
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            const voucherElement = iframeDoc.querySelector('.voucher-container');
            
            if (voucherElement) {
                // Capturar el voucher desde el iframe
                html2canvas(voucherElement, {
                    scale: 2,
                    backgroundColor: '#ffffff',
                    logging: false,
                    useCORS: true,
                    allowTaint: true,
                    width: voucherElement.scrollWidth,
                    height: voucherElement.scrollHeight
                }).then(canvas => {
                    // Convertir canvas a blob y descargar
                    canvas.toBlob(function(blob) {
                        const url = URL.createObjectURL(blob);
                        const link = document.createElement('a');
                        link.href = url;
                        link.download = 'voucher_{{ $transaccion->numero_operacion }}.png';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        URL.revokeObjectURL(url);
                        
                        // Limpiar iframe
                        document.body.removeChild(iframe);
                        
                        // Restaurar botón
                        btn.innerHTML = originalHTML;
                        btn.disabled = false;
                    });
                }).catch(error => {
                    console.error('Error al generar PNG:', error);
                    alert('Error al generar la imagen. Por favor, intenta nuevamente.');
                    document.body.removeChild(iframe);
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                });
            } else {
                alert('No se pudo cargar el voucher. Por favor, intenta nuevamente.');
                document.body.removeChild(iframe);
                btn.innerHTML = originalHTML;
                btn.disabled = false;
            }
        } catch(error) {
            console.error('Error al acceder al iframe:', error);
            alert('Error al procesar el voucher. Por favor, intenta nuevamente.');
            document.body.removeChild(iframe);
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        }
    };

    // Manejar error de carga
    iframe.onerror = function() {
        alert('Error al cargar el voucher. Por favor, intenta nuevamente.');
        document.body.removeChild(iframe);
        btn.innerHTML = originalHTML;
        btn.disabled = false;
    };
}
</script>
</script>
@endsection