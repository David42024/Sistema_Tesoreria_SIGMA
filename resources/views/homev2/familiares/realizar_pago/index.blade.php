<style>
    .deuda-card {
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .deuda-card:hover {
        border-color: var(--secondary-color);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .deuda-card input[type="checkbox"]:checked ~ .card-content {
        background: #e3f2fd;
    }

    .monto-deuda {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--secondary-color);
    }

    .fecha-vencimiento {
        color: #dc3545;
        font-weight: 600;
    }

    .fecha-vencimiento.vigente {
        color: #28a745;
    }

    .resumen-seleccion {
        position: sticky;
        top: 20px;
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
</style>

<div class="p-8 m-4 bg-gray-100 dark:bg-white/[0.03] rounded-2xl">
    <div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('familiar_pago_view_deudas') }}">Deudas</a></li>
                    <li class="breadcrumb-item active">Realizar Pago</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Realizar Pago</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('familiar_pago_pago_realizar_procesar_seleccion') }}" method="POST" id="formSeleccionDeudas">
                        @csrf

                        <div class="row">
                            <div class="col-lg-8">
                                <h5 class="mb-3">Seleccione las deudas que desea pagar</h5>

                                @if($deudas->isEmpty())
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>No tienes deudas pendientes por pagar.
                                    </div>
                                @else
                                    <div id="deudasContainer">
                                        @foreach($deudas as $deuda)
                                            @php
                                                $vencida = \Carbon\Carbon::parse($deuda->fecha_limite) < \Carbon\Carbon::now();
                                            @endphp
                                            <div class="deuda-card">
                                                <label class="d-flex align-items-start gap-3 w-100 cursor-pointer">
                                                    <input type="checkbox"
                                                           name="deudas_seleccionadas[]"
                                                           value="{{ $deuda->id_deuda }}"
                                                           data-monto="{{ $deuda->monto_total }}"
                                                           class="form-check-input mt-1 deuda-checkbox"
                                                           style="width: 24px; height: 24px; cursor: pointer;">

                                                    <div class="card-content flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div>
                                                                <h6 class="mb-1">{{ $deuda->conceptoPago->descripcion ?? 'N/A' }}</h6>
                                                                <small class="text-muted">Período: {{ $deuda->periodo ?? 'N/A' }}</small>
                                                            </div>
                                                            <div class="text-end">
                                                                <div class="monto-deuda">S/. {{ number_format($deuda->monto_total, 2) }}</div>
                                                                <small class="fecha-vencimiento {{ $vencida ? '' : 'vigente' }}">
                                                                    @if($vencida)
                                                                        <i class="fas fa-exclamation-triangle"></i> Vencida
                                                                    @else
                                                                        <i class="fas fa-clock"></i> Vence: {{ \Carbon\Carbon::parse($deuda->fecha_limite)->format('d/m/Y') }}
                                                                    @endif
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="col-lg-4">
                                <div class="resumen-seleccion">
                                    <h5 class="mb-3">Resumen de Selección</h5>

                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Deudas seleccionadas:</span>
                                            <strong id="cantidadSeleccionadas">0</strong>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Total a pagar:</span>
                                            <strong class="text-primary" style="font-size: 1.5rem;" id="totalSeleccionado">S/. 0.00</strong>
                                        </div>
                                    </div>

                                    <hr>

                                    <button type="submit"
                                            class="btn btn-primary w-100 btn-lg"
                                            id="btnContinuar"
                                            disabled>
                                        <i class="fas fa-arrow-right me-2"></i>Continuar al Pago
                                    </button>

                                    <a href="{{ route('familiar_pago_view_deudas') }}" class="btn btn-outline-secondary w-100 mt-2">
                                        <i class="fas fa-arrow-left me-2"></i>Cancelar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.deuda-checkbox');
        const totalElement = document.getElementById('totalSeleccionado');
        const cantidadElement = document.getElementById('cantidadSeleccionadas');
        const btnContinuar = document.getElementById('btnContinuar');

        function actualizarResumen() {
            let total = 0;
            let cantidad = 0;

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    total += parseFloat(checkbox.dataset.monto);
                    cantidad++;
                }
            });

            totalElement.textContent = 'S/. ' + total.toFixed(2);
            cantidadElement.textContent = cantidad;
            btnContinuar.disabled = cantidad === 0;
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', actualizarResumen);
        });
    });
</script>
</div>
