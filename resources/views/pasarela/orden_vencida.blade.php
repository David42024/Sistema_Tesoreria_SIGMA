@extends('layouts.pasarela')

@section('title', 'Orden de Pago Vencida')

@section('styles')
<style>
    .card-sin-padding {
        padding: 0 !important;
    }

    .vencida-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 3rem 2rem;
        text-align: center;
    }

    .titulo-con-icono {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .vencida-icon {
        font-size: 3.5rem;
        animation: bounce-alarm 1.5s ease-in-out 3;
        display: inline-block;
    }

    @keyframes bounce-alarm {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0) rotate(0deg);
        }
        10% {
            transform: translateY(-10px) rotate(-10deg);
        }
        30% {
            transform: translateY(-10px) rotate(10deg);
        }
        40% {
            transform: translateY(-5px) rotate(-5deg);
        }
        60% {
            transform: translateY(-5px) rotate(5deg);
        }
    }

    .vencida-header h2 {
        margin: 0;
        font-size: 2rem;
        font-weight: 700;
        display: inline-block;
    }

    .orden-codigo {
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: 2px;
        margin-top: 1.5rem;
        background: rgba(255, 255, 255, 0.2);
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        display: inline-block;
    }

    @media (max-width: 576px) {
        .vencida-icon {
            font-size: 2.5rem;
        }
        
        .vencida-header h2 {
            font-size: 1.5rem;
        }
    }

    .alert-vencida {
        background: #fff3cd;
        border-left: 5px solid #ffc107;
        padding: 1.5rem;
        border-radius: 10px;
        margin-bottom: 2rem;
    }

    .alert-vencida h4 {
        color: #856404;
        margin-top: 0;
        margin-bottom: 1rem;
        font-weight: 700;
    }

    .alert-vencida p {
        color: #856404;
        margin-bottom: 0.5rem;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e0e0e0;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #666;
    }

    .info-value {
        color: #2c3e50;
        font-weight: 600;
    }

    .monto-box {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 10px;
        text-align: center;
        margin: 1.5rem 0;
    }

    .monto-box .label {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 0.5rem;
    }

    .monto-box .valor {
        font-size: 2rem;
        font-weight: 700;
        color: #2c3e50;
    }

    .instrucciones-card {
        background: #e3f2fd;
        border-left: 5px solid #2196f3;
        padding: 1.5rem;
        border-radius: 10px;
        margin-top: 2rem;
    }

    .instrucciones-card h5 {
        color: #1565c0;
        margin-top: 0;
        margin-bottom: 1rem;
        font-weight: 700;
    }

    .instrucciones-card ol {
        color: #1565c0;
        margin-bottom: 0;
        padding-left: 1.5rem;
    }

    .instrucciones-card ol li {
        margin-bottom: 0.75rem;
    }

    .btn-accion {
        margin-top: 1.5rem;
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-8">
        <div class="card">
            <!-- Header con estado de vencida -->
            <div class="vencida-header">
                <div class="titulo-con-icono">
                    <i class="fas fa-clock vencida-icon"></i>
                    <h2>Orden de Pago Vencida</h2>
                </div>
                <div class="orden-codigo">{{ $orden->codigo_orden }}</div>
            </div>

            <div class="card-body card-sin-padding" style="padding: 2rem !important;">
                <!-- Alerta de vencimiento -->
                <div class="alert-vencida">
                    <h4><i class="fas fa-exclamation-triangle me-2"></i>Esta orden de pago ha vencido</h4>
                    <p><strong>Fecha de vencimiento:</strong> {{ \Carbon\Carbon::parse($orden->fecha_vencimiento)->format('d/m/Y') }}</p>
                    <p class="mb-0">No es posible procesar pagos con esta orden. Por favor, genera una nueva orden de pago para continuar.</p>
                </div>

                <!-- Información de la orden -->
                <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Información de la Orden</h5>
                
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-user me-2"></i>Estudiante:</span>
                    <span class="info-value">
                        @if($orden->alumno)
                            {{ $orden->alumno->primer_nombre }} {{ $orden->alumno->otros_nombres }} 
                            {{ $orden->alumno->apellido_paterno }} {{ $orden->alumno->apellido_materno }}
                        @else
                            No especificado
                        @endif
                    </span>
                </div>

                @if($orden->matricula)
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-graduation-cap me-2"></i>Grado y Sección:</span>
                    <span class="info-value">
                        {{ $orden->matricula->grado->nombre_grado ?? 'N/A' }} - 
                        {{ $orden->matricula->nombreSeccion ?? 'N/A' }}
                    </span>
                </div>
                @endif

                <div class="info-row">
                    <span class="info-label"><i class="fas fa-calendar-alt me-2"></i>Fecha de emisión:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($orden->fecha_emision)->format('d/m/Y') }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label"><i class="fas fa-calendar-times me-2"></i>Fecha de vencimiento:</span>
                    <span class="info-value text-danger">
                        {{ \Carbon\Carbon::parse($orden->fecha_vencimiento)->format('d/m/Y') }}
                        <span class="badge bg-danger ms-2">Vencida</span>
                    </span>
                </div>

                <!-- Montos -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="monto-box">
                            <div class="label">Monto Total</div>
                            <div class="valor">S/ {{ number_format((float)$orden->monto_total, 2) }}</div>
                        </div>
                    </div>
                    @if(isset($montoPagado) && $montoPagado > 0)
                    <div class="col-md-6">
                        <div class="monto-box" style="background: #e8f5e9;">
                            <div class="label">Monto Pagado</div>
                            <div class="valor" style="color: #2e7d32;">S/ {{ number_format((float)$montoPagado, 2) }}</div>
                        </div>
                    </div>
                    @endif
                </div>

                @if(isset($saldoPendiente) && $saldoPendiente > 0)
                <div class="monto-box" style="background: #ffebee; margin-top: 1rem;">
                    <div class="label">Saldo Pendiente</div>
                    <div class="valor" style="color: #c62828;">S/ {{ number_format((float)$saldoPendiente, 2) }}</div>
                </div>
                @endif

                <!-- Instrucciones -->
                <div class="instrucciones-card">
                    <h5><i class="fas fa-clipboard-list me-2"></i>¿Qué debo hacer?</h5>
                    <ol>
                        <li><strong>Contacta con la oficina de Tesorería</strong> para generar una nueva orden de pago actualizada.</li>
                        <li>Puedes llamar al teléfono <strong>(01) 234-5678</strong> o enviar un correo a <strong>tesoreria@sigma.edu.pe</strong></li>
                        <li>Una vez generada la nueva orden, recibirás un <strong>nuevo código de orden</strong> para realizar el pago.</li>
                        @if(isset($montoPagado) && $montoPagado > 0)
                        <li><strong>Nota:</strong> El monto ya abonado (S/ {{ number_format((float)$montoPagado, 2) }}) será considerado en tu nueva orden de pago.</li>
                        @endif
                    </ol>
                </div>

                <!-- Botones de acción -->
                <div class="d-grid gap-2 btn-accion">
                    <a href="{{ route('pasarela.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Inicio
                    </a>
                </div>

                <!-- Información de contacto -->
                <div class="card mt-4" style="background: #f5f5f5; border: none;">
                    <div class="card-body text-center">
                        <h6><i class="fas fa-phone-alt me-2"></i>Necesitas ayuda?</h6>
                        <p class="mb-0">
                            <strong>Teléfono:</strong> (01) 234-5678<br>
                            <strong>Email:</strong> tesoreria@sigma.edu.pe<br>
                            <strong>Horario:</strong> Lunes a Viernes de 8:00 AM a 5:00 PM
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Opcional: Si quieres agregar alguna funcionalidad adicional
    console.log('Vista de orden vencida cargada');
</script>
@endsection
