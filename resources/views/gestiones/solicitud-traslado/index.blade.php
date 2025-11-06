@extends('base.administrativo.blank')

@section('titulo')
Generar Solicitud de Traslado
@endsection

@section('contenido')
<!-- Contenedor Principal -->
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <!-- Header -->
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
            Generar Solicitud de Traslado
        </h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Busque al alumno por código de educando para verificar su situación y generar la solicitud
        </p>
    </div>

    <!-- Card de Búsqueda -->
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-6 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 mb-6">

        <div class="mb-4">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">
                Buscar Alumno
            </h3>
        </div>

        <div class="space-y-4">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Código de Educando
                    <span class="ml-1 text-xs font-normal text-gray-500 dark:text-gray-400">(6 dígitos)</span>
                </label>
                <input
                    type="text"
                    id="codigo_educando"
                    placeholder="Ej: 166787"
                    maxlength="6"
                    class="h-[42px] w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                />
                <div class="mt-2 flex items-start gap-2 rounded-lg bg-blue-50 p-3 dark:bg-blue-900/10">
                    <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-xs text-blue-700 dark:text-blue-300">
                        <strong>Tip:</strong> Ingrese el código de educando del alumno (ejemplo: 166787)
                    </p>
                </div>
            </div>

            <button
                type="button"
                id="btnBuscar"
                class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Buscar Alumno
            </button>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" style="display: none;" class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-6 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6">
        <div class="flex flex-col items-center justify-center py-12">
            <div class="relative">
                <div class="h-16 w-16 animate-spin rounded-full border-4 border-solid border-gray-300 border-t-gray-600 dark:border-gray-700 dark:border-t-gray-400"></div>
            </div>
            <p class="mt-6 text-base font-medium text-gray-800 dark:text-white/90">Buscando alumno...</p>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Por favor espere un momento</p>
        </div>
    </div>

    <!-- Área de Resultados -->
    <div id="resultadosBusqueda" style="display: none;">

        <!-- Información del Alumno -->
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-6 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 mb-6">
            <div class="mb-4">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">
                    Información del Alumno
                </h3>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                    <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Código de Educando</p>
                    <p class="text-base font-semibold text-gray-800 dark:text-white/90" id="alumno-codigo"></p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                    <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Nombre Completo</p>
                    <p class="text-base font-semibold text-gray-800 dark:text-white/90" id="alumno-nombre"></p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                    <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">DNI</p>
                    <p class="text-base font-semibold text-gray-800 dark:text-white/90" id="alumno-dni"></p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                    <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Información Académica</p>
                    <p class="text-base font-semibold text-gray-800 dark:text-white/90" id="alumno-grado"></p>
                </div>
            </div>
        </div>

        <!-- Sección de Deudas (Si tiene) -->
        <div id="seccionDeudas" style="display: none;">
            <div class="overflow-hidden rounded-2xl border border-red-200 bg-white px-4 pb-6 pt-4 dark:border-red-800 dark:bg-white/[0.03] sm:px-6 mb-6">
                <div class="mb-4">
                    <h3 class="text-base font-semibold text-red-600 dark:text-red-400">
                        Deudas Pendientes Detectadas
                    </h3>
                </div>
                <div class="space-y-4">
                    <!-- Alerta -->
                    <div class="rounded-lg border-l-4 border-red-600 bg-red-50 p-4 dark:bg-red-900/10">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-semibold text-red-800 dark:text-red-300">
                                    No se puede procesar la solicitud de traslado
                                </h4>
                                <p class="mt-1 text-sm text-red-700 dark:text-red-200">
                                    El alumno tiene deudas pendientes. Debe regularizar su situación financiera antes de solicitar el traslado.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Deudas -->
                    <div class="rounded-lg border border-gray-200 overflow-hidden dark:border-gray-800">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-gray-100 border-y bg-red-600 text-white dark:border-gray-800">
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase">Concepto</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase">Período</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase">Fecha Límite</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium uppercase">Monto Total</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium uppercase">Pendiente</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaDeudas" class="divide-y divide-gray-100 dark:divide-gray-800">
                                </tbody>
                                <tfoot>
                                    <tr class="bg-red-600 text-white">
                                        <td colspan="4" class="px-4 py-3 text-right text-sm font-semibold">TOTAL PENDIENTE:</td>
                                        <td class="px-4 py-3 text-right text-base font-bold">S/ <span id="totalPendiente"></span></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección Sin Deudas - Formulario -->
        <div id="seccionSinDeudas" style="display: none;">
            <!-- Alerta de Éxito -->
            <div class="mb-6 rounded-lg border-l-4 border-green-600 bg-green-50 p-4 dark:bg-green-900/10">
                <div class="flex items-center">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-green-600">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-sm font-semibold text-green-800 dark:text-green-300">
                            Sin Deudas Pendientes
                        </h4>
                        <p class="mt-1 text-sm text-green-700 dark:text-green-200">
                            El alumno no tiene deudas pendientes. Puede proceder con la solicitud de traslado.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Formulario de Solicitud -->
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-6 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6">
                <div class="mb-4">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">
                        Datos de la Solicitud de Traslado
                    </h3>
                </div>
                <div class="space-y-5">
                    <form id="formSolicitudTraslado" class="space-y-5">
                        <input type="hidden" id="id_alumno" name="id_alumno">

                        <!-- Fila 1 -->
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300" for="colegio_destino">
                                    Colegio de Destino <span class="text-red-500">*</span>
                                </label>
                                <input
                                    class="h-[42px] w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                    type="text"
                                    name="colegio_destino"
                                    id="colegio_destino"
                                    placeholder="Ej: I.E. San Juan Bosco"
                                    required
                                />
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300" for="fecha_traslado">
                                    Fecha de Traslado <span class="text-red-500">*</span>
                                </label>
                                <input
                                    class="h-[42px] w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                    type="date"
                                    name="fecha_traslado"
                                    id="fecha_traslado"
                                    required
                                />
                            </div>
                        </div>

                        <!-- Fila 2 -->
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300" for="direccion_nuevo_colegio">
                                    Dirección del Nuevo Colegio
                                </label>
                                <input
                                    class="h-[42px] w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                    type="text"
                                    name="direccion_nuevo_colegio"
                                    id="direccion_nuevo_colegio"
                                    placeholder="Av. Los Pinos 123, Lima"
                                />
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300" for="telefono_nuevo_colegio">
                                    Teléfono del Nuevo Colegio
                                </label>
                                <input
                                    class="h-[42px] w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                    type="text"
                                    name="telefono_nuevo_colegio"
                                    id="telefono_nuevo_colegio"
                                    placeholder="(01) 234-5678"
                                />
                            </div>
                        </div>

                        <!-- Motivo -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300" for="motivo_traslado">
                                Motivo del Traslado <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                class="w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                name="motivo_traslado"
                                id="motivo_traslado"
                                rows="4"
                                placeholder="Explique brevemente el motivo del traslado..."
                                required
                            ></textarea>
                        </div>

                        <!-- Observaciones -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300" for="observaciones">
                                Observaciones Adicionales
                            </label>
                            <textarea
                                class="w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                name="observaciones"
                                id="observaciones"
                                rows="3"
                                placeholder="Observaciones adicionales (opcional)"
                            ></textarea>
                        </div>

                        <!-- Botones -->
                        <div class="flex gap-3 pt-4">
                            <button
                                type="submit"
                                class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg border border-gray-300 bg-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Enviar Solicitud de Traslado
                            </button>
                            <button
                                type="button"
                                onclick="location.reload()"
                                class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Nueva Búsqueda
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('custom-js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Buscar alumno al hacer clic
    document.getElementById('btnBuscar').addEventListener('click', buscarAlumno);

    // Buscar alumno al presionar Enter
    document.getElementById('codigo_educando').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            buscarAlumno();
        }
    });

    function buscarAlumno() {
        const codigo = document.getElementById('codigo_educando').value.trim();

        if (!codigo) {
            Swal.fire({
                icon: 'warning',
                title: 'Campo vacío',
                text: 'Por favor ingrese un código de educando',
                confirmButtonColor: '#3C50E0'
            });
            return;
        }

        if (codigo.length !== 6) {
            Swal.fire({
                icon: 'warning',
                title: 'Código inválido',
                text: 'El código de educando debe tener 6 dígitos',
                confirmButtonColor: '#3C50E0'
            });
            return;
        }

        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('resultadosBusqueda').style.display = 'none';

        fetch('{{ route("traslado_buscar") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                codigo_educando: codigo
            })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('loadingSpinner').style.display = 'none';

            if (data.success) {
                mostrarResultados(data);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Error al buscar el alumno',
                    confirmButtonColor: '#3C50E0'
                });
            }
        })
        .catch(error => {
            document.getElementById('loadingSpinner').style.display = 'none';
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Alumno no encontrado',
                text: 'No se encontró ningún alumno con ese código de educando',
                confirmButtonColor: '#3C50E0'
            });
        });
    }

    function mostrarResultados(data) {
        // Mostrar información del alumno
        document.getElementById('alumno-codigo').textContent = data.alumno.codigo_educando;
        document.getElementById('alumno-nombre').textContent = data.alumno.nombre_completo;
        document.getElementById('alumno-dni').textContent = data.alumno.dni || 'No registrado';
        document.getElementById('alumno-grado').textContent = data.alumno.grado;
        document.getElementById('id_alumno').value = data.alumno.id_alumno;

        document.getElementById('resultadosBusqueda').style.display = 'block';

        // Scroll suave hacia resultados
        document.getElementById('resultadosBusqueda').scrollIntoView({ behavior: 'smooth', block: 'start' });

        if (data.tiene_deudas) {
            // Mostrar deudas
            document.getElementById('seccionDeudas').style.display = 'block';
            document.getElementById('seccionSinDeudas').style.display = 'none';

            let htmlDeudas = '';
            data.deudas.forEach(function(deuda, index) {
                const bgClass = index % 2 === 0 ? 'bg-white dark:bg-boxdark' : 'bg-red-50/50 dark:bg-red-900/10';
                htmlDeudas += `
                    <tr class="${bgClass}">
                        <td class="px-5 py-4">
                            <p class="font-medium text-black dark:text-white">${deuda.concepto}</p>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-black dark:text-white">${deuda.periodo}</p>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-black dark:text-white">${deuda.fecha_limite}</p>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <p class="font-medium text-black dark:text-white">S/ ${deuda.monto_total}</p>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <p class="text-xl font-bold text-red-600 dark:text-red-400">S/ ${deuda.monto_pendiente}</p>
                        </td>
                    </tr>
                `;
            });

            document.getElementById('tablaDeudas').innerHTML = htmlDeudas;
            document.getElementById('totalPendiente').textContent = data.monto_total_pendiente;
        } else {
            // Sin deudas - mostrar formulario
            document.getElementById('seccionDeudas').style.display = 'none';
            document.getElementById('seccionSinDeudas').style.display = 'block';
        }
    }

    // Enviar solicitud de traslado
    document.getElementById('formSolicitudTraslado').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = {
            id_alumno: document.getElementById('id_alumno').value,
            colegio_destino: document.getElementById('colegio_destino').value,
            fecha_traslado: document.getElementById('fecha_traslado').value,
            direccion_nuevo_colegio: document.getElementById('direccion_nuevo_colegio').value,
            telefono_nuevo_colegio: document.getElementById('telefono_nuevo_colegio').value,
            motivo_traslado: document.getElementById('motivo_traslado').value,
            observaciones: document.getElementById('observaciones').value
        };

        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('resultadosBusqueda').style.display = 'none';
        window.scrollTo({ top: 0, behavior: 'smooth' });

        fetch('{{ route("traslado_guardar") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('loadingSpinner').style.display = 'none';

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Solicitud Generada!',
                    text: 'La solicitud de traslado ha sido generada exitosamente',
                    showCancelButton: true,
                    confirmButtonText: '📄 Descargar PDF',
                    cancelButtonText: 'Cerrar',
                    confirmButtonColor: '#3C50E0',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ url("traslados/pdf") }}/' + data.codigo_solicitud;
                    } else {
                        location.reload();
                    }
                });
            } else {
                document.getElementById('resultadosBusqueda').style.display = 'block';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Error al generar la solicitud',
                    confirmButtonColor: '#3C50E0'
                });
            }
        })
        .catch(error => {
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('resultadosBusqueda').style.display = 'block';
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al generar la solicitud',
                confirmButtonColor: '#3C50E0'
            });
        });
    });
});
</script>
@endsection
