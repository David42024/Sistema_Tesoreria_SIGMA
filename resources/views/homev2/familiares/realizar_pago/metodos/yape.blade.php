<style>
    .metodo-logo { background: #722282; color: white; padding: 2rem; border-radius: 10px; text-align: center; margin-bottom: 2rem; }
    .phone-input { font-size: 1.5rem; text-align: center; letter-spacing: 2px; font-weight: 600; }
</style>

<div class="p-8 m-4 bg-gray-100 dark:bg-white/[0.03] rounded-2xl">
    <div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="metodo-logo">
                        <img src="https://logosenvector.com/logo/img/yape-37283.png" alt="Yape" style="height: 100px; object-fit: contain;">
                        <h3 class="mt-3">Pago con Yape</h3>
                    </div>

                    <div class="alert alert-info">
                        <strong>Orden:</strong> {{ $orden->codigo_orden }}<br>
                        <strong>Alumno:</strong> {{ $alumno->primer_nombre }} {{ $alumno->apellido_paterno }}<br>
                        <h4 class="mt-2 mb-0">Total: S/. {{ number_format($orden->monto_total, 2) }}</h4>
                    </div>

                    <form action="{{ route('familiar_pago_pago_realizar_procesar') }}" method="POST" id="pagoForm">
                        @csrf
                        <input type="hidden" name="metodo_pago" value="yape">
                        <input type="hidden" name="monto" value="{{ $orden->monto_total }}">

                        <div class="mb-4">
                            <label class="form-label">Número de celular registrado en Yape</label>
                            <input type="text" class="form-control phone-input" name="numero_celular"
                                   placeholder="999 999 999" maxlength="9" pattern="[9][0-9]{8}" required>
                            <small class="text-muted">Debe empezar con 9 y tener 9 dígitos</small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-paper-plane me-2"></i>Enviar Solicitud de Pago
                        </button>

                        <a href="{{ route('familiar_pago_pago_realizar_metodo') }}" class="btn btn-outline-secondary mt-3 w-100">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    document.querySelector('input[name="numero_celular"]').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
</script>
