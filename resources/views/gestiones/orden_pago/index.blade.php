@extends('base.administrativo.blank')

@section('titulo')
  {{ $data['titulo'] }}
@endsection

@section('just-after-html')
  <div class="delete-modal hidden">
    @include('layout.modals.modal-01', [
      'caution_message' => '¿Estás seguro?',
      'action' => 'Estás eliminando la Orden de Pago',
      'columns' => [
        'Código',
        'Alumno',
        'Grado/Sección',
        'Monto Total',
        'Fecha Vencimiento',
        'Estado',
        '',
      ],
      'rows' => [
        'codigo',
        'alumno',
        'grado-seccion',
        'monto',
        'vencimiento',
        'estado',
        'btn'
      ],
      'last_warning_message' => 'Borrar esto afectará a todo lo que esté vinculado a esta Orden de Pago',
      'confirm_button' => 'Sí, bórralo',
      'cancel_button' => 'Cancelar',
      'is_form' => true,
      'data_input_name' => 'id'
    ])
  </div>
@endsection

@section('contenido')
      @if(isset($data['created']))
        @include('layout.alerts.animated.timed-alert',[
          'message' => 'La orden de pago ha sido generada exitosamente.',
          'route' => 'layout.alerts.success' 
        ])
      @endif

      @if(isset($data['edited']))
        @include('layout.alerts.animated.timed-alert',[
          'message' => 'La orden de pago ha sido actualizada exitosamente.',
          'route' => 'layout.alerts.orange-success' 
        ])
      @endif

      @if(isset($data['abort']))
        @include('layout.alerts.animated.timed-alert',[
          'message' => 'La acción sobre la orden de pago ha sido cancelada.',
          'route' => 'layout.alerts.info' 
        ])
      @endif

      @if(isset($data['deleted']))
        @include('layout.alerts.animated.timed-alert',[
          'message' => 'La orden de pago ha sido eliminada exitosamente.',
          'route' => 'layout.alerts.red-success' 
        ])
      @endif

      @include('layout.tables.table-ordenes-pago', $data)    
@endsection

@section('custom-js')
  <script src="{{ asset('js/tables.js') }}"></script>
  <script src="{{ asset('js/delete-button-modal.js') }}"></script>

  {{-- Script para continuar la guía de pagos --}}
  <script>
    // ============ FUNCIONES AUXILIARES PARA LA GUÍA ============
    function obtenerEstadoGuia() {
        const dataStr = sessionStorage.getItem('guiaPagos');
        if (!dataStr) return null;
        
        try {
            const data = JSON.parse(dataStr);
            // Verificar si ha expirado
            if (Date.now() > data.expira) {
                console.log('Guía expirada, limpiando...');
                sessionStorage.removeItem('guiaPagos');
                return null;
            }
            return data;
        } catch (e) {
            console.error('Error parseando guía:', e);
            sessionStorage.removeItem('guiaPagos');
            return null;
        }
    }

    function guardarEstadoGuia(paso, subpaso = 0) {
        const timestamp = Date.now();
        const guiaData = {
            activa: true,
            paso: paso,
            subpaso: subpaso,
            timestamp: timestamp,
            expira: timestamp + (5 * 60 * 1000) // 5 minutos
        };
        sessionStorage.setItem('guiaPagos', JSON.stringify(guiaData));
    }

    function limpiarGuia() {
        sessionStorage.removeItem('guiaPagos');
        // Mantener solo el flag de que el usuario conoce la guía (en localStorage)
        const conoceGuia = localStorage.getItem('usuarioConoceGuiaPagos');
        if (conoceGuia) {
            localStorage.setItem('usuarioConoceGuiaPagos', 'true');
        }
    }

    // ============ INICIO DEL SCRIPT ============
    document.addEventListener('DOMContentLoaded', function() {
        // Abrir PDF en nueva ventana si se acaba de crear una orden
        @if(isset($data['created']) && request()->has('pdf_id'))
            const pdfUrl = "{{ route('orden_pago_pdf', ['id' => request('pdf_id')]) }}";
            console.log('🔍 Abriendo PDF:', pdfUrl);
            
            // Abrir inmediatamente en nueva ventana
            setTimeout(() => {
                const ventanaPDF = window.open(pdfUrl, '_blank', 'noopener,noreferrer');
                if (!ventanaPDF || ventanaPDF.closed || typeof ventanaPDF.closed == 'undefined') {
                    console.error('❌ Popup bloqueado');
                    alert('Por favor permite ventanas emergentes para ver el PDF.');
                } else {
                    console.log('✅ PDF abierto en nueva ventana');
                }
            }, 200);
        @endif

        // Verificar si la guía está activa (Paso 1)
        const estadoGuia = obtenerEstadoGuia();
        const guiaActiva = estadoGuia && estadoGuia.activa;
        const pasoActual = estadoGuia ? estadoGuia.paso : null;

        if (guiaActiva && pasoActual === 'ordenPago') {
            console.log('Continuando guía paso 1 en orden_pago');
            setTimeout(() => {
                mostrarTooltipFinal();
            }, 500);
        }

        // Detectar si se acaba de crear una orden exitosamente (Paso 2)
        @if(isset($data['created']))
            // Verificar si hay una guía activa o si el usuario ha usado la guía antes
            const usuarioConoceGuia = localStorage.getItem('usuarioConoceGuiaPagos');
            
            if (usuarioConoceGuia === 'true') {
                // Preguntar si desea continuar con el paso 2
                setTimeout(() => {
                    mostrarPreguntaPaso2();
                }, 2000); // Esperar 2 segundos después del alert de éxito
            }
        @endif
    });

    function mostrarTooltipFinal() {
        // Crear overlay
        const overlay = document.createElement('div');
        overlay.id = 'overlayGuiaFinal';
        overlay.className = 'fixed inset-0 z-[99998] bg-black/70 backdrop-blur-sm';
        document.body.appendChild(overlay);

        // Crear tooltip
        const tooltip = document.createElement('div');
        tooltip.className = 'fixed z-[99999] top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 max-w-2xl w-full mx-4';
        tooltip.innerHTML = `
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border-4 border-emerald-500 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 px-8 py-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-bold text-white uppercase tracking-wider">Paso Final</span>
                        <button id="btnCerrarGuiaFinal" class="text-white/80 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">Generar Orden de Pago</h3>
                    <div class="h-1.5 bg-white/30 rounded-full overflow-hidden">
                        <div class="h-full bg-white" style="width: 100%"></div>
                    </div>
                </div>

                <div class="p-8">
                    <div class="mb-6">
                        <div class="flex items-start gap-4 mb-4 p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                            <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <div>
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Para generar una orden de pago:</h4>
                                <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                                    <li class="flex items-start gap-2">
                                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">1.</span>
                                        <span>Busca al <strong>estudiante</strong> por su DNI, nombre o apellido usando los <strong>filtros disponibles</strong></span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">2.</span>
                                        <span>Haz clic en el botón <strong>"Nueva Orden"</strong> o <strong>"Generar"</strong> en la fila del estudiante</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">3.</span>
                                        <span>Completa los datos requeridos y confirma la generación</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded mb-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-blue-800 dark:text-blue-200 mb-1">Consejo</p>
                                <p class="text-sm text-blue-700 dark:text-blue-300">Utiliza los filtros de búsqueda en la parte superior de la tabla para encontrar rápidamente al estudiante que necesitas.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button id="btnFinalizarGuia" class="px-8 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold rounded-lg transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
                            ¡Entendido, comenzar!
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(tooltip);

        // Bloquear scroll
        document.body.style.overflow = 'hidden';

        // Event listeners
        document.getElementById('btnCerrarGuiaFinal').addEventListener('click', cerrarGuiaFinal);
        document.getElementById('btnFinalizarGuia').addEventListener('click', cerrarGuiaFinal);
    }

    function cerrarGuiaFinal() {
        // Remover elementos del modal
        const overlay = document.getElementById('overlayGuiaFinal');
        const tooltip = overlay ? overlay.nextElementSibling : null;
        
        if (overlay) overlay.remove();
        if (tooltip) tooltip.remove();

        // Restaurar scroll
        document.body.style.overflow = 'auto';

        // Ahora SÍ resaltar el botón de crear
        const btnCrear = document.getElementById('btn-crear-orden-pago');
        if (btnCrear) {
            btnCrear.style.position = 'relative';
            btnCrear.style.zIndex = '100000';
            btnCrear.style.border = '3px solid rgb(16, 185, 129)';
            btnCrear.style.outline = '3px solid rgba(16, 185, 129, 0.3)';
            btnCrear.style.borderRadius = '8px';

            // Scroll suave hacia el botón
            btnCrear.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        // Marcar que el usuario conoce la guía (esto sí va en localStorage permanente)
        localStorage.setItem('usuarioConoceGuiaPagos', 'true');

        // Limpiar sessionStorage de la guía
        setTimeout(() => {
            limpiarGuia();
        }, 100);
    }

    // Función para preguntar si desea continuar con el paso 2
    function mostrarPreguntaPaso2() {
        // Crear overlay
        const overlay = document.createElement('div');
        overlay.id = 'overlayPreguntaPaso2';
        overlay.className = 'fixed inset-0 z-[99998] bg-black/70 backdrop-blur-sm';
        document.body.appendChild(overlay);

        // Crear modal de pregunta
        const modal = document.createElement('div');
        modal.className = 'fixed z-[99999] top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 max-w-lg w-full mx-4';
        modal.innerHTML = `
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border-4 border-blue-500 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-500 px-8 py-6">
                    <div class="flex items-center gap-3 mb-2">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="text-2xl font-bold text-white">¡Orden Creada Exitosamente!</h3>
                    </div>
                </div>

                <div class="p-8">
                    <div class="mb-6">
                        <p class="text-lg text-gray-700 dark:text-gray-300 mb-4">
                            Tu orden de pago ha sido generada. ¿Te gustaría continuar con el <strong>Paso 2: Realizar el Pago en Línea</strong>?
                        </p>
                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded">
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                La guía te mostrará cómo el estudiante puede pagar en línea usando la pasarela de pagos del sistema.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3 justify-end">
                        <button id="btnOmitirPaso2" class="px-6 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                            No, ahora no
                        </button>
                        <button id="btnContinuarPaso2" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white font-bold rounded-lg transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
                            Sí, continuar con la guía
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);

        // Bloquear scroll
        document.body.style.overflow = 'hidden';

        // Event listeners
        document.getElementById('btnOmitirPaso2').addEventListener('click', cerrarPreguntaPaso2);
        document.getElementById('btnContinuarPaso2').addEventListener('click', iniciarPaso2);
    }

    function cerrarPreguntaPaso2() {
        const overlay = document.getElementById('overlayPreguntaPaso2');
        const modal = overlay ? overlay.nextElementSibling : null;
        
        if (overlay) overlay.remove();
        if (modal) modal.remove();

        document.body.style.overflow = 'auto';
    }

    function iniciarPaso2() {
        // Cerrar modal de pregunta
        cerrarPreguntaPaso2();

        // Guardar en sessionStorage con timestamp (expira en 5 minutos)
        const timestamp = Date.now();
        const guiaData = {
            activa: true,
            paso: 'pagarEnLinea',
            subpaso: 0,
            timestamp: timestamp,
            expira: timestamp + (5 * 60 * 1000) // 5 minutos
        };
        sessionStorage.setItem('guiaPagos', JSON.stringify(guiaData));

        // Iniciar guía interactiva del paso 2
        iniciarGuiaPagarEnLinea();
    }

    // Guía interactiva para Pagar en Línea (Paso 2)
    const pasosPagar = [
        {
            selector: '#btn-dropdown-usuario',
            titulo: 'Abre el Menú de Usuario',
            descripcion: 'Haz clic en tu <strong>nombre de usuario</strong> en la esquina superior derecha para abrir el menú desplegable.',
            posicion: 'bottom-left',
            esperarClick: true
        },
        {
            selector: '#btn-cerrar-sesion',
            titulo: 'Cerrar Sesión',
            descripcion: 'Haz clic en <strong>"Cerrar Sesión"</strong> para salir del sistema administrativo y volver al login.',
            posicion: 'dropdown-left',
            esperarClick: true
        }
    ];

    let pasoActualPagar = 0;
    let elementoActualPagar = null;
    let overlayPagar = null;
    let tooltipPagar = null;

    function iniciarGuiaPagarEnLinea() {
        pasoActualPagar = 0;

        // Crear overlay
        overlayPagar = document.createElement('div');
        overlayPagar.id = 'overlayGuiaPagar';
        overlayPagar.className = 'fixed inset-0 z-[99998] bg-black/70 backdrop-blur-sm';
        document.body.appendChild(overlayPagar);

        // Crear tooltip
        tooltipPagar = document.createElement('div');
        tooltipPagar.id = 'tooltipGuiaPagar';
        tooltipPagar.className = 'fixed z-[99999]';
        document.body.appendChild(tooltipPagar);

        // Bloquear clics
        document.addEventListener('click', bloquearClicsPagar, true);
        
        mostrarPasoPagar(pasoActualPagar);
    }

    function mostrarPasoPagar(index) {
        const paso = pasosPagar[index];
        elementoActualPagar = document.querySelector(paso.selector);

        if (!elementoActualPagar) {
            console.error('Elemento no encontrado:', paso.selector);
            return;
        }

        // Limpiar estilos anteriores
        document.querySelectorAll('[data-guia-highlight]').forEach(el => {
            el.style.border = '';
            el.style.outline = '';
            el.style.borderRadius = '';
            el.style.position = '';
            el.style.zIndex = '';
            el.removeAttribute('data-guia-highlight');
        });

        // Resaltar elemento actual
        elementoActualPagar.style.position = 'relative';
        elementoActualPagar.style.zIndex = '100000';
        elementoActualPagar.style.border = '3px solid rgb(168, 85, 247)'; // purple-500
        elementoActualPagar.style.outline = '3px solid rgba(168, 85, 247, 0.3)';
        elementoActualPagar.style.borderRadius = '8px';
        elementoActualPagar.setAttribute('data-guia-highlight', 'true');

        // Posicionar tooltip
        posicionarTooltipPagar(paso);

        // Scroll suave al elemento
        elementoActualPagar.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Agregar listener si espera click
        if (paso.esperarClick) {
            elementoActualPagar.addEventListener('click', elementoClickHandlerPagar);
        }

        // Guardar paso actual en sessionStorage
        guardarEstadoGuia('pagarEnLinea', index);
    }

    function posicionarTooltipPagar(paso) {
        const rect = elementoActualPagar.getBoundingClientRect();
        const progreso = ((pasoActualPagar + 1) / pasosPagar.length) * 66.66; // 66.66% porque es paso 2 de 3

        tooltipPagar.innerHTML = `
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border-4 border-purple-500 overflow-hidden max-w-md">
                <div class="bg-gradient-to-r from-purple-500 to-pink-500 px-6 py-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-white uppercase tracking-wider">Paso 2 de 3 • ${pasoActualPagar + 1}/${pasosPagar.length}</span>
                        <button id="btnCerrarGuiaPagar" class="text-white/80 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">${paso.titulo}</h3>
                    <div class="h-1.5 bg-white/30 rounded-full overflow-hidden">
                        <div class="h-full bg-white rounded-full transition-all duration-500" style="width: ${progreso}%"></div>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 dark:text-gray-300 mb-4">${paso.descripcion}</p>
                    ${!paso.esperarClick ? `
                        <div class="flex justify-end">
                            <button id="btnSiguientePagar" class="px-6 py-2 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-semibold rounded-lg transition-all">
                                Siguiente
                            </button>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;

        // Posicionar según configuración
        const tooltipWidth = 400; // Ancho aproximado del tooltip
        const tooltipHeight = 250; // Alto aproximado del tooltip
        const padding = 20; // Separación del elemento
        
        let top, left;
        
        if (paso.posicion === 'bottom-left') {
            // Debajo y a la izquierda del elemento
            top = rect.bottom + window.scrollY + padding;
            left = rect.right - tooltipWidth;
            tooltipPagar.style.transform = '';
            
            // Ajustar si se sale de la pantalla
            if (left < padding) left = padding;
            if (left + tooltipWidth > window.innerWidth - padding) {
                left = window.innerWidth - tooltipWidth - padding;
            }
        } else if (paso.posicion === 'bottom') {
            // Debajo del elemento, centrado
            top = rect.bottom + window.scrollY + padding;
            left = rect.left + (rect.width / 2);
            tooltipPagar.style.transform = 'translateX(-50%)';
        } else if (paso.posicion === 'left') {
            // A la izquierda del elemento, centrado verticalmente
            top = rect.top + window.scrollY + (rect.height / 2) - (tooltipHeight / 2);
            left = rect.left - tooltipWidth - padding;
            tooltipPagar.style.transform = '';
            
            // Si no cabe a la izquierda, colocar a la derecha
            if (left < 0) {
                left = rect.right + padding;
            }
            
            // Asegurar que no se salga por arriba o abajo
            if (top < window.scrollY + padding) {
                top = window.scrollY + padding;
            }
            if (top + tooltipHeight > window.scrollY + window.innerHeight - padding) {
                top = window.scrollY + window.innerHeight - tooltipHeight - padding;
            }
        } else if (paso.posicion === 'dropdown-left') {
            // Al lado izquierdo del dropdown completo
            // El dropdown tiene x-show="dropdownOpen", buscar ese contenedor
            let dropdown = document.querySelector('[x-show="dropdownOpen"]');
            
            if (!dropdown) {
                // Fallback: buscar por la clase del dropdown
                dropdown = elementoActualPagar.closest('div.absolute');
            }
            
            const dropdownRect = dropdown ? dropdown.getBoundingClientRect() : rect;
            
            console.log('Dropdown encontrado:', dropdown);
            console.log('DropdownRect:', dropdownRect);
            
            // Calcular posición desde el lado derecho de la pantalla
            // El tooltip debe estar 300px desde el borde derecho
            const rightPosition = 300; // 300px desde el derecho como en tu ejemplo
            top = 65 + window.scrollY; // 65px desde arriba como en tu ejemplo
            
            // Usar right en lugar de left
            tooltipPagar.style.right = rightPosition + 'px';
            tooltipPagar.style.left = 'auto'; // Desactivar left
            tooltipPagar.style.transform = '';
            
            // Asegurar que no se salga por arriba o abajo
            if (top < window.scrollY + padding) {
                top = window.scrollY + padding;
            }
            if (top + tooltipHeight > window.scrollY + window.innerHeight - padding) {
                top = window.scrollY + window.innerHeight - tooltipHeight - padding;
            }
        } else if (paso.posicion === 'right') {
            // A la derecha del elemento
            top = rect.top + window.scrollY + (rect.height / 2) - (tooltipHeight / 2);
            left = rect.right + padding;
            tooltipPagar.style.transform = '';
        } else {
            // Por defecto: centro de la pantalla
            top = window.innerHeight / 2 - tooltipHeight / 2 + window.scrollY;
            left = window.innerWidth / 2;
            tooltipPagar.style.transform = 'translateX(-50%)';
        }

        tooltipPagar.style.top = top + 'px';
        
        // Solo establecer left si no es dropdown-left (que usa right)
        if (paso.posicion !== 'dropdown-left') {
            tooltipPagar.style.left = left + 'px';
        }

        // Event listeners
        document.getElementById('btnCerrarGuiaPagar').addEventListener('click', cerrarGuiaPagar);
        const btnSiguiente = document.getElementById('btnSiguientePagar');
        if (btnSiguiente) {
            btnSiguiente.addEventListener('click', avanzarPasoPagar);
        }
    }

    function elementoClickHandlerPagar() {
        elementoActualPagar.removeEventListener('click', elementoClickHandlerPagar);
        avanzarPasoPagar();
    }

    function avanzarPasoPagar() {
        pasoActualPagar++;
        if (pasoActualPagar < pasosPagar.length) {
            mostrarPasoPagar(pasoActualPagar);
        } else {
            // Al terminar el último paso, marcar que debe continuar en login
            cerrarGuiaPagar();
            guardarEstadoGuia('loginPagar', 0);
        }
    }

    function bloquearClicsPagar(e) {
        const elementosPermitidos = [elementoActualPagar, document.getElementById('btnCerrarGuiaPagar'), tooltipPagar];
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

    function cerrarGuiaPagar() {
        // Limpiar estilos
        document.querySelectorAll('[data-guia-highlight]').forEach(el => {
            el.style.border = '';
            el.style.outline = '';
            el.style.borderRadius = '';
            el.style.position = '';
            el.style.zIndex = '';
            el.removeAttribute('data-guia-highlight');
        });

        // Remover elementos
        if (overlayPagar) overlayPagar.remove();
        if (tooltipPagar) tooltipPagar.remove();

        // Remover listener
        document.removeEventListener('click', bloquearClicsPagar, true);

        document.body.style.overflow = 'auto';
    }
  </script>
@endsection
