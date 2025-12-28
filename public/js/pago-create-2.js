// ==========================================
// MANEJO DE TIPO DE PAGO
// ==========================================

document.addEventListener('DOMContentLoaded', function() {
    const radioDeudaIndividual = document.querySelector('input[name="tipo_pago_selector"][value="deuda_individual"]');
    const radioOrdenCompleta = document.querySelector('input[name="tipo_pago_selector"][value="orden_completa"]');
    
    const seccionDeudaIndividual = document.getElementById('seccion_deuda_individual');
    const seccionOrdenCompleta = document.getElementById('seccion_orden_completa');
    const detallesPagoContainer = document.getElementById('detalles-pago-container');
    
    const btnBuscarOrden = document.getElementById('btnBuscarOrden');
    const codigoOrdenInput = document.getElementById('codigo_orden');
    const infoOrdenContainer = document.getElementById('info_orden_container');
    
    // Inicializar el valor del campo hidden según el radio seleccionado al cargar
    const tipoPagoHidden = document.getElementById('tipo_pago_hidden');
    if (tipoPagoHidden) {
        if (radioDeudaIndividual?.checked) {
            tipoPagoHidden.value = 'deuda_individual';
            console.log('🚀 Inicializado como deuda_individual');
        } else if (radioOrdenCompleta?.checked) {
            tipoPagoHidden.value = 'orden_completa';
            console.log('🚀 Inicializado como orden_completa');
        }
    }
    
    // Función para cambiar entre modos
    function cambiarModoPayment(modo) {
        console.log('Cambiando a modo:', modo);
        
        // Actualizar el campo hidden con el tipo de pago
        const tipoPagoHidden = document.getElementById('tipo_pago_hidden');
        if (tipoPagoHidden) {
            tipoPagoHidden.value = modo;
            console.log('✅ tipo_pago_hidden actualizado a:', modo);
        }
        
        if (modo === 'deuda_individual') {
            // Mostrar sección de deuda individual
            if (seccionDeudaIndividual) {
                seccionDeudaIndividual.classList.remove('hidden');
                console.log('Mostrando seccionDeudaIndividual');
            }
            // Ocultar sección de orden completa
            if (seccionOrdenCompleta) {
                seccionOrdenCompleta.classList.add('hidden');
                console.log('Ocultando seccionOrdenCompleta');
            }
            // Mostrar detalles de pago
            if (detallesPagoContainer) {
                detallesPagoContainer.classList.remove('hidden');
                console.log('Mostrando detallesPagoContainer');
            }
            
            // Re-inicializar los event listeners de vouchers
            console.log('🔧 Intentando re-inicializar vouchers...');
            
            // Función para intentar inicializar con reintentos
            function intentarInicializar(intentos = 0) {
                const maxIntentos = 10;
                
                console.log(`⏱️ Intento ${intentos + 1} de ${maxIntentos}`);
                console.log('📍 window.inicializarVouchersDeudaIndividual existe?', typeof window.inicializarVouchersDeudaIndividual);
                
                if (window.inicializarVouchersDeudaIndividual) {
                    window.inicializarVouchersDeudaIndividual();
                    console.log('✅ Event listeners de vouchers re-inicializados');
                } else if (intentos < maxIntentos) {
                    console.warn(`⚠️ Función no disponible aún, reintentando en 100ms...`);
                    setTimeout(() => intentarInicializar(intentos + 1), 100);
                } else {
                    console.error('❌ window.inicializarVouchersDeudaIndividual NO se pudo cargar después de', maxIntentos, 'intentos');
                }
            }
            
            setTimeout(() => intentarInicializar(), 100);
            
            // Pre-llenar con datos de búsqueda unificada
            const datosBusqueda = window.datosBusquedaUnificada?.getDatos();
            const tipoBusqueda = window.datosBusquedaUnificada?.getTipo();
            
            console.log('Datos de búsqueda unificada:', datosBusqueda, tipoBusqueda);
            
            if (datosBusqueda && datosBusqueda.datos) {
                const codigoAlumnoInput = document.getElementById('codigo_alumno');
                const nombreAlumnoInput = document.getElementById('nombre_alumno');
                const datosReales = datosBusqueda.datos; // Extraer los datos reales
                
                console.log('Datos reales extraídos:', datosReales);
                
                // Llenar el código y nombre del alumno
                if (tipoBusqueda === 'estudiante' && datosReales.codigo_educando) {
                    if (codigoAlumnoInput) {
                        codigoAlumnoInput.value = datosReales.codigo_educando;
                    }
                    if (nombreAlumnoInput && datosReales.nombre_completo) {
                        nombreAlumnoInput.value = datosReales.nombre_completo;
                    }
                    // Cargar deudas directamente del endpoint de búsqueda alumno
                    cargarDeudasDirectamente(datosReales);
                } 
                else if (tipoBusqueda === 'orden' && datosReales.codigo_estudiante) {
                    if (codigoAlumnoInput) {
                        codigoAlumnoInput.value = datosReales.codigo_estudiante;
                    }
                    if (nombreAlumnoInput && datosReales.nombre_estudiante) {
                        nombreAlumnoInput.value = datosReales.nombre_estudiante;
                    }
                    // Cargar deudas desde la orden
                    cargarDeudasDesdeOrden(datosReales);
                }
            }
            
            // Cambiar action del formulario al original
            const form = document.getElementById('form');
            if (form && !form.hasAttribute('action')) {
                form.setAttribute('action', '');
            }
        } else if (modo === 'orden_completa') {
            // Ocultar sección de deuda individual
            if (seccionDeudaIndividual) {
                seccionDeudaIndividual.classList.add('hidden');
                console.log('Ocultando seccionDeudaIndividual');
            }
            // Mostrar sección de orden completa
            if (seccionOrdenCompleta) {
                seccionOrdenCompleta.classList.remove('hidden');
                console.log('Mostrando seccionOrdenCompleta');
            }
            // Ocultar detalles de pago
            if (detallesPagoContainer) {
                detallesPagoContainer.classList.add('hidden');
                console.log('Ocultando detallesPagoContainer');
            }
            
            // Pre-llenar con datos de búsqueda unificada
            const datosBusqueda = window.datosBusquedaUnificada?.getDatos();
            const tipoBusqueda = window.datosBusquedaUnificada?.getTipo();
            
            console.log('Modo orden completa - Datos:', datosBusqueda, tipoBusqueda);
            
            if (datosBusqueda && datosBusqueda.datos) {
                const datosReales = datosBusqueda.datos; // Extraer los datos reales
                
                console.log('Datos reales en orden completa:', datosReales);
                
                // Si buscó por orden, mostrar directamente
                if (tipoBusqueda === 'orden') {
                    console.log('Mostrando orden encontrada previamente');
                    mostrarInformacionOrden(datosReales);
                }
                // Si buscó por estudiante, buscar su última orden pendiente
                else if (tipoBusqueda === 'estudiante' && datosReales.codigo_educando) {
                    console.log('Estudiante encontrado, buscando su última orden pendiente...');
                    // Buscar la orden del estudiante usando el endpoint de búsqueda por alumno
                    buscarOrdenPorEstudiante(datosReales.codigo_educando);
                }
            }
        }
    }
    
    // Nueva función para buscar orden por código de estudiante
    async function buscarOrdenPorEstudiante(codigoEstudiante) {
        try {
            console.log('Buscando orden para estudiante:', codigoEstudiante);
            
            // Usar el endpoint que ya existe para buscar alumno (que incluye la orden pendiente)
            const response = await fetch(`/pagos/buscarAlumno/${encodeURIComponent(codigoEstudiante)}`);
            const data = await response.json();
            
            console.log('Respuesta búsqueda alumno para orden:', data);
            
            if (response.ok && data.success && data.id_orden) {
                // El endpoint devuelve id_orden, buscar los detalles completos de la orden
                const ordenResponse = await fetch(`/pagos/info-orden/${data.id_orden}`);
                const ordenData = await ordenResponse.json();
                
                console.log('Datos completos de la orden:', ordenData);
                
                if (ordenResponse.ok && ordenData.success && ordenData.orden) {
                    // Mostrar la información de la orden
                    mostrarInformacionOrden(ordenData.orden);
                } else {
                    console.error('No se pudo obtener los detalles de la orden');
                    mostrarErrorOrden('No se pudo cargar la información de la orden');
                }
            } else if (!response.ok && data.sin_orden) {
                // Caso especial: alumno encontrado pero sin orden pendiente
                mostrarErrorOrden(`Alumno ${data.alumno?.nombre_completo || ''} no tiene una orden de pago pendiente. Debe generar una orden primero.`);
            } else {
                console.error('Estudiante sin orden pendiente');
                mostrarErrorOrden('Este estudiante no tiene una orden de pago pendiente');
            }
        } catch (error) {
            console.error('Error buscando orden del estudiante:', error);
            mostrarErrorOrden('Error al buscar la orden del estudiante');
        }
    }
    
    function mostrarErrorOrden(mensaje) {
        // Actualizar mensaje del header
        const mensajeHeader = document.getElementById('mensaje_orden_header');
        if (mensajeHeader) {
            mensajeHeader.textContent = 'Error al cargar la orden';
        }
        
        if (infoOrdenContainer) {
            infoOrdenContainer.classList.remove('hidden');
            infoOrdenContainer.innerHTML = `
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4">
                    <p class="text-sm text-red-700 dark:text-red-300">
                        ❌ ${mensaje}
                    </p>
                </div>
            `;
        }
    }
    
    // ==========================================
    // FUNCIONES PARA CARGAR DEUDAS DIRECTAMENTE
    // ==========================================
    
    function cargarDeudasDirectamente(datosAlumno) {
        console.log('🔵 cargarDeudasDirectamente - Iniciando con datos:', datosAlumno);
        
        // Los datos ya vienen del endpoint buscarAlumno que incluye las deudas
        const selectDeuda = document.getElementById('select_deuda') || document.getElementById('id_deuda');
        
        console.log('🔵 Select de deuda encontrado:', selectDeuda);
        
        if (!selectDeuda) {
            console.error('❌ No se encontró el select de deudas');
            return;
        }
        
        // Limpiar select
        selectDeuda.innerHTML = '';
        console.log('🔵 Select limpiado');
        
        // Si el alumno tiene deudas (ya vienen en los datos de la búsqueda)
        // Necesitamos buscar nuevamente para obtener las deudas completas
        const url = `/pagos/buscarAlumno/${encodeURIComponent(datosAlumno.codigo_educando)}`;
        console.log('🔵 Haciendo fetch a:', url);
        
        fetch(url)
            .then(response => {
                console.log('🔵 Respuesta recibida:', response.status, response.ok);
                return response.json();
            })
            .then(data => {
                console.log('🔵 Datos JSON parseados:', data);
                console.log('🔵 data.success:', data.success);
                console.log('🔵 data.deudas:', data.deudas);
                console.log('🔵 data.deudas es array?:', Array.isArray(data.deudas));
                console.log('🔵 data.deudas.length:', data.deudas?.length);
                
                if (data.success && Array.isArray(data.deudas) && data.deudas.length > 0) {
                    console.log('✅ Procesando', data.deudas.length, 'deudas');
                    
                    data.deudas.forEach((d, index) => {
                        console.log(`  - Deuda ${index + 1}:`, d);
                        const opt = document.createElement('option');
                        opt.value = d.id_deuda;
                        const mes = d.concepto.split(" ")[0].toUpperCase();
                        opt.textContent = `PERIODO ${d.periodo} - MES ${mes} - ESCALA ${d.escala.toUpperCase()}`;
                        opt.dataset.montoTotal = d.monto_total ?? 0;
                        opt.dataset.montoPagado = d.monto_pagado ?? 0;
                        selectDeuda.appendChild(opt);
                        console.log(`  ✅ Opción agregada: ${opt.textContent}`);
                        console.log(`     - montoTotal: ${opt.dataset.montoTotal}`);
                        console.log(`     - montoPagado: ${opt.dataset.montoPagado}`);
                    });
                    
                    console.log('✅ Todas las opciones agregadas. Select.options.length:', selectDeuda.options.length);
                    
                    // Seleccionar la primera opción
                    if (selectDeuda.options.length > 0) {
                        selectDeuda.selectedIndex = 0;
                        console.log('✅ Primera opción seleccionada');
                        
                        // Llenar manualmente los campos de monto
                        const primeraOpcion = selectDeuda.options[0];
                        const montoTotal = parseFloat(primeraOpcion.dataset.montoTotal || 0);
                        const montoPagado = parseFloat(primeraOpcion.dataset.montoPagado || 0);
                        
                        console.log('📊 Montos a llenar:', { montoTotal, montoPagado });
                        
                        const montoTotalInput = document.getElementById('monto_total_a_pagar');
                        const montoPagadoInput = document.getElementById('monto_pagado');
                        
                        console.log('📊 Inputs encontrados:', { 
                            montoTotalInput: !!montoTotalInput, 
                            montoPagadoInput: !!montoPagadoInput 
                        });
                        
                        if (montoTotalInput) {
                            montoTotalInput.value = `S/ ${montoTotal.toFixed(2)}`;
                            console.log('✅ monto_total_a_pagar llenado:', montoTotalInput.value);
                        }
                        if (montoPagadoInput) {
                            montoPagadoInput.value = `S/ ${montoPagado.toFixed(2)}`;
                            console.log('✅ monto_pagado llenado:', montoPagadoInput.value);
                        }
                    }
                    
                    // Disparar evento change para cargar la primera deuda
                    selectDeuda.dispatchEvent(new Event('change'));
                    console.log('✅ Evento change disparado');
                } else {
                    console.log('⚠️ Sin deudas o respuesta inválida');
                    const opt = document.createElement('option');
                    opt.value = '';
                    opt.textContent = 'Sin deudas pendientes';
                    selectDeuda.appendChild(opt);
                }
            })
            .catch(error => {
                console.error('❌ Error cargando deudas:', error);
            });
    }
    
    async function cargarDeudasDesdeOrden(datosOrden) {
        console.log('Cargando deudas desde la orden:', datosOrden);
        
        const selectDeuda = document.getElementById('select_deuda') || document.getElementById('id_deuda');
        
        if (!selectDeuda) {
            console.error('No se encontró el select de deudas');
            return;
        }
        
        // Limpiar select
        selectDeuda.innerHTML = '';
        
        // Las deudas ya vienen en datosOrden.deudas
        if (datosOrden.deudas && Array.isArray(datosOrden.deudas) && datosOrden.deudas.length > 0) {
            datosOrden.deudas.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d.id_deuda;
                opt.textContent = `PERIODO ${d.periodo} - ${d.concepto}`;
                opt.dataset.montoTotal = d.monto_total ?? 0;
                opt.dataset.montoPagado = d.monto_pagado ?? 0;
                selectDeuda.appendChild(opt);
            });
            
            // Disparar evento change para cargar la primera deuda
            selectDeuda.dispatchEvent(new Event('change'));
        } else {
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = 'Sin deudas en esta orden';
            selectDeuda.appendChild(opt);
        }
    }
    
    // ==========================================
    // FIN DE FUNCIONES DE CARGA DE DEUDAS
    // ==========================================

    
    // Event listeners para los radio buttons
    if (radioDeudaIndividual) {
        radioDeudaIndividual.addEventListener('change', function() {
            if (this.checked) {
                cambiarModoPayment('deuda_individual');
            }
        });
    }
    
    if (radioOrdenCompleta) {
        radioOrdenCompleta.addEventListener('change', function() {
            if (this.checked) {
                cambiarModoPayment('orden_completa');
            }
        });
    }
    
    // ==========================================
    // BÚSQUEDA DE ORDEN
    // ==========================================
    
    if (btnBuscarOrden) {
        btnBuscarOrden.addEventListener('click', async function() {
            const codigoOrden = codigoOrdenInput?.value.trim();
            
            if (!codigoOrden) {
                alert('Por favor, ingrese el código de la orden');
                return;
            }
            
            // Mostrar loading
            btnBuscarOrden.disabled = true;
            btnBuscarOrden.innerHTML = `
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Buscando...
            `;
            
            try {
                // Buscar la orden usando la ruta de Laravel
                const baseUrl = window.Laravel?.routes?.buscarOrden || '/financiera/pagos/buscar-orden/';
                const url = baseUrl + encodeURIComponent(codigoOrden);
                
                console.log('Buscando orden en:', url);
                
                const response = await fetch(url);
                
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || 'Orden no encontrada');
                }
                
                const data = await response.json();
                
                if (data.success) {
                    mostrarInformacionOrden(data.orden);
                } else {
                    alert(data.message || 'No se pudo encontrar la orden');
                }
                
            } catch (error) {
                console.error('Error al buscar orden:', error);
                alert('Error: ' + error.message);
            } finally {
                // Restaurar botón
                btnBuscarOrden.disabled = false;
                btnBuscarOrden.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Buscar
                `;
            }
        });
    }
    
    // Permitir buscar con Enter
    if (codigoOrdenInput) {
        codigoOrdenInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (btnBuscarOrden) btnBuscarOrden.click();
            }
        });
    }
    
    // ==========================================
    // MOSTRAR INFORMACIÓN DE LA ORDEN
    // ==========================================
    
    function mostrarInformacionOrden(orden) {
        console.log('📋 mostrarInformacionOrden - Mostrando datos de orden:', orden);
        
        // IMPORTANTE: Mostrar el contenedor de información
        const infoOrdenContainer = document.getElementById('info_orden_container');
        if (infoOrdenContainer) {
            infoOrdenContainer.classList.remove('hidden');
            console.log('✅ Contenedor info_orden_container mostrado');
        } else {
            console.error('❌ No se encontró info_orden_container');
        }
        
        // Actualizar mensaje del header
        const mensajeHeader = document.getElementById('mensaje_orden_header');
        if (mensajeHeader) {
            mensajeHeader.textContent = `Orden ${orden.codigo_orden || ''} - ${orden.nombre_estudiante || ''}`;
        }
        
        // Llenar información básica con los NUEVOS IDs
        const infoEstudiante = document.getElementById('info_estudiante_orden');
        const infoCodigoEstudiante = document.getElementById('info_codigo_estudiante_orden');
        const infoMontoTotal = document.getElementById('info_monto_total_orden');
        const infoMontoPagado = document.getElementById('info_monto_pagado_orden');
        const infoMontoPendiente = document.getElementById('info_monto_pendiente_orden');
        
        if (infoEstudiante) {
            infoEstudiante.value = orden.nombre_estudiante || '-';
            console.log('✅ Estudiante llenado:', orden.nombre_estudiante);
        } else {
            console.error('❌ No se encontró info_estudiante_orden');
        }
        
        if (infoCodigoEstudiante) {
            infoCodigoEstudiante.value = orden.codigo_estudiante || '-';
            console.log('✅ Código Estudiante llenado:', orden.codigo_estudiante);
        } else {
            console.error('❌ No se encontró info_codigo_estudiante_orden');
        }
        
        if (infoMontoTotal) {
            infoMontoTotal.value = `S/ ${parseFloat(orden.monto_total || 0).toFixed(2)}`;
            console.log('✅ Monto Total llenado:', orden.monto_total);
        } else {
            console.error('❌ No se encontró info_monto_total_orden');
        }
        
        // Calcular monto pagado sumando todos los pagos de la orden
        let totalPagado = 0;
        if (orden.deudas && Array.isArray(orden.deudas)) {
            orden.deudas.forEach(deuda => {
                totalPagado += parseFloat(deuda.monto_pagado || 0);
            });
        }
        
        if (infoMontoPagado) {
            infoMontoPagado.value = `S/ ${totalPagado.toFixed(2)}`;
            console.log('✅ Monto Pagado llenado:', totalPagado);
        } else {
            console.error('❌ No se encontró info_monto_pagado_orden');
        }
        
        if (infoMontoPendiente) {
            infoMontoPendiente.value = `S/ ${parseFloat(orden.monto_pendiente || 0).toFixed(2)}`;
            console.log('✅ Monto Pendiente llenado:', orden.monto_pendiente);
        } else {
            console.error('❌ No se encontró info_monto_pendiente_orden');
        }
        
        // Llenar tabla de deudas
        const tablaDeudas = document.getElementById('tabla_deudas_orden');
        if (tablaDeudas && orden.deudas && Array.isArray(orden.deudas)) {
            tablaDeudas.innerHTML = '';
            
            orden.deudas.forEach((deuda, index) => {
                const montoPagado = parseFloat(deuda.monto_pagado || 0);
                const montoTotal = parseFloat(deuda.monto_total || 0);
                const montoPendiente = montoTotal - montoPagado;
                
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150';
                row.innerHTML = `
                    <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400 font-semibold">${index + 1}</td>
                    <td class="px-4 py-3 text-gray-800 dark:text-gray-200 font-medium">${deuda.concepto || '-'}</td>
                    <td class="px-4 py-3 text-right">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                            S/ ${montoTotal.toFixed(2)}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                            S/ ${montoPagado.toFixed(2)}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold ${
                            montoPendiente > 0 
                                ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' 
                                : 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
                        }">
                            S/ ${montoPendiente.toFixed(2)}
                        </span>
                    </td>
                `;
                tablaDeudas.appendChild(row);
            });
        }
        
        // Input hidden para id_orden
        const idOrdenHidden = document.getElementById('id_orden_hidden');
        if (idOrdenHidden) {
            idOrdenHidden.value = orden.id_orden || '';
        }
        
        // Llenar los campos en la fila superior (Fecha, Código, Nombre)
        const codigoEstudianteOrden = document.getElementById('codigo_estudiante_orden');
        const nombreEstudianteOrden = document.getElementById('nombre_estudiante_orden');
        
        if (codigoEstudianteOrden) {
            codigoEstudianteOrden.value = orden.codigo_estudiante || '';
        }
        
        if (nombreEstudianteOrden) {
            nombreEstudianteOrden.value = orden.nombre_estudiante || '';
        }
        
        // Cambiar el action del formulario
        const form = document.getElementById('form');
        if (form) {
            const actionUrl = window.Laravel?.routes?.registrarPagoOrden || '/pagos/registrar-pago-orden';
            form.setAttribute('action', actionUrl);
            form.setAttribute('method', 'POST');
        }

        // Guardar la orden cargada globalmente para que otros modos (ej. deuda_individual)
        // puedan acceder a ella si el usuario cambia de modo después de la carga
        window.currentOrdenData = orden;

        // Si existe el select de deudas en la vista de Deuda Individual, cargarlo
        // desde la orden (esto asegura que al buscar por orden se muestren las deudas)
        const selectDeuda = document.getElementById('select_deuda') || document.getElementById('id_deuda');
        if (selectDeuda && typeof cargarDeudasDesdeOrden === 'function') {
            try {
                cargarDeudasDesdeOrden(orden);
                console.log('✅ cargarDeudasDesdeOrden invocado desde mostrarInformacionOrden');
            } catch (err) {
                console.error('❌ Error al invocar cargarDeudasDesdeOrden:', err);
            }
        }

        // Disparar evento para que otros componentes sepan que se cargó la orden
        window.dispatchEvent(new CustomEvent('ordenCargada', {
            detail: {
                monto_pendiente: orden.monto_pendiente,
                monto_total: orden.monto_total
            }
        }));
    }
    
    // ==========================================
    // MANEJO DEL SELECT DE DEUDAS EN ORDEN
    // ==========================================
    
    const selectOrden = document.getElementById('select_orden');
    const montoTotalOrdenPagar = document.getElementById('monto_total_a_pagar_orden');
    const montoPagadoOrden = document.getElementById('monto_pagado_orden');
    
    if (selectOrden && montoTotalOrdenPagar && montoPagadoOrden) {
        selectOrden.addEventListener('change', function() {
            const opt = selectOrden.options[selectOrden.selectedIndex];
            
            if (!opt || !opt.value) {
                montoTotalOrdenPagar.value = '';
                montoPagadoOrden.value = '';
                return;
            }
            
            const montoTotal = parseFloat(opt.dataset.montoTotal || 0) || 0;
            const montoPagado = parseFloat(opt.dataset.montoPagado || 0) || 0;
            
            montoTotalOrdenPagar.value = `S/ ${montoTotal.toFixed(2)}`;
            montoPagadoOrden.value = `S/ ${montoPagado.toFixed(2)}`;
        });
    }

    // Listener para select_deuda (Deuda Individual) — rellena montos al cambiar la deuda
    const selectDeudaGlobal = document.getElementById('select_deuda') || document.getElementById('id_deuda');
    if (selectDeudaGlobal) {
        selectDeudaGlobal.addEventListener('change', function() {
            const opt = selectDeudaGlobal.options[selectDeudaGlobal.selectedIndex];
            const montoTotalInput = document.getElementById('monto_total_a_pagar');
            const montoPagadoInput = document.getElementById('monto_pagado');
            const montoPendienteInput = document.getElementById('monto_pendiente');

            if (!opt || !opt.value) {
                if (montoTotalInput) montoTotalInput.value = '';
                if (montoPagadoInput) montoPagadoInput.value = '';
                if (montoPendienteInput) montoPendienteInput.value = '';
                return;
            }

            const montoTotal = parseFloat(opt.dataset.montoTotal || 0) || 0;
            const montoPagado = parseFloat(opt.dataset.montoPagado || 0) || 0;
            const montoPendiente = montoTotal - montoPagado;

            if (montoTotalInput) montoTotalInput.value = `S/ ${montoTotal.toFixed(2)}`;
            if (montoPagadoInput) montoPagadoInput.value = `S/ ${montoPagado.toFixed(2)}`;
            if (montoPendienteInput) montoPendienteInput.value = `S/ ${montoPendiente.toFixed(2)}`;

            console.log('select_deuda change ->', { montoTotal, montoPagado, montoPendiente });
        });
    }
    
    // Inicializar - NO activar ningún modo al inicio
    // Se activará cuando el usuario seleccione después de buscar
    // cambiarModoPayment('deuda_individual');
    
    // ==========================================
    // MANEJO DEL VOUCHER EN ORDEN
    // ==========================================
    
    const btnUploadVoucherOrden = document.getElementById('btnUploadVoucherOrden');
    const voucherOrdenInput = document.getElementById('voucher_orden');
    const voucherLabelOrden = document.getElementById('voucher_label_orden');
    
    if (btnUploadVoucherOrden && voucherOrdenInput) {
        btnUploadVoucherOrden.addEventListener('click', function() {
            voucherOrdenInput.click();
        });
        
        voucherOrdenInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const fileName = this.files[0].name;
                const fileSize = (this.files[0].size / 1024 / 1024).toFixed(2); // MB
                if (voucherLabelOrden) {
                    voucherLabelOrden.innerHTML = `
                        <span class="text-green-600 dark:text-green-400 font-medium">✓ ${fileName}</span>
                        <span class="text-gray-500 text-xs">(${fileSize} MB)</span>
                    `;
                }
            }
        });
    }
    
    // ==========================================
    // VALIDACIÓN DE MONTO EN ORDEN
    // ==========================================
    
    const tipoPagoOrden = document.getElementById('tipo_pago_orden');
    const montoPagoOrden = document.getElementById('monto_pago_orden');
    const helperMontoOrden = document.getElementById('helper_monto_orden');
    
    if (tipoPagoOrden && montoPagoOrden && helperMontoOrden) {
        let montoPendienteOrden = 0;
        
        // Actualizar monto pendiente cuando se carga la orden
        window.addEventListener('ordenCargada', function(e) {
            montoPendienteOrden = parseFloat(e.detail.monto_pendiente) || 0;
        });
        
        tipoPagoOrden.addEventListener('change', function() {
            const tipo = this.value;
            
            if (tipo === 'orden_completa') {
                montoPagoOrden.value = montoPendienteOrden.toFixed(2);
                montoPagoOrden.readOnly = true;
                helperMontoOrden.textContent = 'El monto debe ser el total pendiente';
                helperMontoOrden.className = 'text-xs mt-1 text-blue-600 dark:text-blue-400';
            } else if (tipo === 'orden_parcial') {
                montoPagoOrden.value = '';
                montoPagoOrden.readOnly = false;
                helperMontoOrden.textContent = 'Ingrese un monto menor al pendiente';
                helperMontoOrden.className = 'text-xs mt-1 text-amber-600 dark:text-amber-400';
            } else {
                montoPagoOrden.value = '';
                montoPagoOrden.readOnly = false;
                helperMontoOrden.textContent = '';
            }
        });
        
        montoPagoOrden.addEventListener('input', function() {
            const monto = parseFloat(this.value) || 0;
            const tipo = tipoPagoOrden.value;
            
            if (tipo === 'orden_parcial') {
                if (monto > montoPendienteOrden) {
                    helperMontoOrden.textContent = `⚠️ El monto no puede ser mayor al pendiente (S/ ${montoPendienteOrden.toFixed(2)})`;
                    helperMontoOrden.className = 'text-xs mt-1 text-red-600 dark:text-red-400 font-semibold';
                } else if (monto === montoPendienteOrden) {
                    helperMontoOrden.textContent = '💡 Este monto paga toda la orden. Use "Pago Completo" en su lugar';
                    helperMontoOrden.className = 'text-xs mt-1 text-amber-600 dark:text-amber-400';
                } else if (monto > 0) {
                    helperMontoOrden.textContent = `✓ Monto válido. Restará S/ ${(montoPendienteOrden - monto).toFixed(2)}`;
                    helperMontoOrden.className = 'text-xs mt-1 text-green-600 dark:text-green-400';
                } else {
                    helperMontoOrden.textContent = 'Ingrese un monto mayor a 0';
                    helperMontoOrden.className = 'text-xs mt-1 text-gray-500 dark:text-gray-400';
                }
            }
        });
    }

    // ==========================================
    // MANEJO DE VOUCHERS EN DETALLES DE PAGO (2 DETALLES)
    // ==========================================
    
    // Vouchers de orden (2 detalles)
    [1, 2].forEach(n => {
        const input = document.getElementById(`voucher_path_orden_${n}`);
        const btn = document.getElementById(`btnUploadVoucher_orden_${n}`);
        const label = document.getElementById(`voucher_label_orden_${n}`);

        if (btn && input && label) {
            btn.addEventListener('click', () => input.click());
            
            input.addEventListener('change', () => {
                if (input.files.length > 0) {
                    const fileName = input.files[0].name;
                    const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2);
                    label.innerHTML = `<span class="text-green-600 dark:text-green-400 font-medium">✓ ${fileName}</span> <span class="text-gray-500 text-xs">(${fileSize} MB)</span>`;
                } else {
                    label.textContent = 'Seleccionar';
                }
            });
        }
    });

    // Control de visibilidad de vouchers según método de pago (solo transferencia y yape)
    [1, 2].forEach(n => {
        const metodoPago = document.getElementById(`metodo_pago_orden_${n}`);
        const voucherContainer = document.getElementById(`voucher_container_orden_${n}`);
        const voucherInput = document.getElementById(`voucher_path_orden_${n}`);
        const reciboInput = document.getElementById(`detalle_recibo_orden_${n}`);

        // Variable para almacenar el handler actual
        let currentInputHandler = null;

        if (metodoPago && voucherContainer) {
            metodoPago.addEventListener('change', () => {
                const valor = metodoPago.value;
                
                // Solo mostrar voucher para transferencia y yape
                if (valor === 'transferencia' || valor === 'yape') {
                    voucherContainer.classList.remove('hidden');
                    voucherContainer.classList.add('flex');
                } else {
                    voucherContainer.classList.remove('flex');
                    voucherContainer.classList.add('hidden');
                    // Limpiar el input si se oculta
                    if (voucherInput) {
                        voucherInput.value = '';
                        const label = document.getElementById(`voucher_label_orden_${n}`);
                        if (label) label.textContent = 'Seleccionar';
                    }
                }

                // Habilitar/deshabilitar input de número de operación
                if (reciboInput) {
                    // Remover el event listener anterior si existe
                    if (currentInputHandler) {
                        reciboInput.removeEventListener('input', currentInputHandler);
                    }

                    if (valor && valor !== '') {
                        reciboInput.disabled = false;
                        // ⚠️ LIMPIAR el número de operación al cambiar método de pago
                        reciboInput.value = '';
                        
                        // Aplicar restricciones según método de pago
                        switch(valor.toLowerCase()) {
                            case 'yape':
                            case 'plin':
                                reciboInput.maxLength = 8;
                                reciboInput.pattern = '[0-9]{6,8}';
                                reciboInput.placeholder = 'Ingrese 6 a 8 dígitos';
                                reciboInput.setAttribute('inputmode', 'numeric');
                                reciboInput.setAttribute('type', 'text');
                                // Crear y guardar el nuevo handler
                                currentInputHandler = function(e) {
                                    this.value = this.value.replace(/[^0-9]/g, '');
                                };
                                reciboInput.addEventListener('input', currentInputHandler);
                                break;
                            case 'transferencia':
                                reciboInput.maxLength = 11;
                                reciboInput.pattern = '[0-9]{11}';
                                reciboInput.placeholder = 'Ingrese 11 dígitos';
                                reciboInput.setAttribute('inputmode', 'numeric');
                                reciboInput.setAttribute('type', 'text');
                                // Crear y guardar el nuevo handler
                                currentInputHandler = function(e) {
                                    this.value = this.value.replace(/[^0-9]/g, '');
                                };
                                reciboInput.addEventListener('input', currentInputHandler);
                                break;
                            case 'tarjeta':
                                reciboInput.maxLength = 13;
                                reciboInput.pattern = '[0-9]{13}';
                                reciboInput.placeholder = 'Ingrese 13 dígitos';
                                reciboInput.setAttribute('inputmode', 'numeric');
                                reciboInput.setAttribute('type', 'text');
                                // Crear y guardar el nuevo handler
                                currentInputHandler = function(e) {
                                    this.value = this.value.replace(/[^0-9]/g, '');
                                };
                                reciboInput.addEventListener('input', currentInputHandler);
                                break;
                            case 'paypal':
                                reciboInput.maxLength = 17;
                                reciboInput.pattern = '[A-Z0-9]{1,17}';
                                reciboInput.placeholder = 'ID de transacción PayPal (17 caracteres)';
                                reciboInput.setAttribute('inputmode', 'text');
                                reciboInput.setAttribute('type', 'text');
                                // Crear y guardar el nuevo handler
                                currentInputHandler = function(e) {
                                    this.value = this.value.replace(/[^A-Za-z0-9]/g, '').toUpperCase();
                                };
                                reciboInput.addEventListener('input', currentInputHandler);
                                break;
                            default:
                                reciboInput.removeAttribute('maxLength');
                                reciboInput.removeAttribute('pattern');
                                reciboInput.placeholder = 'Número de operación';
                                reciboInput.setAttribute('inputmode', 'text');
                                reciboInput.setAttribute('type', 'text');
                                currentInputHandler = null;
                        }
                    } else {
                        reciboInput.disabled = true;
                        reciboInput.value = '';
                        reciboInput.placeholder = 'Seleccione un método de pago';
                        reciboInput.removeAttribute('maxLength');
                        reciboInput.removeAttribute('pattern');
                        currentInputHandler = null;
                    }
                }
            });
        }
    });

    // Validar montos de detalles de pago en Sección 2 (Orden Completa)
    function validarMontosOrden() {
        const montoTotalOrdenInput = document.getElementById('info_monto_total_orden');
        const monto1Input = document.getElementById('detalle_monto_orden_1');
        const monto2Input = document.getElementById('detalle_monto_orden_2');

        if (!montoTotalOrdenInput || !monto1Input || !monto2Input) return;

        const montoTotal = parseFloat(montoTotalOrdenInput.value.replace(/[^0-9.]/g, '')) || 0;
        const monto1 = parseFloat(monto1Input.value) || 0;
        const monto2 = parseFloat(monto2Input.value) || 0;

        // Calcular resto disponible para cada input
        const restoParaMonto1 = montoTotal - monto2;
        const restoParaMonto2 = montoTotal - monto1;

        // Actualizar placeholders dinámicamente
        if (monto2 > 0 && restoParaMonto1 >= 0) {
            monto1Input.placeholder = `Máximo disponible: S/ ${restoParaMonto1.toFixed(2)}`;
        } else {
            monto1Input.placeholder = '';
        }

        if (monto1 > 0 && restoParaMonto2 >= 0) {
            monto2Input.placeholder = `Máximo disponible: S/ ${restoParaMonto2.toFixed(2)}`;
        } else {
            monto2Input.placeholder = '';
        }

        // Limitar valores al máximo disponible (sin alertas)
        if (monto1 > restoParaMonto1 && restoParaMonto1 >= 0) {
            monto1Input.value = restoParaMonto1.toFixed(2);
        }

        if (monto2 > restoParaMonto2 && restoParaMonto2 >= 0) {
            monto2Input.value = restoParaMonto2.toFixed(2);
        }

        // Si la suma supera, ajustar el último valor modificado
        const sumaTotal = parseFloat(monto1Input.value || 0) + parseFloat(monto2Input.value || 0);
        if (sumaTotal > montoTotal) {
            // Determinar cuál input fue el último modificado y ajustarlo
            const target = document.activeElement;
            if (target === monto1Input) {
                monto1Input.value = (montoTotal - parseFloat(monto2Input.value || 0)).toFixed(2);
            } else if (target === monto2Input) {
                monto2Input.value = (montoTotal - parseFloat(monto1Input.value || 0)).toFixed(2);
            }
        }
    }

    // Formatear monto a dos decimales al salir del campo
    function formatearMontoOrden(input) {
        const valor = parseFloat(input.value);
        if (!isNaN(valor) && valor >= 0) {
            input.value = valor.toFixed(2);
        } else if (input.value !== '') {
            input.value = '0.00';
        }
    }

    // Validar que solo se ingresen números y punto decimal
    function validarNumericoOrden(input) {
        input.value = input.value.replace(/[^0-9.]/g, '');
        // Permitir solo un punto decimal
        const parts = input.value.split('.');
        if (parts.length > 2) {
            input.value = parts[0] + '.' + parts.slice(1).join('');
        }
    }

    // Agregar event listeners para validar montos en Sección 2
    const monto1OrdenInput = document.getElementById('detalle_monto_orden_1');
    const monto2OrdenInput = document.getElementById('detalle_monto_orden_2');

    if (monto1OrdenInput) {
        monto1OrdenInput.addEventListener('input', function() {
            validarNumericoOrden(this);
            validarMontosOrden();
        });
        monto1OrdenInput.addEventListener('blur', function() {
            formatearMontoOrden(this);
            validarMontosOrden();
        });
    }
    
    if (monto2OrdenInput) {
        monto2OrdenInput.addEventListener('input', function() {
            validarNumericoOrden(this);
            validarMontosOrden();
        });
        monto2OrdenInput.addEventListener('blur', function() {
            formatearMontoOrden(this);
            validarMontosOrden();
        });
    }

    // ==========================================
    // MANEJADOR DE SUBMIT DEL FORMULARIO
    // ==========================================
    const form = document.getElementById('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const tipoPago = document.getElementById('tipo_pago_hidden')?.value;
            
            console.log('📤 Submit interceptado - Tipo de pago:', tipoPago);
            
            // Si es orden completa, necesitamos transformar los datos
            if (tipoPago === 'orden_completa') {
                e.preventDefault(); // Detener el submit normal
                
                console.log('🔄 Transformando datos para Orden Completa...');
                
                // Obtener el id_orden del campo hidden
                const idOrden = document.getElementById('id_orden_hidden')?.value;
                
                if (!idOrden) {
                    alert('Error: No se ha seleccionado una orden de pago.');
                    return false;
                }
                
                // Obtener datos del PRIMER detalle (solo usamos el primero para orden)
                const metodoPago1 = document.getElementById('metodo_pago_orden_1')?.value;
                const numeroOperacion1 = document.getElementById('detalle_recibo_orden_1')?.value;
                const monto1 = document.getElementById('detalle_monto_orden_1')?.value;
                const fecha1 = document.getElementById('detalle_fecha_orden_1')?.value; // Ya viene en formato Y-m-d
                const observaciones = document.getElementById('observaciones')?.value || '';
                
                // Verificar si hay segundo detalle con datos
                const metodoPago2 = document.getElementById('metodo_pago_orden_2')?.value;
                const numeroOperacion2 = document.getElementById('detalle_recibo_orden_2')?.value;
                const monto2 = document.getElementById('detalle_monto_orden_2')?.value;
                const fecha2 = document.getElementById('detalle_fecha_orden_2')?.value;
                
                const tieneSegundoDetalle = metodoPago2 || numeroOperacion2 || monto2 || fecha2;
                
                if (tieneSegundoDetalle) {
                    alert('⚠️ Para Orden Completa solo se permite UN detalle de pago.\n\nPor favor, elimine los datos del segundo detalle.');
                    return false;
                }
                
                // Obtener el monto total de la orden para determinar tipo_pago
                const montoTotalOrdenInput = document.getElementById('info_monto_total_orden');
                const montoTotalOrdenTexto = montoTotalOrdenInput?.value?.replace('S/', '').replace(',', '').trim() || '0';
                const montoTotalOrden = parseFloat(montoTotalOrdenTexto);
                const montoIngresado = parseFloat(monto1);
                
                // Determinar si es pago completo o parcial
                const diferencia = Math.abs(montoTotalOrden - montoIngresado);
                const esPagoCompleto = diferencia < 0.01; // Tolerancia de 1 centavo
                
                const tipoPagoOrden = esPagoCompleto ? 'orden_completa' : 'orden_parcial';
                
                console.log('💰 Monto total orden:', montoTotalOrden);
                console.log('💰 Monto ingresado:', montoIngresado);
                console.log('📊 Tipo pago determinado:', tipoPagoOrden);
                
                // Obtener el voucher si existe
                const voucherInput = document.getElementById('voucher_path_orden_1');
                const voucherFile = voucherInput?.files[0];
                
                // Crear FormData para enviar
                const formData = new FormData();
                formData.append('_token', document.querySelector('input[name="_token"]').value);
                formData.append('id_orden', idOrden);
                formData.append('tipo_pago', tipoPagoOrden);
                formData.append('monto', monto1);
                formData.append('metodo_pago', metodoPago1);
                formData.append('numero_operacion', numeroOperacion1);
                formData.append('fecha_pago', fecha1);
                formData.append('observaciones', observaciones);
                
                if (voucherFile) {
                    formData.append('voucher', voucherFile);
                }
                
                // Enviar con fetch a la ruta correcta
                const registerUrl = '/pagos/registrar-pago-orden';
                
                fetch(registerUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        return response.json().catch(() => {
                            // Si no es JSON, redirigir manualmente
                            window.location.href = '/pagos?created=true';
                        });
                    } else {
                        return response.json().then(data => {
                            throw data;
                        });
                    }
                })
                .then(data => {
                    window.location.href = '/pagos?created=true';
                })
                .catch(error => {
                    // Mostrar errores de validación si existen
                    if (error.errors) {
                        let mensajeError = 'Errores de validación:\n\n';
                        for (let campo in error.errors) {
                            mensajeError += `• ${error.errors[campo].join('\n• ')}\n`;
                        }
                        alert(mensajeError);
                    } else if (error.message) {
                        alert('Error: ' + error.message);
                    } else {
                        alert('Error al procesar el pago. Por favor intente nuevamente.');
                    }
                });
                
                return false; 
            }
            
            // Si es deuda_individual, dejar que el form se envíe normalmente
            return true;
        });
    }
});

