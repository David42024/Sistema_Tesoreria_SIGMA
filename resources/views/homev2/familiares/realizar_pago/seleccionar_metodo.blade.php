<style>
    .metodo-pago-card {
        border: 2px solid #e0e0e0;
        border-radius: 15px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        height: 100%;
        text-decoration: none;
        display: block;
    }

    .metodo-pago-card:hover {
        border-color: var(--secondary-color);
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(52, 152, 219, 0.2);
    }

    .metodo-pago-card i {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .metodo-pago-card h5 {
        color: #2c3e50;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .metodo-pago-card small {
        color: #666;
    }

    .metodo-yape i { color: #722282; }
    .metodo-plin i { color: #00C1A2; }
    .metodo-transferencia i { color: #0066cc; }
    .metodo-tarjeta i { color: #ff6600; }
    .metodo-paypal i { color: #003087; }

    .deuda-item {
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
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('familiar_pago_view_deudas') }}">Deudas</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('familiar_pago_pago_realizar_index') }}">Realizar Pago</a></li>
                    <li class="breadcrumb-item active">Seleccionar Método</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-8 mx-auto">
            <!-- Resumen de Deudas Seleccionadas -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-list-check me-2"></i>Resumen de Deudas Seleccionadas</h5>
                </div>
                <div class="card-body">
                    @foreach($deudasSeleccionadas as $deuda)
                        <div class="deuda-item">
                            <span>{{ $deuda->conceptoPago->descripcion ?? 'N/A' }}</span>
                            <strong>S/. {{ number_format($deuda->monto_total, 2) }}</strong>
                        </div>
                    @endforeach

                    <div class="mt-3 pt-3 border-top">
                        <div class="d-flex justify-content-between">
                            <h5>Total a Pagar:</h5>
                            <h5 class="text-primary">S/. {{ number_format($montoTotal, 2) }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selección de Método de Pago -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-wallet me-2"></i>Selecciona tu Método de Pago</h4>
                </div>
                <div class="card-body">
                    <p class="text-center mb-4">Elige cómo deseas realizar tu pago de forma segura</p>

                    <div class="row g-3">
                        <!-- Yape -->
                        <div class="col-6 col-md-4">
                            <a href="{{ route('familiar_pago_pago_realizar_formulario', ['metodo' => 'yape']) }}" class="metodo-pago-card metodo-yape">
                                <img src="https://logosenvector.com/logo/img/yape-37283.png" alt="Yape" style="height: 60px; object-fit: contain; margin-bottom: 1rem;">
                                <h5>Yape</h5>
                                <small>Instantáneo</small>
                            </a>
                        </div>

                        <!-- Plin -->
                        <div class="col-6 col-md-4">
                            <a href="{{ route('familiar_pago_pago_realizar_formulario', ['metodo' => 'plin']) }}" class="metodo-pago-card metodo-plin">
                                <img src="https://marketingperu.beglobal.biz/wp-content/uploads/2024/09/logo-plin-fondo-transparente.png" alt="Plin" style="height: 60px; object-fit: contain; margin-bottom: 1rem;">
                                <h5>Plin</h5>
                                <small>Instantáneo</small>
                            </a>
                        </div>

                        <!-- Transferencia -->
                        <div class="col-6 col-md-4">
                            <a href="{{ route('familiar_pago_pago_realizar_formulario', ['metodo' => 'transferencia']) }}" class="metodo-pago-card metodo-transferencia">
                                <i class="fas fa-university"></i>
                                <h5>Transferencia</h5>
                                <small>Bancaria</small>
                            </a>
                        </div>

                        <!-- Tarjeta -->
                        <div class="col-6 col-md-4">
                            <a href="{{ route('familiar_pago_pago_realizar_formulario', ['metodo' => 'tarjeta']) }}" class="metodo-pago-card metodo-tarjeta">
                                <i class="fas fa-credit-card"></i>
                                <h5>Tarjeta</h5>
                                <small>Crédito/Débito</small>
                            </a>
                        </div>

                        <!-- PayPal -->
                        <div class="col-6 col-md-4">
                            <a href="{{ route('familiar_pago_pago_realizar_formulario', ['metodo' => 'paypal']) }}" class="metodo-pago-card metodo-paypal">
                                <i class="fab fa-paypal"></i>
                                <h5>PayPal</h5>
                                <small>Internacional</small>
                            </a>
                        </div>

                        <!-- Volver -->
                        <div class="col-6 col-md-4">
                            <a href="{{ route('familiar_pago_pago_realizar_index') }}" class="metodo-pago-card" style="border-color: #6c757d;">
                                <i class="fas fa-arrow-left" style="color: #6c757d;"></i>
                                <h5>Volver</h5>
                                <small>Cambiar deudas</small>
                            </a>
                        </div>
                    </div>

                    <!-- Security Info -->
                    <div class="text-center mt-4 pt-3 border-top">
                        <i class="fas fa-shield-alt text-success me-2"></i>
                        <small class="text-muted">Todos los pagos son procesados de forma segura y encriptada</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
