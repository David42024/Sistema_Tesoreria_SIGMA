<div class="p-8 m-4 bg-gray-100 dark:bg-white/[0.03] rounded-2xl">
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fab fa-paypal fa-4x text-primary mb-3"></i>
                        <h3>Pago con PayPal</h3>
                    </div>

                    <div class="alert alert-info">
                        <strong>Orden:</strong> {{ $orden->codigo_orden }}<br>
                        <strong>Alumno:</strong> {{ $alumno->primer_nombre }} {{ $alumno->apellido_paterno }}<br>
                        <h4 class="mt-2 mb-0">Total: S/. {{ number_format($orden->monto_total, 2) }}</h4>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        Serás redirigido a PayPal para completar el pago de forma segura.
                    </div>

                    <form action="{{ route('familiar_pago_pago_realizar_procesar') }}" method="POST">
                        @csrf
                        <input type="hidden" name="metodo_pago" value="paypal">
                        <input type="hidden" name="monto" value="{{ $orden->monto_total }}">

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fab fa-paypal me-2"></i>Pagar con PayPal
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
