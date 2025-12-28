@extends('layouts.pasarela')

@section('title', 'Detalles de Orden de Pago')

@section('styles')
<style>
    .orden-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px 15px 0 0;
        margin: -2rem -2rem 2rem -2rem;
    }

    .orden-header h2 {
        margin: 0;
        font-size: 1.8rem;
    }

    .orden-codigo {
        font-size: 2rem;
        font-weight: 700;
        letter-spacing: 2px;
        margin-top: 0.5rem;
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

    .monto-total {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 10px;
        margin: 1.5rem 0;
        text-align: center;
    }

    .monto-total .label {
        font-size: 1rem;
        color: #666;
        margin-bottom: 0.5rem;
    }

    .monto-total .valor {
        font-size: 3rem;
        font-weight: 700;
        color: var(--primary-color);
    }

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

    .conceptos-list {
        list-style: none;
        padding: 0;
    }

    .conceptos-list li {
        padding: 0.75rem;
        background: #f8f9fa;
        margin-bottom: 0.5rem;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .concepto-nombre {
        font-weight: 600;
    }

    .concepto-monto {
        color: var(--secondary-color);
        font-weight: 700;
        font-size: 1.1rem;
    }

    .vencimiento-alerta {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }

    .vencimiento-alerta.vencido {
        background: #f8d7da;
        border-left-color: #dc3545;
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-10">
        <!-- Orden Details Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="orden-header">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h2><i class="fas fa-file-invoice-dollar me-2"></i>Orden de Pago</h2>
                            <div class="orden-codigo">{{ $orden->codigo_orden }}</div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-light text-dark" style="font-size: 1rem;">
                                @if($orden->estado == '0')
                                    <i class="fas fa-clock me-1"></i>Pendiente
                                @elseif($orden->estado == '1')
                                    <i class="fas fa-spinner me-1"></i>En Proceso
                                @elseif($orden->estado == '2')
                                    <i class="fas fa-check me-1"></i>Pagado
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Alert de orden vencida con pago parcial (puede seguir pagando) -->
                @if(session('advertencia_vencida'))
                    <div class="alert" style="background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%); border-left: 5px solid #ffc107; border-radius: 10px; padding: 1.5rem; margin-bottom: 1.5rem;">
                        <div class="d-flex align-items-start gap-3">
                            <div style="flex-shrink: 0;">
                                <svg style="width: 2rem; height: 2rem; color: #856404;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div style="flex: 1;">
                                <h5 style="color: #856404; font-weight: 700; margin: 0 0 0.5rem 0;">
                                    <svg style="width: 1.25rem; height: 1.25rem; display: inline;" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    Orden Vencida con Pago Parcial
                                </h5>
                                <p style="color: #856404; margin: 0 0 0.5rem 0;">
                                    {{ session('advertencia_vencida') }}
                                </p>
                                <p style="color: #856404; margin: 0; font-weight: 600;">
                                    Puede completar el pago del saldo pendiente de <strong>S/ {{ number_format($saldoPendiente, 2) }}</strong>.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Alert de vencimiento -->
                @php
                    $dias_restantes = \Carbon\Carbon::now()->diffInDays($orden->fecha_vencimiento, false);
                @endphp
                
                @if($dias_restantes < 0 && !session('advertencia_vencida'))
                    <div class="vencimiento-alerta vencido">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>¡Orden Vencida!</strong> Esta orden venció el {{ $orden->fecha_vencimiento->format('d/m/Y') }}.
                        Por favor, contacta con tesorería.
                    </div>
                @elseif($dias_restantes <= 3)
                    <div class="vencimiento-alerta">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>¡Atención!</strong> Esta orden vence en {{ $dias_restantes }} día{{ $dias_restantes != 1 ? 's' : '' }}
                        ({{ $orden->fecha_vencimiento->format('d/m/Y') }}).
                    </div>
                @endif

                <!-- Información del Estudiante -->
                <h5 class="mb-3"><i class="fas fa-user-graduate me-2"></i>Información del Estudiante</h5>
                <div class="info-row">
                    <span class="info-label">Estudiante:</span>
                    <span class="info-value">{{ $orden->alumno->primer_nombre }} {{ $orden->alumno->otros_nombres }} {{ $orden->alumno->apellido_paterno }} {{ $orden->alumno->apellido_materno }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">DNI:</span>
                    <span class="info-value">{{ $orden->alumno->dni }}</span>
                </div>
                @if($orden->matricula)
                <div class="info-row">
                    <span class="info-label">Grado:</span>
                    <span class="info-value">
                        {{ $orden->matricula->grado->nombre_grado ?? 'N/A' }} - Sección {{ $orden->matricula->nombreSeccion ?? 'N/A' }}
                    </span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Fecha de Emisión:</span>
                    <span class="info-value">{{ $orden->fecha_orden_pago->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Fecha de Vencimiento:</span>
                    <span class="info-value">{{ $orden->fecha_vencimiento->format('d/m/Y') }}</span>
                </div>

                <!-- Conceptos de Pago -->
                <h5 class="mt-4 mb-3"><i class="fas fa-list-ul me-2"></i>Conceptos a Pagar</h5>
                <ul class="conceptos-list">
                    @foreach($orden->detalles as $detalle)
                        <li>
                            <div>
                                <span class="concepto-nombre">{{ $detalle->conceptoPago->descripcion ?? $detalle->deuda->conceptoPago->descripcion ?? 'Concepto no especificado' }}</span>
                                @if($detalle->descripcion_ajuste)
                                    <br><small class="text-muted">{{ $detalle->descripcion_ajuste }}</small>
                                @endif
                            </div>
                            <span class="concepto-monto">S/ {{ number_format($detalle->monto_subtotal, 2) }}</span>
                        </li>
                    @endforeach
                </ul>

                <!-- Información de Pagos Realizados -->
                @if($montoPagado > 0)
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Pagos Realizados:</strong> S/ {{ number_format($montoPagado, 2) }}
                </div>
                @endif

                <!-- Monto Total -->
                <div class="monto-total">
                    <div class="label">{{ $montoPagado > 0 ? 'SALDO PENDIENTE' : 'MONTO TOTAL A PAGAR' }}</div>
                    <div class="valor">S/ {{ number_format($saldoPendiente, 2) }}</div>
                    @if($montoPagado > 0)
                        <small class="text-muted d-block mt-2">
                            Monto original: S/ {{ number_format($orden->monto_total, 2) }}
                        </small>
                    @endif
                </div>

                @if($orden->observaciones)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Observaciones:</strong> {{ $orden->observaciones }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Métodos de Pago -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-wallet me-2"></i>Selecciona tu Método de Pago</h3>
            </div>
            <div class="card-body">
                <p class="text-center mb-4">Elige cómo deseas realizar tu pago de forma segura</p>
                
                <div class="row g-3">
                    <!-- Yape -->
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="{{ route('pasarela.metodo', [$orden->codigo_orden, 'yape']) }}" class="metodo-pago-card metodo-yape">
                            <img src="https://logosenvector.com/logo/img/yape-37283.png" alt="Yape" style="height: 60px; object-fit: contain; margin-bottom: 1rem;">
                            <h5>Yape</h5>
                            <small>Instantáneo</small>
                        </a>
                    </div>

                    <!-- Plin -->
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="{{ route('pasarela.metodo', [$orden->codigo_orden, 'plin']) }}" class="metodo-pago-card metodo-plin">
                            <img src="https://marketingperu.beglobal.biz/wp-content/uploads/2024/09/logo-plin-fondo-transparente.png" alt="Plin" style="height: 60px; object-fit: contain; margin-bottom: 1rem;">
                            <h5>Plin</h5>
                            <small>Instantáneo</small>
                        </a>
                    </div>

                    <!-- Transferencia -->
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="{{ route('pasarela.metodo', [$orden->codigo_orden, 'transferencia']) }}" class="metodo-pago-card metodo-transferencia">
                            <i class="fas fa-university"></i>
                            <h5>Transferencia</h5>
                            <small>Bancaria</small>
                        </a>
                    </div>

                    <!-- Tarjeta -->
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="{{ route('pasarela.metodo', [$orden->codigo_orden, 'tarjeta']) }}" class="metodo-pago-card metodo-tarjeta">
                            <i class="fas fa-credit-card"></i>
                            <h5>Tarjeta</h5>
                            <small>Crédito/Débito</small>
                        </a>
                    </div>

                    <!-- PayPal -->
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="{{ route('pasarela.metodo', [$orden->codigo_orden, 'paypal']) }}" class="metodo-pago-card metodo-paypal">
                            <i class="fab fa-paypal"></i>
                            <h5>PayPal</h5>
                            <small>Internacional</small>
                        </a>
                    </div>

                    <!-- Volver -->
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="{{ route('pasarela.index') }}" class="metodo-pago-card" style="border-color: #6c757d;">
                            <i class="fas fa-arrow-left" style="color: #6c757d;"></i>
                            <h5>Volver</h5>
                            <small>Buscar otra orden</small>
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

{{-- Overlay y Tooltip para Guía Interactiva Paso 3 --}}
<div id="overlayGuiaPago" style="display: none; position: fixed; inset: 0; z-index: 9998;">
    <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px);"></div>
</div>

{{-- Tooltip flotante --}}
<div id="tooltipGuiaPago" style="display: none; position: fixed; z-index: 9999; max-width: 500px;">
    <div style="background: white; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border: 4px solid #a855f7; overflow: hidden;">
        {{-- Header --}}
        <div style="background: linear-gradient(to right, #a855f7, #ec4899); padding: 1.5rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                <span style="font-size: 0.875rem; font-weight: bold; color: white; text-transform: uppercase; letter-spacing: 0.05em;">PASO 3 DE 3 • FINAL</span>
                <button id="btnCerrarGuiaPago" style="color: rgba(255,255,255,0.8); background: none; border: none; cursor: pointer; padding: 0; transition: color 0.3s;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <h3 style="font-size: 1.5rem; font-weight: bold; color: white; margin: 0 0 0.75rem 0;">¡Elige tu Método de Pago!</h3>
            <div style="height: 6px; background: rgba(255,255,255,0.3); border-radius: 9999px; overflow: hidden;">
                <div style="height: 100%; background: white; border-radius: 9999px; width: 100%; transition: width 0.5s;"></div>
            </div>
        </div>

        {{-- Contenido --}}
        <div style="padding: 2rem;">
            <div style="background: linear-gradient(135deg, #fdf4ff 0%, #fae8ff 100%); padding: 1.25rem; border-radius: 12px; margin-bottom: 1.5rem; border-left: 4px solid #a855f7;">
                <div style="display: flex; align-items: start; gap: 1rem;">
                    <svg style="width: 28px; height: 28px; color: #a855f7; flex-shrink: 0; margin-top: 2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p style="color: #701a75; font-weight: 600; margin: 0 0 0.5rem 0; font-size: 1rem;">
                            ¡Ya estás listo para pagar!
                        </p>
                        <p style="color: #701a75; margin: 0; line-height: 1.6;">
                            Ahora selecciona el <strong>método de pago</strong> que prefieras. Todos son seguros y fáciles de usar.
                        </p>
                    </div>
                </div>
            </div>

            <div style="background: #f9fafb; padding: 1.25rem; border-radius: 12px; margin-bottom: 1.5rem;">
                <h5 style="color: #374151; font-weight: 700; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
                    <svg style="width: 20px; height: 20px; color: #a855f7;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>
                    Métodos disponibles:
                </h5>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></div>
                        <span style="font-size: 0.875rem; color: #4b5563;"><strong>Yape</strong> - Instantáneo</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></div>
                        <span style="font-size: 0.875rem; color: #4b5563;"><strong>Plin</strong> - Instantáneo</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></div>
                        <span style="font-size: 0.875rem; color: #4b5563;"><strong>Tarjeta</strong> - Visa/Mastercard</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></div>
                        <span style="font-size: 0.875rem; color: #4b5563;"><strong>Transferencia</strong> - Bancaria</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></div>
                        <span style="font-size: 0.875rem; color: #4b5563;"><strong>PayPal</strong> - Internacional</span>
                    </div>
                </div>
            </div>

            <div style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <svg style="width: 20px; height: 20px; color: #1e40af; flex-shrink: 0;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <p style="font-size: 0.875rem; color: #1e3a8a; margin: 0; line-height: 1.5;">
                        <strong>Consejo:</strong> Yape y Plin son los más rápidos. Tu pago se refleja al instante.
                    </p>
                </div>
            </div>

            <div style="display: flex; justify-content: center; gap: 1rem;">
                <button id="btnEntendidoPago" style="padding: 0.875rem 2rem; background: linear-gradient(to right, #a855f7, #ec4899); color: white; font-weight: bold; font-size: 1.125rem; border-radius: 10px; border: none; cursor: pointer; box-shadow: 0 10px 15px -3px rgba(168, 85, 247, 0.3); transition: all 0.3s; display: flex; align-items: center; gap: 0.75rem;">
                    <span>¡Entendido!</span>
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Script para la Guía Interactiva Paso 3 --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Verificar si debe mostrar la guía del paso 3
        const estadoGuia = obtenerEstadoGuia();
        
        if (estadoGuia && estadoGuia.activa && estadoGuia.paso === 'pagoMetodo') {
            console.log('✅ Iniciando guía paso 3 - Selección de método de pago');
            setTimeout(() => {
                mostrarGuiaPagoMetodo();
            }, 800);
        }
    });

    function obtenerEstadoGuia() {
        const dataStr = sessionStorage.getItem('guiaPagos');
        if (!dataStr) return null;
        
        try {
            const data = JSON.parse(dataStr);
            if (Date.now() > data.expira) {
                sessionStorage.removeItem('guiaPagos');
                return null;
            }
            return data;
        } catch (e) {
            sessionStorage.removeItem('guiaPagos');
            return null;
        }
    }

    function mostrarGuiaPagoMetodo() {
        const overlay = document.getElementById('overlayGuiaPago');
        const tooltip = document.getElementById('tooltipGuiaPago');
        
        if (!overlay || !tooltip) return;

        // Mostrar overlay y tooltip
        overlay.style.display = 'block';
        tooltip.style.display = 'block';
        document.body.style.overflow = 'hidden';

        // Destacar los métodos de pago
        const metodosCards = document.querySelectorAll('.metodo-pago-card');
        metodosCards.forEach((card, index) => {
            if (index < 5) { // Solo los métodos de pago, no el botón "Volver"
                card.style.position = 'relative';
                card.style.zIndex = '9999';
                card.style.border = '3px solid #a855f7';
                card.style.outline = '3px solid rgba(168, 85, 247, 0.3)';
                card.style.transform = 'translateY(-5px)';
                card.style.boxShadow = '0 20px 40px rgba(168, 85, 247, 0.4)';
            }
        });

        // Posicionar tooltip en el centro
        posicionarTooltipCentrado();

        // Event listeners
        document.getElementById('btnCerrarGuiaPago').addEventListener('click', cerrarGuiaPago);
        document.getElementById('btnEntendidoPago').addEventListener('click', cerrarGuiaPago);

        // Bloquear clics excepto en métodos de pago y botones de la guía
        document.addEventListener('click', bloquearClicsPago, true);
    }

    function posicionarTooltipCentrado() {
        const tooltip = document.getElementById('tooltipGuiaPago');
        const windowHeight = window.innerHeight;
        const windowWidth = window.innerWidth;
        
        tooltip.style.top = '50%';
        tooltip.style.left = '50%';
        tooltip.style.transform = 'translate(-50%, -50%)';
    }

    function bloquearClicsPago(e) {
        const tooltip = document.getElementById('tooltipGuiaPago');
        const btnCerrar = document.getElementById('btnCerrarGuiaPago');
        const btnEntendido = document.getElementById('btnEntendidoPago');
        const metodosCards = document.querySelectorAll('.metodo-pago-card');
        
        const elementosPermitidos = [tooltip, btnCerrar, btnEntendido, ...metodosCards];
        
        let permitido = false;
        for (let elem of elementosPermitidos) {
            if (elem && (elem === e.target || elem.contains(e.target))) {
                permitido = true;
                break;
            }
        }
        
        if (!permitido) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        }
    }

    function cerrarGuiaPago() {
        const overlay = document.getElementById('overlayGuiaPago');
        const tooltip = document.getElementById('tooltipGuiaPago');
        
        if (overlay) overlay.style.display = 'none';
        if (tooltip) tooltip.style.display = 'none';
        document.body.style.overflow = 'auto';

        // Remover estilos de destacado
        const metodosCards = document.querySelectorAll('.metodo-pago-card');
        metodosCards.forEach(card => {
            card.style.position = '';
            card.style.zIndex = '';
            card.style.border = '';
            card.style.outline = '';
            card.style.transform = '';
            card.style.boxShadow = '';
        });

        // Remover listener de bloqueo
        document.removeEventListener('click', bloquearClicsPago, true);

        // Limpiar sessionStorage
        sessionStorage.removeItem('guiaPagos');
        
        // Guardar que el usuario ya conoce la guía
        localStorage.setItem('usuarioConoceGuiaPagos', 'true');
        
        console.log('✅ Guía completada');
    }
</script>

@endsection
