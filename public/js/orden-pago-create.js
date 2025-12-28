document.addEventListener('DOMContentLoaded', function() {
    const btnBuscarAlumno = document.getElementById('btnBuscarAlumno');
    const btnLimpiarBusqueda = document.getElementById('btnLimpiarBusqueda');
    const btnGenerarOrden = document.getElementById('btnGenerarOrden');
    const codigoAlumnoInput = document.getElementById('codigo_alumno');
    const errorCodigo = document.getElementById('error_codigo_alumno');
    
    const contenedorDeuda = document.getElementById('contenedorDeuda');
    const contenedorMeses = document.getElementById('contenedorMeses');
    const contenedorFecha = document.getElementById('contenedorFecha');
    const contenedorResumen = document.getElementById('contenedorResumen');

    const formOrdenPago = document.getElementById('formOrdenPago');
    let alumnoData = null;
    let montoMensualActual = 0;
    let alumnoTieneDeudasAnteriores = false; // Variable para guardar el estado del backend
    let mesActualAplicaMora = false; // Indica si el mes actual tiene mora
    let mesActualPorcentajeMora = 0; // Porcentaje de mora del mes actual
    let mesActualDescripcionPolitica = ''; // Descripción de la política de mora

    const urlParams = new URLSearchParams(window.location.search);
    const codigoParam = urlParams.get('codigo');
    if (codigoParam && codigoAlumnoInput) {
        codigoAlumnoInput.value = codigoParam;
        setTimeout(() => {
            btnBuscarAlumno.click();
        }, 500);
    }

    function limpiarFormulario() {
        codigoAlumnoInput.value = '';
        alumnoTieneDeudasAnteriores = false; // Resetear el estado
        mesActualAplicaMora = false; // Resetear mora del mes actual
        mesActualPorcentajeMora = 0; // Resetear porcentaje
        mesActualDescripcionPolitica = ''; // Resetear descripción de la política
        
        if (errorCodigo) {
            errorCodigo.textContent = '';
            errorCodigo.classList.add('hidden');
        }
        if (codigoAlumnoInput) {
            codigoAlumnoInput.classList.remove('border-red-500');
        }

        const mensajes = [
            'mensajeDeudaAnterior',
            'mensajeDeudaBloqueante',
            'mensajeOrdenReciente',
            'mensajeSinDeudas',
            'mensajeAlDia',
            'mensajeDeudasMultiples',
            'mensajeBloqueoOrden',
            'error_servidor',
            'error_deudas'
        ];
        mensajes.forEach(id => {
            const elemento = document.getElementById(id);
            if (elemento) {
                elemento.remove();
            }
        });
        
        if (contenedorDeuda) contenedorDeuda.classList.add('hidden');
        if (contenedorMeses) contenedorMeses.classList.add('hidden');
        if (contenedorFecha) contenedorFecha.classList.add('hidden');
        if (contenedorResumen) contenedorResumen.classList.add('hidden');
        
        const errorDeudas = document.getElementById('error_deudas');
        if (errorDeudas) {
            errorDeudas.classList.add('hidden');
            const errorTexto = document.getElementById('error_deudas_texto');
            if (errorTexto) errorTexto.textContent = '';
        }

        const campos = ['id_alumno', 'id_matricula', 'nombre_completo', 'dni_alumno', 'nivel_educativo', 'grado', 'seccion', 'escala', 'monto_mensual'];
        campos.forEach(campo => {
            const elemento = document.getElementById(campo);
            if (elemento) {
                elemento.value = '';
                if (elemento.hasAttribute('readonly')) {
                    if (campo === 'nombre_completo') elemento.placeholder = 'Busque un alumno primero';
                    else elemento.placeholder = '-';
                }
            }
        });
        
        // Resetear el campo de deudas anteriores
        document.getElementById('tiene_deudas_anteriores').value = '0';
        
        const listaDeudas = document.getElementById('listaDeudasCheckbox');
        if (listaDeudas) listaDeudas.innerHTML = '';
        
        const inputAdelantar = document.getElementById('meses_adelantar');
        if (inputAdelantar) {
            inputAdelantar.value = '0';
            inputAdelantar.max = '12';
        }
        
        const preguntaAdelantar = document.getElementById('preguntaAdelantar');
        const inputMesesAdelantar = document.getElementById('inputMesesAdelantar');
        if (preguntaAdelantar) preguntaAdelantar.classList.remove('hidden');
        if (inputMesesAdelantar) inputMesesAdelantar.classList.add('hidden');
        
        const resumenMeses = document.getElementById('resumen_meses');
        const resumenBase = document.getElementById('resumen_base');
        const resumenDescuento = document.getElementById('resumen_descuento');
        const resumenMora = document.getElementById('resumen_mora');
        const resumenTotal = document.getElementById('resumen_total');
        
        if (resumenMeses) resumenMeses.textContent = '1 mes';
        if (resumenBase) resumenBase.textContent = 'S/ 0.00';
        if (resumenDescuento) resumenDescuento.textContent = '- S/ 0.00';
        if (resumenMora) resumenMora.textContent = '+ S/ 0.00';
        if (resumenTotal) resumenTotal.textContent = 'S/ 0.00';
        
        // Ocultar el contenedor de descripción de política
        const contenedorDetalle = document.getElementById('contenedor_detalle_politica');
        if (contenedorDetalle) contenedorDetalle.classList.add('hidden');
        
        if (btnGenerarOrden) {

            btnGenerarOrden.disabled = true;
            btnGenerarOrden.classList.add('opacity-50', 'cursor-not-allowed');
        }
        
        alumnoData = null;
        montoMensualActual = 0;
        alumnoTieneDeudasAnteriores = false;
        mesActualAplicaMora = false;
        mesActualPorcentajeMora = 0;
        mesActualDescripcionPolitica = '';
        
        if (codigoAlumnoInput) {
            codigoAlumnoInput.focus();
        }        
    }

    if (btnLimpiarBusqueda) {
        btnLimpiarBusqueda.addEventListener('click', function(e) {
            e.preventDefault();
            limpiarFormulario();
        });
    }

    // Función para procesar los datos del alumno y llenar el formulario
    function procesarDatosAlumno(data) {

        
        // Verificar si el alumno está completamente al día ANTES de cualquier otro procesamiento
        if (data.al_dia) {

            
            // Ocultar secciones de deudas y fechas
            if (contenedorDeuda) contenedorDeuda.classList.add('hidden');
            if (contenedorMeses) contenedorMeses.classList.add('hidden');
            if (contenedorFecha) contenedorFecha.classList.add('hidden');
            if (contenedorResumen) contenedorResumen.classList.add('hidden');
            
            // Deshabilitar el botón de generar
            if (btnGenerarOrden) {

                btnGenerarOrden.disabled = true;
                btnGenerarOrden.classList.add('opacity-50', 'cursor-not-allowed');
            }
            
            // Crear y mostrar mensaje de éxito
            if (!document.getElementById('mensajeAlDia')) {
                const mensajeDiv = document.createElement('div');
                mensajeDiv.id = 'mensajeAlDia';
                mensajeDiv.className = 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg mb-4 col-span-1 sm:col-span-2 lg:col-span-3';
                mensajeDiv.innerHTML = `
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">${data.message || '¡Felicidades! El estudiante está completamente al día con sus pagos.'}</span>
                    </div>
                `;
                const formSection = document.getElementById('formOrdenPago');
                if (formSection) {
                    formSection.insertBefore(mensajeDiv, formSection.firstChild);

                }
            }
            
            return;
        }
        
        if (!data.success) {
            throw new Error(data.error || 'Error al buscar alumno');
        }
        
        alumnoData = data;
        
        // Llenar datos del alumno en los campos del formulario
        document.getElementById('id_alumno').value = data.alumno.id_alumno;
        document.getElementById('id_matricula').value = data.matricula.id_matricula;
        document.getElementById('nombre_completo').value = data.alumno.nombre_completo;
        document.getElementById('dni_alumno').value = data.alumno.dni || '';
        
        document.getElementById('nivel_educativo').value = data.matricula.nivel_educativo || '';
        document.getElementById('grado').value = data.matricula.grado || '';
        document.getElementById('seccion').value = data.matricula.seccion || '';
        
        const escala = data.matricula.escala || 'E';
        document.getElementById('escala').value = escala;
        
        // Calcular monto mensual según la escala
        const montosEscala = {
            'A': 500.00,
            'B': 400.00,
            'C': 300.00,
            'D': 200.00,
            'E': 100.00
        };
        
        const montoMensual = montosEscala[escala.toUpperCase()] || 100.00;
        document.getElementById('monto_mensual').value = 'S/ ' + montoMensual.toFixed(2);

        const listaDeudasCheckbox = document.getElementById('listaDeudasCheckbox');
        if (listaDeudasCheckbox) {
            listaDeudasCheckbox.innerHTML = '';
        }
        
        // Verificar si debe bloquear la generación de orden
        if (data.bloquear_generacion) {
            if (contenedorDeuda) contenedorDeuda.classList.add('hidden');
            if (contenedorMeses) contenedorMeses.classList.add('hidden');
            if (contenedorFecha) contenedorFecha.classList.add('hidden');
            if (contenedorResumen) contenedorResumen.classList.add('hidden');
            
            // Remover mensajes anteriores
            const mensajeAnterior = document.getElementById('mensajeBloqueoOrden');
            if (mensajeAnterior) mensajeAnterior.remove();
            
            const mensajeDiv = document.createElement('div');
            mensajeDiv.id = 'mensajeBloqueoOrden';
            
            if (data.orden_vigente) {
                // Orden NO vencida (vigente)
                mensajeDiv.className = 'sm:col-span-3 lg:col-span-3 mt-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-l-4 border-blue-500 dark:border-blue-400 rounded-lg p-5 shadow-md';
                mensajeDiv.innerHTML = `
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <svg class="w-7 h-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-base font-bold text-blue-900 dark:text-blue-200 mb-2 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                Ya existe una orden de pago vigente
                            </h4>
                            <p class="text-sm text-blue-800 dark:text-blue-300 leading-relaxed mb-3">
                                El estudiante ya tiene una orden de pago activa (<span class="font-bold bg-blue-100 dark:bg-blue-800/40 px-2 py-0.5 rounded">${data.ultima_orden}</span>) 
                                que vence el <span class="font-bold">${data.fecha_vencimiento}</span>.
                            </p>
                            <div class="bg-blue-100 dark:bg-blue-800/30 rounded-lg p-3 mt-3">
                                <p class="text-sm text-blue-900 dark:text-blue-200 font-medium mb-2 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Podrá generar una nueva orden cuando:
                                </p>
                                <ul class="text-sm text-blue-800 dark:text-blue-300 ml-6 space-y-1 list-disc">
                                    <li>Se pague completamente la orden actual, o</li>
                                    <li>Se cumpla la fecha de vencimiento (${data.fecha_vencimiento})</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                `;
            } else if (data.orden_vencida_con_deuda) {
                // Orden vencida CON deuda pendiente
                mensajeDiv.className = 'sm:col-span-3 lg:col-span-3 mt-4 bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 border-l-4 border-red-500 dark:border-red-400 rounded-lg p-5 shadow-md';
                mensajeDiv.innerHTML = `
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-base font-bold text-red-900 dark:text-red-200 mb-2 flex items-center gap-2">
                                Orden de pago vencida con deuda pendiente
                            </h4>
                            <p class="text-sm text-red-800 dark:text-red-300 leading-relaxed mb-2">
                                La orden <span class="font-bold bg-red-100 dark:bg-red-800/40 px-2 py-0.5 rounded">${data.ultima_orden}</span> 
                                venció el <span class="font-bold">${data.fecha_vencimiento}</span> y tiene un saldo pendiente de 
                                <span class="font-bold text-lg">S/ ${parseFloat(data.monto_pendiente).toFixed(2)}</span>.
                            </p>
                            <div class="bg-red-100 dark:bg-red-800/30 rounded-lg p-3 mt-3">
                                <p class="text-sm text-red-900 dark:text-red-200 font-medium flex items-start gap-2">
                                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                    <span>Debe completar el pago de esta orden antes de generar una nueva. Diríjase al módulo de <strong>Pagos</strong> para completar el pago pendiente.</span>
                                </p>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            const contenedorPrincipal = document.querySelector('.grid.grid-cols-1');
            if (contenedorPrincipal) {
                contenedorPrincipal.appendChild(mensajeDiv);
            }
            
            btnGenerarOrden.disabled = true;
            
            return;
        }
        
        // Remover mensaje de bloqueo si existe (caso: orden vencida sin pagos - permitir continuar)
        const mensajeBloqueo = document.getElementById('mensajeBloqueoOrden');
        if (mensajeBloqueo) mensajeBloqueo.remove();
        
        if (data.deudas_bloqueantes) {
            if (contenedorDeuda) contenedorDeuda.classList.add('hidden');
            if (contenedorMeses) contenedorMeses.classList.add('hidden');
            if (contenedorFecha) contenedorFecha.classList.add('hidden');
            if (contenedorResumen) contenedorResumen.classList.add('hidden');
            
            if (!document.getElementById('mensajeDeudaBloqueante')) {
                const mensajeDiv = document.createElement('div');
                mensajeDiv.id = 'mensajeDeudaBloqueante';
                mensajeDiv.className = 'sm:col-span-3 lg:col-span-3 mt-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4';
                mensajeDiv.innerHTML = `
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div>
                            <h4 class="font-semibold text-red-800 dark:text-red-300 mb-1">No se puede generar orden de pago</h4>
                            <p class="text-sm text-red-700 dark:text-red-400">Este estudiante tiene deudas de meses anteriores sin pagar. Debe cancelar primero las deudas atrasadas antes de generar nuevas órdenes de pago.</p>
                            <p class="text-sm text-red-700 dark:text-red-400 mt-2"><strong>Nota:</strong> Si ya generó una orden de pago para las deudas atrasadas, debe esperar a que se realice el pago o que la orden venza antes de poder generar una nueva.</p>
                        </div>
                    </div>
                `;
                const contenedorPrincipal = document.querySelector('.grid.grid-cols-1');
                if (contenedorPrincipal) {
                    contenedorPrincipal.appendChild(mensajeDiv);
                }
            }
            
            return;
        }
        
        if (data.deudas && data.deudas.length > 0) {
            montoMensualActual = data.deudas[0].monto_pendiente || 0;
            mesActualAplicaMora = data.deudas[0].aplica_mora || false;
            mesActualPorcentajeMora = parseFloat(data.deudas[0].porcentaje_mora) || 0;
            mesActualDescripcionPolitica = data.deudas[0].descripcion_politica || '';
            
            // Obtener mes y año actual
            const fechaActual = new Date();
            const mesActualNumero = fechaActual.getMonth() + 1; // 1-12
            const anioActual = fechaActual.getFullYear();
            
            const mesesMap = {
                'ENERO': 1, 'FEBRERO': 2, 'MARZO': 3, 'ABRIL': 4,
                'MAYO': 5, 'JUNIO': 6, 'JULIO': 7, 'AGOSTO': 8,
                'SETIEMBRE': 9, 'OCTUBRE': 10, 'NOVIEMBRE': 11, 'DICIEMBRE': 12
            };
            
            data.deudas.forEach((deuda, index) => {
                const deudaDiv = document.createElement('div');
                
                // Detectar si esta deuda es del mes actual
                const conceptoParts = deuda.concepto.split(' ');
                const mesDeuda = mesesMap[conceptoParts[0]] || 0;
                const anioDeuda = conceptoParts.length > 1 ? parseInt(conceptoParts[1]) : 0;
                const esMesActual = (mesDeuda === mesActualNumero && anioDeuda === anioActual && !data.tiene_deudas_anteriores);
                
                // Estilo diferente si es mes actual
                if (esMesActual) {
                    deudaDiv.className = 'flex items-start gap-3 p-3 rounded-lg border-2 border-blue-400 dark:border-blue-500 bg-blue-50 dark:bg-blue-900/20 transition-colors';
                } else {
                    deudaDiv.className = 'flex items-start gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors';
                }
                
                const moraTexto = deuda.aplica_mora ? ` <span class="text-red-600 dark:text-red-400">(+${deuda.porcentaje_mora}% mora)</span>` : '';
                const badgeMesActual = esMesActual ? ' <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">Mes Actual</span>' : '';
                
                deudaDiv.innerHTML = `
                    <input type="checkbox" 
                           id="deuda_${deuda.id_deuda}" 
                           name="deudas[]" 
                           value="${deuda.id_deuda}"
                           data-monto-pendiente="${deuda.monto_pendiente}"
                           data-porcentaje-mora="${deuda.porcentaje_mora || 0}"
                           data-aplica-mora="${deuda.aplica_mora ? '1' : '0'}"
                           data-es-deuda-anterior="${deuda.es_deuda_anterior ? '1' : '0'}"
                           data-meses-disponibles="${deuda.meses_disponibles_adelantar}"
                           data-periodo="${deuda.periodo}"
                           data-fecha-limite="${deuda.fecha_limite}"
                           data-es-mes-actual="${esMesActual ? '1' : '0'}"
                           ${esMesActual ? 'checked disabled' : ''}
                           class="mt-1 w-4 h-4 text-indigo-600 rounded focus:ring-2 focus:ring-indigo-500 deuda-checkbox ${esMesActual ? 'opacity-50 cursor-not-allowed' : ''}">
                    <label for="deuda_${deuda.id_deuda}" class="flex-1 ${esMesActual ? 'cursor-default' : 'cursor-pointer'}">
                        <div class="font-medium text-gray-800 dark:text-white text-sm">
                            ${deuda.concepto}${badgeMesActual}
                        </div>
                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">
                            Pendiente: <span class="font-semibold">S/ ${deuda.monto_pendiente.toFixed(2)}</span> ${moraTexto}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-500 mt-0.5">
                            Vencimiento: ${deuda.fecha_limite}
                        </div>
                        ${esMesActual ? '<div class="text-xs text-blue-600 dark:text-blue-400 mt-1 font-medium">✓ Este mes se incluirá automáticamente en la orden de pago</div>' : ''}
                    </label>
                `;
                
                if (listaDeudasCheckbox) {
                    listaDeudasCheckbox.appendChild(deudaDiv);
                }
            });
            
            if (contenedorDeuda) contenedorDeuda.classList.remove('hidden');
            
            if (data.deudas.length > 1) {
                const mensajeDeudasMultiples = document.getElementById('mensajeDeudasMultiples');
                if (mensajeDeudasMultiples) {
                    mensajeDeudasMultiples.classList.remove('hidden');
                }
            }

            document.querySelectorAll('.deuda-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const errorDeudas = document.getElementById('error_deudas');
                    const listaDeudas = document.getElementById('listaDeudasCheckbox');
                    if (errorDeudas && !errorDeudas.classList.contains('hidden')) {
                        errorDeudas.classList.add('hidden');
                        listaDeudas.classList.remove('border-red-500');
                    }
                    actualizarResumen();
                });
            });
            
            formOrdenPago.dataset.porcentajeDescuento = data.politica_descuento || 10;
            
            const mesesMaximos = data.meses_maximos_adelantar || 0;
            const inputMesesAdelantar = document.getElementById('meses_adelantar');
            if (inputMesesAdelantar) {
                inputMesesAdelantar.max = mesesMaximos;
                inputMesesAdelantar.value = 0;
                inputMesesAdelantar.dataset.mesesMaximos = mesesMaximos;
                
                const labelMeses = document.querySelector('label[for="meses_adelantar"]');
                if (labelMeses && mesesMaximos > 0) {
                    labelMeses.textContent = '¿Cuántos meses desea adelantar?';
                }
            }

            if (data.tiene_deudas_anteriores) {
                alumnoTieneDeudasAnteriores = true;
                document.getElementById('tiene_deudas_anteriores').value = '1';
                if (contenedorDeuda) contenedorDeuda.classList.remove('hidden');
                if (contenedorMeses) contenedorMeses.classList.add('hidden');
                
                const tituloDeudas = document.getElementById('tituloDeudas');
                if (tituloDeudas) {
                    tituloDeudas.textContent = 'Deudas Atrasadas (Selecciona cuáles pagar)';
                    tituloDeudas.classList.add('text-red-600', 'dark:text-red-400');
                }
                
                const infoAdelanto = document.getElementById('infoAdelanto');
                if (infoAdelanto) {
                    infoAdelanto.classList.add('hidden');
                }
                
                if (!document.getElementById('mensajeDeudaAnterior')) {
                    const mensajeDiv = document.createElement('div');
                    mensajeDiv.id = 'mensajeDeudaAnterior';
                    mensajeDiv.className = 'sm:col-span-3 lg:col-span-3 mt-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4';
                    mensajeDiv.innerHTML = `
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div>
                                <h4 class="font-semibold text-yellow-800 dark:text-yellow-300 mb-1">Deudas pendientes de meses anteriores</h4>
                                <p class="text-sm text-yellow-700 dark:text-yellow-400">Este estudiante tiene deudas atrasadas. No podrá adelantar meses hasta regularizar estas deudas.</p>
                                <p class="text-sm text-yellow-700 dark:text-yellow-400 mt-2">Por favor, seleccione las deudas que desea pagar en esta orden.</p>
                            </div>
                        </div>
                    `;
                    const contenedorPrincipal = document.querySelector('.grid.grid-cols-1');
                    if (contenedorPrincipal) {
                        contenedorPrincipal.insertBefore(mensajeDiv, contenedorDeuda);
                    }
                }
            } else {
                alumnoTieneDeudasAnteriores = false;
                document.getElementById('tiene_deudas_anteriores').value = '0';
                
                // Solo mostrar adelanto de pagos si hay meses disponibles
                const mesesDisponibles = data.meses_maximos_adelantar || 0;
                if (contenedorMeses && mesesDisponibles > 0) {
                    contenedorMeses.classList.remove('hidden');
                } else if (contenedorMeses) {
                    contenedorMeses.classList.add('hidden');
                }
                
                const tituloDeudas = document.getElementById('tituloDeudas');
                if (tituloDeudas) {
                    tituloDeudas.textContent = 'Deudas Pendientes';
                    tituloDeudas.classList.remove('text-red-600', 'dark:text-red-400');
                }
            }
        } else {
            // No hay deudas en el array de respuesta
            if (contenedorDeuda) contenedorDeuda.classList.add('hidden');
            if (contenedorMeses) contenedorMeses.classList.add('hidden');
            
            // Si llegamos aquí sin deudas, verificar si hay meses disponibles para adelantar
            const mesesDisponibles = data.meses_maximos_adelantar || 0;
            
            if (mesesDisponibles === 0) {
                // Estamos en el último mes del año y no hay meses futuros
                if (!document.getElementById('mensajeSinDeudas')) {
                    const mensajeDiv = document.createElement('div');
                    mensajeDiv.id = 'mensajeSinDeudas';
                    mensajeDiv.className = 'sm:col-span-3 lg:col-span-3 mt-4 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4';
                    mensajeDiv.innerHTML = `
                        <div class="flex items-center gap-3">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-sm text-gray-600 dark:text-gray-400">No hay deudas pendientes ni meses disponibles para adelantar. El estudiante está al día.</p>
                        </div>
                    `;
                    const contenedorPrincipal = document.querySelector('.grid.grid-cols-1');
                    if (contenedorPrincipal) {
                        contenedorPrincipal.appendChild(mensajeDiv);
                    }
                }
            }
        }
        
        // Mostrar contenedores de fecha y resumen si hay deudas
        if (data.deudas && data.deudas.length > 0) {
            if (contenedorFecha) contenedorFecha.classList.remove('hidden');
            if (contenedorResumen) contenedorResumen.classList.remove('hidden');
            actualizarResumen();
        }
    }

    // Exponer funciones globalmente para orden-pago-busqueda.js
    window.limpiarFormulario = limpiarFormulario;
    
    // Función para buscar alumno por código (llamada desde orden-pago-busqueda.js)
    window.buscarAlumnoPorCodigo = function(codigo) {

        
        // Obtener referencias directas del DOM
        const inputCodigo = document.getElementById('codigo_alumno');
        const inputError = document.getElementById('error_codigo_alumno');
        const contenedorDeudaEl = document.getElementById('contenedorDeuda');
        const contenedorMesesEl = document.getElementById('contenedorMeses');
        const contenedorFechaEl = document.getElementById('contenedorFecha');
        const contenedorResumenEl = document.getElementById('contenedorResumen');
        const btnGenerar = document.getElementById('btnGenerarOrden');
        const formOrden = document.getElementById('formOrdenPago');
        
        // Asignar el código al input
        if (inputCodigo) {
            inputCodigo.value = codigo;
        } else {
            return;
        }
        
        // Ejecutar la búsqueda directamente (simular el click del botón)
        if (!codigo) {
            return;
        }
        

        
        // Limpiar mensajes anteriores
        if (inputError) {
            inputError.textContent = '';
            inputError.classList.add('hidden');
        }
        if (inputCodigo) {
            inputCodigo.classList.remove('border-red-500');
        }
        
        const mensajes = ['mensajeDeudaAnterior', 'mensajeDeudaBloqueante', 'mensajeOrdenReciente', 'mensajeSinDeudas', 'mensajeAlDia'];
        mensajes.forEach(id => {
            const elemento = document.getElementById(id);
            if (elemento) elemento.remove();
        });

        const baseUrl = window.location.origin;
        const url = `${baseUrl}/orden-pago/buscarAlumno/${codigo}`;

        
        fetch(url)
            .then(response => {

                return response.json().then(data => {
                    if (!response.ok) {
                        if (data.sin_deudas) {
                            throw new Error(data.message || 'El alumno está al día con sus pagos');
                        }
                        throw new Error(data.error || 'Alumno no encontrado');
                    }
                    return data;
                });
            })
            .then(data => {

                
                if (data.al_dia) {

                    if (contenedorDeudaEl) contenedorDeudaEl.classList.add('hidden');
                    if (contenedorMesesEl) contenedorMesesEl.classList.add('hidden');
                    if (contenedorFechaEl) contenedorFechaEl.classList.add('hidden');
                    if (contenedorResumenEl) contenedorResumenEl.classList.add('hidden');
                    
                    if (btnGenerar) {
                        btnGenerar.disabled = true;
                        btnGenerar.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                    
                    if (!document.getElementById('mensajeAlDia')) {
                        const mensajeDiv = document.createElement('div');
                        mensajeDiv.id = 'mensajeAlDia';
                        mensajeDiv.className = 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg mb-4 col-span-1 sm:col-span-2 lg:col-span-3';
                        mensajeDiv.innerHTML = `
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-medium">${data.message || '¡Felicidades! El estudiante está completamente al día con sus pagos.'}</span>
                            </div>
                        `;
                        if (formOrden) {
                            formOrden.insertBefore(mensajeDiv, formOrden.firstChild);
                        }
                    }
                    return;
                }
                
                if (!data.success) {
                    throw new Error(data.error || 'Error al buscar alumno');
                }
                
                // Procesar datos del alumno directamente (reutilizar lógica existente)

                procesarDatosAlumno(data);
            })
            .catch(error => {
                if (inputError) {
                    inputError.textContent = error.message;
                    inputError.classList.remove('hidden');
                }
                if (inputCodigo) {
                    inputCodigo.classList.add('border-red-500');
                }
            });
    };

    btnBuscarAlumno.addEventListener('click', function() {
        const codigo = codigoAlumnoInput.value.trim();
        

        
        errorCodigo.textContent = '';
        errorCodigo.classList.add('hidden');
        codigoAlumnoInput.classList.remove('border-red-500');
        
        const mensajeAnterior = document.getElementById('mensajeDeudaAnterior');
        if (mensajeAnterior) mensajeAnterior.remove();
        
        const mensajeBloqueante = document.getElementById('mensajeDeudaBloqueante');
        if (mensajeBloqueante) mensajeBloqueante.remove();
        
        const mensajeOrdenReciente = document.getElementById('mensajeOrdenReciente');
        if (mensajeOrdenReciente) mensajeOrdenReciente.remove();
        
        const mensajeSinDeudas = document.getElementById('mensajeSinDeudas');
        if (mensajeSinDeudas) mensajeSinDeudas.remove();
        
        const mensajeAlDia = document.getElementById('mensajeAlDia');
        if (mensajeAlDia) mensajeAlDia.remove();
        
        if (!codigo) {
            errorCodigo.textContent = 'Por favor ingrese el código del alumno';
            errorCodigo.classList.remove('hidden');
            codigoAlumnoInput.classList.add('border-red-500');
            return;
        }

        const baseUrl = window.location.origin;
        const url = `${baseUrl}/orden-pago/buscarAlumno/${codigo}`;

        
        fetch(url)
            .then(response => {

                return response.json().then(data => {
                    if (!response.ok) {
                        // Manejo especial para alumno sin deudas
                        if (data.sin_deudas) {
                            throw new Error(data.message || 'El alumno está al día con sus pagos');
                        }
                        throw new Error(data.error || 'Alumno no encontrado');
                    }
                    return data;
                });
            })
            .then(data => {

                // Reutilizar la función procesarDatosAlumno
                procesarDatosAlumno(data);
            })
            .catch(error => {

                errorCodigo.textContent = error.message || 'Error al buscar el alumno';
                errorCodigo.classList.remove('hidden');
                codigoAlumnoInput.classList.add('border-red-500');
                // NO llamar a limpiarFormulario() para mantener el código del alumno visible
            });
    });

    document.getElementById('meses_adelantar').addEventListener('input', function(e) {
        const inputMeses = this;
        let valorTexto = inputMeses.value;
        
        // Eliminar cualquier caracter que no sea dígito
        valorTexto = valorTexto.replace(/[^0-9]/g, '');
        
        // Si está vacío, poner 0
        if (valorTexto === '') {
            inputMeses.value = '0';
            actualizarResumen();
            return;
        }
        
        let valor = parseInt(valorTexto);
        const mesesMaximos = parseInt(inputMeses.dataset.mesesMaximos) || 10;
        
        // Restringir entre 0 y el máximo calculado dinámicamente
        if (valor < 0) {
            valor = 0;
        }
        
        // Solo validar con mesesMaximos dinámico del backend
        if (valor > mesesMaximos) {
            valor = mesesMaximos;
            const errorMeses = document.getElementById('error_meses');
            if (!errorMeses) {
                const errorDiv = document.createElement('div');
                errorDiv.id = 'error_meses';
                errorDiv.className = 'text-sm text-amber-600 dark:text-amber-400 mt-1';
                errorDiv.textContent = `Solo puede adelantar hasta ${mesesMaximos} mes${mesesMaximos > 1 ? 'es' : ''} (hasta diciembre del año actual)`;
                inputMeses.parentElement.parentElement.appendChild(errorDiv);
                
                setTimeout(() => {
                    errorDiv.remove();
                }, 3000);
            }
        }
        
        inputMeses.value = valor.toString();
        actualizarResumen();
    });

    // Botones de incrementar/decrementar meses adelantar
    const btnIncrementar = document.getElementById('btnIncrementar');
    const btnDecrementar = document.getElementById('btnDecrementar');
    const inputMesesAdelantarContador = document.getElementById('meses_adelantar');

    if (btnIncrementar) {
        btnIncrementar.addEventListener('click', function(e) {
            e.preventDefault();
            const valorActual = parseInt(inputMesesAdelantarContador.value) || 0;
            const mesesMaximos = parseInt(inputMesesAdelantarContador.dataset.mesesMaximos) || 10;
            
            if (valorActual < mesesMaximos) {
                inputMesesAdelantarContador.value = valorActual + 1;
                inputMesesAdelantarContador.dispatchEvent(new Event('input'));
            } else {
                // Mostrar mensaje cuando se alcanza el límite
                const errorMeses = document.getElementById('error_meses');
                if (!errorMeses) {
                    const errorDiv = document.createElement('div');
                    errorDiv.id = 'error_meses';
                    errorDiv.className = 'text-sm text-amber-600 dark:text-amber-400 mt-1';
                    errorDiv.textContent = `Solo puede adelantar hasta ${mesesMaximos} mes${mesesMaximos > 1 ? 'es' : ''} (hasta diciembre del año actual)`;
                    inputMesesAdelantarContador.parentElement.parentElement.appendChild(errorDiv);
                    
                    setTimeout(() => {
                        errorDiv.remove();
                    }, 3000);
                }
            }
        });
    }

    if (btnDecrementar) {
        btnDecrementar.addEventListener('click', function(e) {
            e.preventDefault();
            const valorActual = parseInt(inputMesesAdelantarContador.value) || 0;
            
            if (valorActual > 0) {
                inputMesesAdelantarContador.value = valorActual - 1;
                inputMesesAdelantarContador.dispatchEvent(new Event('input'));
            }
        });
    }

    function actualizarResumen() {
        // Incluir TODOS los checkboxes checked, incluyendo los disabled (mes actual)
        const checkboxesSeleccionados = document.querySelectorAll('.deuda-checkbox:checked');
        const mesesAdelantar = parseInt(document.getElementById('meses_adelantar').value) || 0;
        const porcentajeDescuento = parseFloat(formOrdenPago.dataset.porcentajeDescuento) || 10;
        
        const montoMensualEscala = montoMensualActual;
        
        let montoBaseDeudas = 0;
        let moraTotal = 0;
        let cantidadDeudas = checkboxesSeleccionados.length;
        let tieneDeudas = cantidadDeudas > 0;
        let tieneDeudasAtrasadas = alumnoTieneDeudasAnteriores; // Usar el estado del backend por defecto

        if (tieneDeudas) {
            checkboxesSeleccionados.forEach(checkbox => {
                const montoPendiente = parseFloat(checkbox.dataset.montoPendiente) || 0;
                const porcentajeMora = parseFloat(checkbox.dataset.porcentajeMora) || 0;
                const aplicaMora = checkbox.dataset.aplicaMora === '1';
                const esDeudaAnterior = checkbox.dataset.esDeudaAnterior === '1';
                
                montoBaseDeudas += montoPendiente;
                
                if (aplicaMora && porcentajeMora > 0) {
                    moraTotal += montoPendiente * (porcentajeMora / 100);
                }
                
                // Detectar si tiene deudas atrasadas (de meses anteriores)
                if (esDeudaAnterior) {
                    tieneDeudasAtrasadas = true;
                }
            });
        } else {
            // Solo usar el mes actual si NO tiene deudas anteriores pendientes
            if (!alumnoTieneDeudasAnteriores) {
                // Verificar si hay una deuda del mes actual pendiente
                // Si alumnoData tiene deudas y la primera es del mes actual
                const mesActual = new Date().getMonth() + 1; // 1-12
                let tieneDeudaMesActual = false;
                
                if (alumnoData && alumnoData.deudas && alumnoData.deudas.length > 0) {
                    const primeraDeuda = alumnoData.deudas[0];
                    if (primeraDeuda.mes === mesActual) {
                        tieneDeudaMesActual = true;
                    }
                }
                
                if (tieneDeudaMesActual) {
                    // Hay deuda del mes actual pendiente
                    montoBaseDeudas = montoMensualEscala;
                    cantidadDeudas = 1;
                    
                    // Calcular mora del mes actual si aplica
                    if (mesActualAplicaMora && mesActualPorcentajeMora > 0) {
                        moraTotal = montoMensualEscala * (mesActualPorcentajeMora / 100);
                    }
                } else {
                    // Ya pagó el mes actual, no hay mes base
                    montoBaseDeudas = 0;
                    cantidadDeudas = 0;
                }
            } else {
                // Si tiene deudas anteriores y no ha seleccionado ninguna, mostrar 0
                montoBaseDeudas = 0;
                cantidadDeudas = 0;
            }
        }
        
        let montoAdelantado = 0;
        if (mesesAdelantar > 0) {
            montoAdelantado = montoMensualEscala * mesesAdelantar;
        }
        
        const montoBase = montoBaseDeudas + montoAdelantado;
        
        const descuento = mesesAdelantar > 0 ? montoAdelantado * (porcentajeDescuento / 100) : 0;
        
        const total = montoBase - descuento + moraTotal;

        const totalMeses = cantidadDeudas + mesesAdelantar;
        if (mesesAdelantar === 0) {
            if (tieneDeudas) {
                document.getElementById('resumen_meses').textContent = `${cantidadDeudas} mes${cantidadDeudas > 1 ? 'es' : ''}`;
            } else if (alumnoTieneDeudasAnteriores && cantidadDeudas === 0) {
                // Si tiene deudas anteriores pero no ha seleccionado nada
                document.getElementById('resumen_meses').textContent = '0 meses';
            } else if (cantidadDeudas === 1) {
                document.getElementById('resumen_meses').textContent = '1 mes (actual)';
            } else {
                // No tiene mes actual pendiente
                document.getElementById('resumen_meses').textContent = '0 meses';
            }
        } else {
            if (tieneDeudas) {
                document.getElementById('resumen_meses').textContent = `${totalMeses} mes${totalMeses > 1 ? 'es' : ''} (${cantidadDeudas} actual${cantidadDeudas > 1 ? 'es' : ''} + ${mesesAdelantar} adelantado${mesesAdelantar > 1 ? 's' : ''})`;
            } else if (cantidadDeudas === 1) {
                // Tiene mes actual pendiente
                document.getElementById('resumen_meses').textContent = `${totalMeses} mes${totalMeses > 1 ? 'es' : ''} (1 actual + ${mesesAdelantar} adelantado${mesesAdelantar > 1 ? 's' : ''})`;
            } else {
                // Ya pagó el mes actual, solo adelantos
                document.getElementById('resumen_meses').textContent = `${mesesAdelantar} mes${mesesAdelantar > 1 ? 'es' : ''} (${mesesAdelantar} adelantado${mesesAdelantar > 1 ? 's' : ''})`;
            }
        }

        document.getElementById('resumen_base').textContent = `S/ ${montoBase.toFixed(2)}`;
        document.getElementById('resumen_descuento').textContent = `- S/ ${descuento.toFixed(2)}`;
        document.getElementById('resumen_mora').textContent = `+ S/ ${moraTotal.toFixed(2)}`;
        document.getElementById('resumen_total').textContent = `S/ ${total.toFixed(2)}`;
        
        // Mostrar u ocultar la descripción de la política
        const contenedorDetalle = document.getElementById('contenedor_detalle_politica');
        const textoDetalle = document.getElementById('resumen_detalle_politica');
        
        if (moraTotal > 0 && mesActualDescripcionPolitica) {
            const porcentajeFormateado = mesActualPorcentajeMora % 1 === 0 
                ? Math.floor(mesActualPorcentajeMora) 
                : mesActualPorcentajeMora;
            textoDetalle.textContent = `Mora ${porcentajeFormateado}% - ${mesActualDescripcionPolitica}`;
            contenedorDetalle.classList.remove('hidden');
        } else {
            contenedorDetalle.classList.add('hidden');
        }
        
        if (montoMensualEscala > 0) {
            if (contenedorResumen) contenedorResumen.classList.remove('hidden');
            if (contenedorFecha) contenedorFecha.classList.remove('hidden');
        }
        
        actualizarFechaVencimiento(mesesAdelantar, tieneDeudasAtrasadas);

        // Habilitar botón solo si el total de meses es mayor a 0
        const totalMesesParaBoton = cantidadDeudas + mesesAdelantar;
        let tieneAlgoSeleccionado;
        
        if (alumnoTieneDeudasAnteriores) {
            // Con deudas atrasadas: debe marcar checkboxes o adelantar meses
            tieneAlgoSeleccionado = tieneDeudas || mesesAdelantar > 0;
        } else {
            // Sin deudas atrasadas: debe tener al menos 1 mes (actual o adelantado)
            tieneAlgoSeleccionado = totalMesesParaBoton > 0;
        }
        
        if (tieneAlgoSeleccionado) {
            btnGenerarOrden.disabled = false;
            btnGenerarOrden.removeAttribute('disabled');
            btnGenerarOrden.classList.remove('opacity-50', 'cursor-not-allowed');
            btnGenerarOrden.classList.add('cursor-pointer');
        } else {
            btnGenerarOrden.disabled = true;
            btnGenerarOrden.setAttribute('disabled', 'disabled');
            btnGenerarOrden.classList.add('opacity-50', 'cursor-not-allowed');
            btnGenerarOrden.classList.remove('cursor-pointer');
        }
    }
    
    function actualizarFechaVencimiento(mesesAdelantar, tieneDeudasAtrasadas) {
        const hoy = new Date();
        let diasVencimiento;
        let textoVencimiento;
        
        // Deudas atrasadas tienen más tiempo (7 días)
        // Deudas normales y adelantos tienen menos tiempo (3 días)
        if (tieneDeudasAtrasadas) {
            // Deudas atrasadas: 7 días
            diasVencimiento = 7;
            textoVencimiento = 'Tiene 7 días para realizar el pago (deudas atrasadas)';
        } else if (mesesAdelantar > 0) {
            // Adelantos: 3 días
            diasVencimiento = 3;
            textoVencimiento = 'Tiene 3 días para realizar el pago (adelanto)';
        } else {
            // Deudas normales: 3 días
            diasVencimiento = 3;
            textoVencimiento = 'Tiene 3 días para realizar el pago';
        }
        
        const fechaVencimiento = new Date(hoy);
        fechaVencimiento.setDate(hoy.getDate() + diasVencimiento);
        
        const diaDisplay = String(fechaVencimiento.getDate()).padStart(2, '0');
        const mesDisplay = String(fechaVencimiento.getMonth() + 1).padStart(2, '0');
        const anioDisplay = fechaVencimiento.getFullYear();
        const fechaDisplay = `${diaDisplay}/${mesDisplay}/${anioDisplay}`;
        
        const diaBackend = String(fechaVencimiento.getDate()).padStart(2, '0');
        const mesBackend = String(fechaVencimiento.getMonth() + 1).padStart(2, '0');
        const anioBackend = fechaVencimiento.getFullYear();
        const fechaBackend = `${anioBackend}-${mesBackend}-${diaBackend}`;
        
        document.getElementById('fecha_vencimiento_display').value = fechaDisplay;
        document.getElementById('fecha_vencimiento').value = fechaBackend;
        
        const textoExplicativo = document.getElementById('textoVencimiento');
        if (textoExplicativo) {
            textoExplicativo.textContent = textoVencimiento;
        }
    }

    formOrdenPago.addEventListener('submit', function(e) {
        e.preventDefault();

        const errorDeudas = document.getElementById('error_deudas');
        const listaDeudas = document.getElementById('listaDeudasCheckbox');
        errorDeudas.classList.add('hidden');
        listaDeudas.classList.remove('border-red-500');
        
        if (!codigoAlumnoInput.value.trim()) {
            errorCodigo.textContent = 'Por favor ingrese el código del alumno';
            errorCodigo.classList.remove('hidden');
            codigoAlumnoInput.classList.add('border-red-500');
            codigoAlumnoInput.focus();
            return false;
        }

        if (!document.getElementById('id_alumno').value) {
            errorCodigo.textContent = 'Por favor busque el alumno primero';
            errorCodigo.classList.remove('hidden');
            codigoAlumnoInput.classList.add('border-red-500');
            return false;
        }

        // Habilitar temporalmente los checkboxes disabled (mes actual) para que se envíen
        const checkboxesDisabled = document.querySelectorAll('.deuda-checkbox:disabled:checked');
        checkboxesDisabled.forEach(checkbox => {
            checkbox.disabled = false;
        });
        
        const checkboxesSeleccionados = document.querySelectorAll('.deuda-checkbox:checked');
        const mesesAdelantar = parseInt(document.getElementById('meses_adelantar').value) || 0;
        const tieneDeudaAtrasada = !contenedorDeuda.classList.contains('hidden');

        if (tieneDeudaAtrasada && checkboxesSeleccionados.length === 0) {
            // Volver a deshabilitar los checkboxes del mes actual
            checkboxesDisabled.forEach(checkbox => {
                checkbox.disabled = true;
            });
            
            document.getElementById('error_deudas_texto').textContent = 'Debe seleccionar al menos una deuda para generar la orden de pago';
            errorDeudas.classList.remove('hidden');
            listaDeudas.classList.add('border-red-500');
            listaDeudas.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }

        const formData = new FormData(formOrdenPago);

        document.getElementById('error_servidor').classList.add('hidden');
        
        btnGenerarOrden.disabled = true;
        btnGenerarOrden.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Generando...';

        fetch(formOrdenPago.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.open(data.pdf_url, '_blank');
                
                window.location.href = data.redirect_url;
            } else {
                document.getElementById('error_servidor_texto').textContent = data.message || 'No se pudo generar la orden de pago';
                document.getElementById('error_servidor').classList.remove('hidden');
                document.getElementById('error_servidor').scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                btnGenerarOrden.disabled = false;
                btnGenerarOrden.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg> Generar Orden de Pago';
            }
        })
        .catch(error => {
            document.getElementById('error_servidor_texto').textContent = 'Error de conexión: ' + error.message;
            document.getElementById('error_servidor').classList.remove('hidden');
            document.getElementById('error_servidor').scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            btnGenerarOrden.disabled = false;
            btnGenerarOrden.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg> Generar Orden de Pago';
        });
    });

    const btnSiAdelantar = document.getElementById('btnSiAdelantar');
    const btnCancelarAdelantar = document.getElementById('btnCancelarAdelantar');
    const preguntaAdelantar = document.getElementById('preguntaAdelantar');
    const inputMesesAdelantar = document.getElementById('inputMesesAdelantar');
    const mesesAdelantarInput = document.getElementById('meses_adelantar');

    btnSiAdelantar.addEventListener('click', function() {
        preguntaAdelantar.classList.add('hidden');
        inputMesesAdelantar.classList.remove('hidden');
        mesesAdelantarInput.value = '1';
        mesesAdelantarInput.min = '1';
        actualizarResumen();
        if (contenedorFecha) contenedorFecha.classList.remove('hidden');
        if (contenedorResumen) contenedorResumen.classList.remove('hidden');
    });

    btnCancelarAdelantar.addEventListener('click', function() {
        inputMesesAdelantar.classList.add('hidden');
        preguntaAdelantar.classList.remove('hidden');
        mesesAdelantarInput.value = '0';
        mesesAdelantarInput.min = '0';
        actualizarResumen();
        if (contenedorFecha) contenedorFecha.classList.remove('hidden');
        if (contenedorResumen) contenedorResumen.classList.remove('hidden');
    });
});
