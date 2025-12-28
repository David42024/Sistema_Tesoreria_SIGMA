@extends('layouts.pasarela')

@section('title', 'Ingresar Código de Orden')

@section('styles')
<style>
    .hero-section {
        text-align: center;
        color: white;
        margin-bottom: 3rem;
    }

    .hero-section h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .hero-section p {
        font-size: 1.2rem;
        opacity: 0.95;
    }

    .icon-xl {
        font-size: 4rem;
        margin-bottom: 1rem;
        color: var(--secondary-color);
    }

    .info-cards {
        margin-top: 2rem;
    }

    .info-card {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        height: 100%;
    }

    .info-card i {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }

    .info-card.yape i { color: #722282; }
    .info-card.transferencia i { color: #0066cc; }
    .info-card.tarjeta i { color: #ff6600; }
    .info-card.paypal i { color: #003087; }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-8">
        <!-- Hero Section -->
        <div class="hero-section">
            <i class="fas fa-credit-card icon-xl"></i>
            <h1>Paga tu Orden de Forma Segura</h1>
            <p>Ingresa el código de tu orden de pago para continuar</p>
        </div>

        <!-- Form Card -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-barcode me-2"></i>Ingresar Código de Orden</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('pasarela.orden', ['codigo_orden' => 'TEMP']) }}" method="GET" id="codigoForm">
                    <div class="mb-4">
                        <label for="codigo_orden" class="form-label">
                            <i class="fas fa-hashtag me-1"></i>Código de Orden de Pago
                        </label>
                        <input 
                            type="text" 
                            class="form-control form-control-lg text-center" 
                            id="codigo_orden" 
                            name="codigo_orden" 
                            placeholder="Ejemplo: OP-2025-0034"
                            value="{{ old('codigo_orden') }}"
                            required
                            style="font-size: 1.5rem; font-weight: 600; letter-spacing: 2px;"
                        >
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            El código de orden fue enviado a tu correo electrónico o puedes encontrarlo en tu boleta de pago.
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-search me-2"></i>Buscar Orden de Pago
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Cards -->
        <div class="info-cards">
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="info-card yape">
                        <div style="display: flex; gap: 10px; justify-content: center; align-items: center; margin-bottom: 1rem;">
                            <img src="https://logosenvector.com/logo/img/yape-37283.png" alt="Yape" style="height: 40px; object-fit: contain;">
                            <span style="font-size: 1.5rem; font-weight: 600; color: #722282;">/</span>
                            <img src="https://marketingperu.beglobal.biz/wp-content/uploads/2024/09/logo-plin-fondo-transparente.png" alt="Plin" style="height: 40px; object-fit: contain;">
                        </div>
                        <h6>Yape / Plin</h6>
                        <small>Pago instantáneo</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="info-card transferencia">
                        <i class="fas fa-university"></i>
                        <h6>Transferencia</h6>
                        <small>Bancaria</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="info-card tarjeta">
                        <i class="fas fa-credit-card"></i>
                        <h6>Tarjeta</h6>
                        <small>Crédito / Débito</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="info-card paypal">
                        <i class="fab fa-paypal"></i>
                        <h6>PayPal</h6>
                        <small>Internacional</small>
                    </div>
                </div>
            </div>
        </div>
        

        <!-- Help Section -->
        <div class="card mt-4">
            <div class="card-body text-center">
                <h5><i class="fas fa-question-circle me-2"></i>¿Necesitas ayuda?</h5>
                <p class="mb-0">
                    Comunícate con nosotros al 
                    <strong>(01) 234-5678</strong> o envía un correo a 
                    <strong>tesoreria@sigma.edu.pe</strong>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Modify form action to include the entered code
    document.getElementById('codigoForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const codigo = document.getElementById('codigo_orden').value.trim();
        if (codigo) {
            window.location.href = `/pagar/${codigo}`;
        }
    });

    // Auto-format code input (uppercase, remove spaces)
    document.getElementById('codigo_orden').addEventListener('input', function(e) {
        this.value = this.value.toUpperCase().replace(/\s/g, '');
    });
</script>
@endsection
