<div class="p-8 m-4 bg-gray-100 dark:bg-white/[0.03] rounded-2xl">
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-university fa-4x text-primary mb-3"></i>
                        <h3>Transferencia Bancaria</h3>
                    </div>

                    <div class="alert alert-info">
                        <strong>Orden:</strong> {{ $orden->codigo_orden }}<br>
                        <strong>Alumno:</strong> {{ $alumno->primer_nombre }} {{ $alumno->apellido_paterno }}<br>
                        <h4 class="mt-2 mb-0">Total: S/. {{ number_format($orden->monto_total, 2) }}</h4>
                    </div>

                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h5>Datos de la Cuenta del Colegio</h5>
                            <hr>
                            <p><strong>Banco:</strong> {{ $cuenta_colegio['banco'] }}</p>
                            <p><strong>Número de Cuenta:</strong> {{ $cuenta_colegio['numero'] }}</p>
                            <p><strong>CCI:</strong> {{ $cuenta_colegio['cci'] }}</p>
                            <p><strong>Titular:</strong> {{ $cuenta_colegio['titular'] }}</p>
                        </div>
                    </div>

                    <form action="{{ route('familiar_pago_pago_realizar_procesar') }}" method="POST">
                        @csrf
                        <input type="hidden" name="metodo_pago" value="transferencia">
                        <input type="hidden" name="monto" value="{{ $orden->monto_total }}">

                        <div class="mb-3">
                            <label class="form-label">Banco de Origen</label>
                            <select name="banco_origen" class="form-select" required>
                                <option value="">Seleccione su banco</option>
                                @foreach($bancos as $codigo => $nombre)
                                    <option value="{{ $codigo }}">{{ $nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Número de Operación</label>
                            <input type="text" class="form-control" name="numero_operacion"
                                   placeholder="Ingrese el número de operación" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-check me-2"></i>Confirmar Pago
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
