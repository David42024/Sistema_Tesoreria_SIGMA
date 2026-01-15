<div class="p-8 m-4 bg-gray-100 dark:bg-white/[0.03] rounded-2xl">
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-credit-card fa-4x text-warning mb-3"></i>
                        <h3>Pago con Tarjeta</h3>
                    </div>

                    <div class="alert alert-info">
                        <strong>Orden:</strong> {{ $orden->codigo_orden }}<br>
                        <strong>Alumno:</strong> {{ $alumno->primer_nombre }} {{ $alumno->apellido_paterno }}<br>
                        <h4 class="mt-2 mb-0">Total: S/. {{ number_format($orden->monto_total, 2) }}</h4>
                    </div>

                    <form action="{{ route('familiar_pago_pago_realizar_procesar') }}" method="POST">
                        @csrf
                        <input type="hidden" name="metodo_pago" value="tarjeta">
                        <input type="hidden" name="monto" value="{{ $orden->monto_total }}">

                        <div class="mb-3">
                            <label class="form-label">Número de Tarjeta</label>
                            <input type="text" class="form-control" name="numero_tarjeta"
                                   placeholder="1234 5678 9012 3456" maxlength="19" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nombre del Titular</label>
                            <input type="text" class="form-control" name="nombre_titular"
                                   placeholder="Como aparece en la tarjeta" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha de Vencimiento</label>
                                <input type="text" class="form-control" name="fecha_vencimiento"
                                       placeholder="MM/AA" maxlength="5" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">CVV</label>
                                <input type="text" class="form-control" name="cvv"
                                       placeholder="123" maxlength="3" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning btn-lg w-100">
                            <i class="fas fa-lock me-2"></i>Pagar de Forma Segura
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
    // Formatear número de tarjeta
    document.querySelector('input[name="numero_tarjeta"]').addEventListener('input', function(e) {
        let value = this.value.replace(/\s/g, '');
        let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
        this.value = formattedValue;
    });
</script>
