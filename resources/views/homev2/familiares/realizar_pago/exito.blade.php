@extends('layouts.dashboard')

@section('title', '¡Pago Registrado!')

@section('styles')
<style>
    .success-icon {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem;
        animation: scaleIn 0.5s ease;
    }

    @keyframes scaleIn {
        from { transform: scale(0); }
        to { transform: scale(1); }
    }

    .success-checkmark {
        font-size: 3rem;
        color: white;
    }

    .deuda-pagada {
        padding: 0.75rem;
        background: #f8f9fa;
        margin-bottom: 0.5rem;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
    }
</style>

<div class="p-8 m-4 bg-gray-100 dark:bg-white/[0.03] rounded-2xl">
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="success-icon">
                        <i class="fas fa-check success-checkmark"></i>
                    </div>

                    <h2 class="mb-3">¡Pago Registrado Exitosamente!</h2>
                    <p class="text-muted mb-4">Tu solicitud de pago ha sido procesada correctamente</p>

                    <div class="alert alert-success">
                        <h4>Recibo Nº: {{ $transaccion->nro_recibo }}</h4>
                        <p class="mb-0">Fecha: {{ \Carbon\Carbon::parse($transaccion->fecha_transaccion)->format('d/m/Y H:i') }}</p>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Detalles del Pago</h5>
                        </div>
                        <div class="card-body text-start">
                            <div class="mb-3">
                                <strong>Orden de Pago:</strong> {{ $transaccion->orden->codigo_orden }}
                            </div>
                            <div class="mb-3">
                                <strong>Alumno:</strong>
                                {{ $transaccion->orden->alumno->primer_nombre }}
                                {{ $transaccion->orden->alumno->apellido_paterno }}
                            </div>
                            <div class="mb-3">
                                <strong>Método de Pago:</strong> {{ strtoupper($transaccion->metodo_pago) }}
                            </div>
                            <div class="mb-3">
                                <strong>Monto Pagado:</strong>
                                <span class="text-success fs-4">S/. {{ number_format($transaccion->monto, 2) }}</span>
                            </div>

                            <hr>

                            <h6 class="mb-3">Conceptos Pagados:</h6>
                            @foreach($transaccion->orden->detalles as $detalle)
                                <div class="deuda-pagada">
                                    <span>{{ $detalle->conceptoPago->descripcion ?? 'N/A' }}</span>
                                    <strong>S/. {{ number_format($detalle->monto_subtotal, 2) }}</strong>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Estado:</strong> Pendiente de validación<br>
                        <small>El personal del colegio validará tu pago en las próximas horas.</small>
                    </div>

                    <a href="{{ route('familiar_pago_pago_realizar_index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus-circle me-2"></i>Realizar Otro Pago
                    </a>

                    <a href="{{ route('familiar_pago_view_deudas') }}" class="btn btn-outline-secondary btn-lg mt-2">
                        <i class="fas fa-arrow-left me-2"></i>Ver Mis Deudas
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
