// ==========================================
// BÚSQUEDA UNIFICADA PARA PAGOS
// Busca por código de estudiante o código de orden
// ==========================================

document.addEventListener('DOMContentLoaded', function() {
    const btnBuscarUnificado = document.getElementById('btnBuscarUnificado');
    const codigoBusquedaInput = document.getElementById('codigo_busqueda_unificado');
    const helperBusqueda = document.getElementById('helper_busqueda');
    const infoEstudianteDiv = document.getElementById('info_estudiante_encontrado');
    const tipoPagoContainer = document.getElementById('tipo_pago_container');
    
    // Variables para almacenar los datos encontrados
    let datosEncontrados = null;
    let tipoBusqueda = null; // 'estudiante' o 'orden'
    
    // Búsqueda al hacer clic
    if (btnBuscarUnificado) {
        btnBuscarUnificado.addEventListener('click', realizarBusqueda);
    }
    
    // Búsqueda al presionar Enter
    if (codigoBusquedaInput) {
        codigoBusquedaInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                realizarBusqueda();
            }
        });
    }
    
    async function realizarBusqueda() {
        const codigo = codigoBusquedaInput?.value.trim();
        
        if (!codigo) {
            mostrarError('Por favor, ingrese un código para buscar');
            return;
        }
        
        // Determinar tipo de búsqueda por el formato del código
        // Si empieza con "OP-" es orden, si es numérico de 6 dígitos es estudiante
        const esOrden = codigo.toUpperCase().startsWith('OP-');
        const esEstudiante = /^\d{6}$/.test(codigo); // Solo 6 dígitos
        
        if (!esOrden && !esEstudiante) {
            mostrarError('Formato de código no válido. Use 6 dígitos para estudiante o formato OP-YYYY-#### para orden');
            return;
        }
        
        tipoBusqueda = esOrden ? 'orden' : 'estudiante';
        
        // Mostrar loading
        btnBuscarUnificado.disabled = true;
        btnBuscarUnificado.innerHTML = `
            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Buscando...
        `;
        
        try {
            if (tipoBusqueda === 'orden') {
                await buscarPorOrden(codigo);
            } else {
                await buscarPorEstudiante(codigo);
            }
        } catch (error) {
            console.error('Error en búsqueda:', error);
            mostrarError(error.message);
        } finally {
            restaurarBoton();
        }
    }
    
    async function buscarPorOrden(codigoOrden) {
        const baseUrl = window.Laravel?.routes?.buscarOrden || '/financiera/pagos/buscar-orden/';
        const url = baseUrl + encodeURIComponent(codigoOrden);
        
        const response = await fetch(url);
        const data = await response.json();
        
        if (!response.ok) {
            // Si la orden está vencida, mostrar mensaje especial
            if (data.orden_vencida && data.alumno && data.orden) {
                datosEncontrados = {
                    tipo: 'orden',
                    orden_vencida: true,
                    alumno: data.alumno,
                    orden_info: data.orden
                };
                mostrarOrdenVencida(data.alumno, data.orden);
                return;
            }
            
            throw new Error(data.message || 'Orden no encontrada');
        }
        
        if (data.success) {
            datosEncontrados = {
                tipo: 'orden',
                datos: data.orden
            };
            mostrarInformacionOrden(data.orden);
        } else {
            throw new Error(data.message || 'No se pudo encontrar la orden');
        }
    }
    
    async function buscarPorEstudiante(codigoEstudiante) {
        // Usar la ruta existente de buscar alumno
        const url = `/pagos/buscarAlumno/${encodeURIComponent(codigoEstudiante)}`;
        
        console.log('Buscando estudiante:', codigoEstudiante, 'en URL:', url);
        
        const response = await fetch(url);
        const data = await response.json(); // Siempre parsear el JSON primero
        
        console.log('Respuesta completa:', response.ok, data);
        
        if (!response.ok) {
            console.error('Error en respuesta:', data);
            
            // Si tiene sin_orden, mostrar mensaje especial
            if (data.sin_orden && data.alumno) {
                datosEncontrados = {
                    tipo: 'estudiante',
                    datos: data.alumno,
                    sin_orden: true
                };
                mostrarInformacionEstudiante(data.alumno, true, 'sin_orden');
                return;
            }
            
            // Si la orden está vencida, mostrar mensaje especial
            if (data.orden_vencida && data.alumno && data.orden) {
                datosEncontrados = {
                    tipo: 'estudiante',
                    datos: data.alumno,
                    orden_vencida: true,
                    orden_info: data.orden
                };
                mostrarInformacionEstudiante(data.alumno, true, 'orden_vencida', data.orden);
                return;
            }
            
            throw new Error(data.message || 'Estudiante no encontrado');
        }
        
        console.log('Respuesta exitosa:', data);
        
        if (data && data.success && data.alumno) {
            datosEncontrados = {
                tipo: 'estudiante',
                datos: data.alumno
            };
            mostrarInformacionEstudiante(data.alumno);
        } else {
            throw new Error('No se pudo obtener la información del estudiante');
        }
    }
    
    function mostrarInformacionOrden(orden) {
        mostrarOpcionesTipoPago(true);
        
        helperBusqueda.innerHTML = '✅ Orden encontrada. Seleccione el tipo de pago a continuación.';
        helperBusqueda.className = 'text-xs mt-1 text-green-600 dark:text-green-400 font-medium';
    }
    
    function mostrarOrdenVencida(alumno, orden) {
        // No mostrar opciones de pago
        if (tipoPagoContainer) {
            tipoPagoContainer.classList.add('hidden');
        }
        
        helperBusqueda.innerHTML = `
            <div class="flex items-start gap-3 bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 border-l-4 border-red-500 dark:border-red-400 rounded-lg p-4 shadow-md mt-3">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-bold text-red-800 dark:text-red-300 mb-1 flex items-center gap-2">
                        Orden de Pago Vencida
                    </h4>
                    <p class="text-sm text-red-700 dark:text-red-300 leading-relaxed">
                        La orden <span class="font-bold bg-red-100 dark:bg-red-800/40 px-2 py-0.5 rounded">${orden.codigo_orden}</span> 
                        del estudiante <span class="font-semibold">${alumno.nombre_completo}</span> 
                        venció el <span class="font-bold">${orden.fecha_vencimiento}</span>.
                    </p>
                    <div class="flex items-start gap-2 mt-2 text-sm text-red-600 dark:text-red-400 font-medium">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                        <span>Por favor, genere una nueva orden de pago actualizada antes de continuar.</span>
                    </div>
                </div>
            </div>
        `;
        helperBusqueda.className = '';
    }
    
    function mostrarInformacionEstudiante(alumno, tieneProblema = false, tipoProblema = null, ordenInfo = null) {
        // Ya no mostramos el contenedor de información encontrada
        // La información se muestra directamente en cada sección
        
        if (tieneProblema) {
            // No mostrar opciones de pago si hay problemas
            tipoPagoContainer.classList.add('hidden');
            
            if (tipoProblema === 'sin_orden') {
                helperBusqueda.innerHTML = `
                    <div class="flex items-start gap-3 bg-gradient-to-r from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20 border-l-4 border-yellow-500 dark:border-yellow-400 rounded-lg p-4 shadow-md mt-3">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-yellow-800 dark:text-yellow-300 mb-1 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                Sin Orden de Pago
                            </h4>
                            <p class="text-sm text-yellow-700 dark:text-yellow-300 leading-relaxed">
                                El estudiante <span class="font-semibold">${alumno.nombre_completo}</span> no tiene una orden de pago pendiente.
                            </p>
                            <div class="flex items-start gap-2 mt-2 text-sm text-yellow-600 dark:text-yellow-400 font-medium">
                                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                                <span>Por favor, genere una orden de pago primero.</span>
                            </div>
                        </div>
                    </div>
                `;
                helperBusqueda.className = '';
            } else if (tipoProblema === 'orden_vencida' && ordenInfo) {
                helperBusqueda.innerHTML = `
                    <div class="flex items-start gap-3 bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 border-l-4 border-red-500 dark:border-red-400 rounded-lg p-4 shadow-md mt-3">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-red-800 dark:text-red-300 mb-1 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                Orden de Pago Vencida
                            </h4>
                            <p class="text-sm text-red-700 dark:text-red-300 leading-relaxed">
                                La orden <span class="font-bold bg-red-100 dark:bg-red-800/40 px-2 py-0.5 rounded">${ordenInfo.codigo_orden}</span> 
                                venció el <span class="font-bold">${ordenInfo.fecha_vencimiento}</span>.
                            </p>
                            <div class="flex items-start gap-2 mt-2 text-sm text-red-600 dark:text-red-400 font-medium">
                                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                                <span>Por favor, genere una nueva orden de pago actualizada.</span>
                            </div>
                        </div>
                    </div>
                `;
                helperBusqueda.className = '';
            }
        } else {
            // SIEMPRE mostrar AMBAS opciones, sin importar qué se buscó
            mostrarOpcionesTipoPago(true);
            helperBusqueda.innerHTML = '✅ Estudiante encontrado. Seleccione el tipo de pago a continuación.';
            helperBusqueda.className = 'text-xs mt-1 text-green-600 dark:text-green-400 font-medium';
        }
    }
    
    function mostrarOpcionesTipoPago(mostrarAmbas) {
        if (!tipoPagoContainer) return;
        
        // SIEMPRE mostrar el contenedor
        tipoPagoContainer.classList.remove('hidden');
        
        const labelOrdenCompleta = document.getElementById('label_orden_completa');
        
        // SIEMPRE mostrar ambas opciones (parámetro ignorado ahora)
        if (labelOrdenCompleta) {
            labelOrdenCompleta.classList.remove('hidden');
        }
        
        // NO auto-seleccionar nada, el usuario debe elegir manualmente
    }
    
    function mostrarError(mensaje) {
        if (helperBusqueda) {
            helperBusqueda.innerHTML = `❌ ${mensaje}`;
            helperBusqueda.className = 'text-xs mt-1 text-red-600 dark:text-red-400 font-medium';
        }
        
        // Ocultar tipo de pago si estaba visible
        if (tipoPagoContainer) {
            tipoPagoContainer.classList.add('hidden');
        }
    }
    
    function restaurarBoton() {
        if (btnBuscarUnificado) {
            btnBuscarUnificado.disabled = false;
            btnBuscarUnificado.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Buscar
            `;
        }
    }
    
    // Exponer datos para que otros scripts los usen
    window.datosBusquedaUnificada = {
        getDatos: () => datosEncontrados,
        getTipo: () => tipoBusqueda
    };
});
