@extends('layouts.pasarela')

@section('title', 'Pagar con ' . ucfirst($metodo))

@section('styles')
<style>
    .metodo-logo {
        text-align: center;
        padding: 2rem;
        background: @if($metodo == 'yape') #722282 @else #00C1A2 @endif;
        color: white;
        border-radius: 15px 15px 0 0;
        margin: -2rem -2rem 2rem -2rem;
    }

    .metodo-logo i {
        font-size: 4rem;
        margin-bottom: 1rem;
    }

    .simulacion-box {
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        padding: 2rem;
        text-align: center;
        margin: 1.5rem 0;
    }

    .simulacion-box.activo {
        background: #d4edda;
        border-color: #28a745;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    .phone-input {
        font-size: 1.5rem;
        text-align: center;
        letter-spacing: 2px;
        font-weight: 600;
    }

    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
    }

    .step {
        flex: 1;
        text-align: center;
        padding: 1rem;
        background: #e9ecef;
        position: relative;
    }

    .step.active {
        background: var(--secondary-color);
        color: white;
    }

    .step.completed {
        background: var(--success-color);
        color: white;
    }

    .validation-message {
        padding: 0.75rem;
        border-radius: 8px;
        margin-top: 0.5rem;
        display: none;
        font-size: 0.9rem;
        animation: fadeIn 0.3s ease;
    }

    .validation-message.error {
        background: linear-gradient(135deg, #fee 0%, #fcc 100%);
        border-left: 4px solid #dc3545;
        color: #721c24;
        display: block;
    }

    .validation-message.success {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border-left: 4px solid #28a745;
        color: #155724;
        display: block;
    }

    .validation-message.warning {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border-left: 4px solid #ffc107;
        color: #856404;
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .form-control.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .form-control.is-valid {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-body">
                <!-- Logo con fondo de color -->
                <div style="text-align: center; padding: 3rem 2rem; background: #722282; border-radius: 15px 15px 0 0; margin: -2rem -2rem 2rem -2rem;">
                    <img src="https://i.postimg.cc/0NXYG6gD/logo-Yape.png" alt="Yape" style="height: 150px; object-fit: contain; margin-bottom: 1.5rem;">
                    <p style="margin: 0; color: white; font-size: 1.1rem; opacity: 0.95;">Pago rápido y seguro desde tu celular</p>
                </div>

                <!-- Orden Info -->
                <div class="alert alert-info">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Orden:</strong> {{ $orden->codigo_orden }}<br>
                            <strong>Alumno:</strong> {{ $orden->alumno->primer_nombre }} {{ $orden->alumno->apellido_paterno }}
                        </div>
                        <div class="text-end">
                            @if($montoPagado > 0)
                                <small class="text-muted">Pagado: S/ {{ number_format($montoPagado, 2) }}</small><br>
                                <h3 class="mb-0 text-primary">S/ {{ number_format($saldoPendiente, 2) }}</h3>
                                <small>Saldo Pendiente</small>
                            @else
                                <h3 class="mb-0">S/ {{ number_format($orden->monto_total, 2) }}</h3>
                                <small>Monto Total</small>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="step active">
                        <strong>1</strong><br>Ingresa los datos
                    </div>
                    <div class="step">
                        <strong>2</strong><br>Confirma el pago
                    </div>
                    <div class="step">
                        <strong>3</strong><br>Comprobante
                    </div>
                </div>

                <!-- Form -->
                <form action="{{ route('pasarela.procesar', $orden->codigo_orden) }}" method="POST" enctype="multipart/form-data" id="pagoForm">
                    @csrf
                    <input type="hidden" name="metodo_pago" value="{{ $metodo }}">

                    <!-- Paso 1: Número de celular y monto -->
                    <div id="paso1">
                        <h5><i class="fas fa-mobile-alt me-2"></i>Paso 1: Datos del pago</h5>
                        
                        <div class="mb-4">
                            <label for="celular" class="form-label">Número de celular registrado en {{ strtoupper($metodo) }}</label>
                            <input 
                                type="text" 
                                class="form-control phone-input" 
                                id="celular" 
                                name="celular" 
                                placeholder="999 999 999"
                                maxlength="9"
                                pattern="[9][0-9]{8}"
                                required
                            >
                            <div class="form-text">Debe empezar con 9 y tener 9 dígitos</div>
                            <div id="celular-validation" class="validation-message"></div>
                        </div>

                        <div class="mb-4">
                            <label for="monto_pago" class="form-label">Monto a Pagar</label>
                            <div class="input-group">
                                <span class="input-group-text">S/</span>
                                <input 
                                    type="number" 
                                    class="form-control form-control-lg text-end" 
                                    id="monto_pago" 
                                    name="monto_pago" 
                                    placeholder="0.00"
                                    step="0.01"
                                    min="1"
                                    max="{{ $saldoPendiente }}"
                                    required
                                    style="font-size: 1.5rem; font-weight: 600;"
                                >
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                @if($montoPagado > 0)
                                    <strong class="text-success">Pagado: S/ {{ number_format($montoPagado, 2) }}</strong> |
                                @endif
                                <strong class="text-primary">Saldo pendiente: S/ {{ number_format($saldoPendiente, 2) }}</strong>
                                <br>Puedes pagar el saldo completo o una parte.
                            </div>
                            <div id="monto-validation" class="validation-message"></div>
                        </div>

                        <button type="button" class="btn btn-primary btn-lg w-100" id="btnContinuar" onclick="validarYContinuar()" disabled>
                            <i class="fas fa-paper-plane me-2"></i>Enviar Solicitud de Pago
                        </button>
                    </div>

                    <!-- Paso 2: Simulación de envío -->
                    <div id="paso2" style="display: none;">
                        <div class="simulacion-box activo">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h4>¡Solicitud Enviada!</h4>
                            <p>Hemos enviado una solicitud de pago de <strong>S/ <span id="montoMostrar">0.00</span></strong> a tu app de {{ strtoupper($metodo) }}.</p>
                            <p>Por favor, <strong>abre tu app y confirma el pago</strong>.</p>
                        </div>
                        <button type="submit" class="btn btn-success btn-lg w-100">
                            <i class="fas fa-check-circle me-2"></i>Confirmar Pago y Generar Comprobante
                        </button>
                    </div>

                    <a href="{{ route('pasarela.orden', $orden->codigo_orden) }}" class="btn btn-outline-secondary mt-3 w-100">
                        <i class="fas fa-arrow-left me-2"></i>Volver a Métodos de Pago
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const montoMax = {{ $saldoPendiente }};
    let celularValido = false;
    let montoValido = false;

    function validarYContinuar() {
        if (!celularValido || !montoValido) return;

        const monto = parseFloat(document.getElementById('monto_pago').value);
        
        // Mostrar monto en el paso 2
        document.getElementById('montoMostrar').textContent = monto.toFixed(2);
        
        // Cambiar de paso
        document.getElementById('paso1').style.display = 'none';
        document.getElementById('paso2').style.display = 'block';
        document.querySelectorAll('.step')[1].classList.add('active');
        document.querySelectorAll('.step')[0].classList.add('completed');
    }

    function actualizarBotonContinuar() {
        const btn = document.getElementById('btnContinuar');
        if (celularValido && montoValido) {
            btn.disabled = false;
            btn.classList.remove('btn-secondary');
            btn.classList.add('btn-primary');
        } else {
            btn.disabled = true;
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-secondary');
        }
    }

    function mostrarValidacion(elementId, tipo, mensaje) {
        const elem = document.getElementById(elementId);
        elem.className = `validation-message ${tipo}`;
        
        let icono = '';
        if (tipo === 'error') icono = '<svg style="width: 1.25rem; height: 1.25rem; display: inline; margin-right: 0.5rem;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>';
        else if (tipo === 'success') icono = '<svg style="width: 1.25rem; height: 1.25rem; display: inline; margin-right: 0.5rem;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>';
        else if (tipo === 'warning') icono = '<svg style="width: 1.25rem; height: 1.25rem; display: inline; margin-right: 0.5rem;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>';
        
        elem.innerHTML = icono + mensaje;
    }

    function ocultarValidacion(elementId) {
        const elem = document.getElementById(elementId);
        elem.style.display = 'none';
    }

    // Validación de celular en tiempo real
    document.getElementById('celular').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
        
        const celular = this.value;
        const validationDiv = 'celular-validation';
        
        if (celular.length === 0) {
            this.classList.remove('is-valid', 'is-invalid');
            ocultarValidacion(validationDiv);
            celularValido = false;
        } else if (!celular.startsWith('9')) {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
            mostrarValidacion(validationDiv, 'error', 'El número debe empezar con 9');
            celularValido = false;
        } else if (celular.length < 9) {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
            mostrarValidacion(validationDiv, 'warning', `Faltan ${9 - celular.length} dígito(s)`);
            celularValido = false;
        } else if (celular.length === 9) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
            mostrarValidacion(validationDiv, 'success', '✓ Número de celular válido');
            celularValido = true;
        }
        
        actualizarBotonContinuar();
    });

    // Validación de monto en tiempo real
    document.getElementById('monto_pago').addEventListener('input', function(e) {
        const valor = this.value.trim();
        let monto = parseFloat(valor);
        const validationDiv = 'monto-validation';
        
        // Si está vacío o es cero
        if (!valor || valor === '' || valor === '0' || valor === '0.' || valor === '0.0' || valor === '0.00') {
            this.classList.remove('is-valid', 'is-invalid');
            ocultarValidacion(validationDiv);
            montoValido = false;
            actualizarBotonContinuar();
            return;
        }
        
        // Si no es un número válido o es menor o igual a 0
        if (isNaN(monto) || monto <= 0) {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
            mostrarValidacion(validationDiv, 'error', 'Ingresa un monto válido mayor a S/ 0.00');
            montoValido = false;
        } 
        // Si excede el máximo
        else if (monto > montoMax) {
            this.value = montoMax.toFixed(2);
            monto = montoMax;
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
            mostrarValidacion(validationDiv, 'warning', `Monto ajustado al saldo máximo disponible: S/ ${montoMax.toFixed(2)}`);
            montoValido = true;
        } 
        // Monto válido
        else {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
            
            if (Math.abs(monto - montoMax) < 0.01) { // Comparación con tolerancia
                mostrarValidacion(validationDiv, 'success', '✓ Pagarás el monto completo de la orden');
            } else {
                const restante = montoMax - monto;
                mostrarValidacion(validationDiv, 'success', `✓ Pago parcial aceptado. Quedará un saldo de S/ ${restante.toFixed(2)}`);
            }
            montoValido = true;
        }
        
        actualizarBotonContinuar();
    });

    // Validación cuando pierde el foco para formatear
    document.getElementById('monto_pago').addEventListener('blur', function() {
        if (this.value && !isNaN(parseFloat(this.value))) {
            this.value = parseFloat(this.value).toFixed(2);
        }
    });
</script>
@endsection
