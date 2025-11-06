@extends('base.administrativo.blank')

@section('titulo')
Generar Solicitud de Traslado
@endsection

@section('contenido')
<!-- Contenedor Principal con Gradiente -->
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <!-- Header Mejorado con Icono y Gradiente -->
    <div class="mb-8 rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 p-6 ">
        <div class="flex items-center">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
            </div>
            <div class="ml-5">
                <h2 class="text-3xl font-bold text-white">
                    Generar Solicitud de Traslado
                </h2>
                <p class="mt-1 text-sm text-white/80">
                    Busque al alumno por código de educando para verificar su situación y generar la solicitud
                </p>
            </div>
        </div>
    </div>

    <!-- Card de Búsqueda Mejorado bg-gradient-to-r from-gray-50 to-gray-100-->
    <div class="rounded-xl border border-stroke bg-white  dark:border-strokedark dark:bg-boxdark mb-8 overflow-hidden">
        <!-- Header del Card -->
        <div class="border-b border-stroke bg-blue-400 px-7 py-5 dark:border-strokedark dark:from-meta-4 dark:to-meta-4">
            <div class="flex items-center">
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-primary">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <h3 class="ml-4 text-xl font-semibold text-black dark:text-white">
                    Buscar Alumno
                </h3>
            </div>
        </div>

        <!-- Contenido del Card -->
        <div class="p-7">
            <div class="mb-5">
                <label class="mb-3 block text-sm font-semibold text-black dark:text-white">
                    Código de Educando
                    <span class="ml-1 text-xs font-normal text-gray-500">(6 dígitos)</span>
                </label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </span>
                    <input
                        type="text"
                        id="codigo_educando"
                        placeholder="Ej: 166787"
                        maxlength="6"
                        class="w-full rounded-lg border-2 border-stroke bg-transparent py-4 pl-12 pr-5 text-lg font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary"
                    />
                </div>
                <div class="mt-3 flex items-start rounded-lg bg-blue-50 p-3 dark:bg-blue-900/20">
                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="ml-3 text-sm text-blue-700 dark:text-blue-300">
                        <strong>Tip:</strong> Ingrese el código de educando del alumno (ejemplo: 166787)
                    </p>
                </div>
            </div>

            <button
                type="button"
                id="btnBuscar"
                class="flex w-full items-center justify-center gap-2 rounded-lg bg-blue-400 py-4 font-semibold text-white transition hover:bg-opacity-90 hover:shadow-xl active:scale-98"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Buscar Alumno
            </button>
        </div>
    </div>

    <!-- Loading Spinner Mejorado -->
    <div id="loadingSpinner" style="display: none;" class="rounded-xl border border-stroke bg-white shadow-lg dark:border-strokedark dark:bg-boxdark p-12">
        <div class="flex flex-col items-center justify-center">
            <div class="relative">
                <div class="h-20 w-20 animate-spin rounded-full border-4 border-solid border-primary border-t-transparent"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="h-8 w-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="mt-6 text-lg font-medium text-black dark:text-white">Buscando alumno...</p>
            <p class="mt-2 text-sm text-gray-500">Por favor espere un momento</p>
        </div>
    </div>

    <!-- Área de Resultados -->
    <div id="resultadosBusqueda" style="display: none;">

        <!-- Información del Alumno Mejorada -->
        <div class="rounded-xl border border-stroke bg-white shadow-lg dark:border-strokedark dark:bg-boxdark mb-6 overflow-hidden">
            <div class="border-b border-stroke bg-gradient-to-r from-green-50 to-emerald-50 px-7 py-5 dark:border-strokedark dark:from-meta-4 dark:to-meta-4">
                <div class="flex items-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-600">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h3 class="ml-4 text-xl font-semibold text-black dark:text-white">
                        Información del Alumno
                    </h3>
                </div>
            </div>
            <div class="p-7">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Código de Educando</p>
                        <p class="text-xl font-bold text-black dark:text-white" id="alumno-codigo"></p>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Nombre Completo</p>
                        <p class="text-xl font-bold text-black dark:text-white" id="alumno-nombre"></p>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">DNI</p>
                        <p class="text-xl font-bold text-black dark:text-white" id="alumno-dni"></p>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Información Académica</p>
                        <p class="text-xl font-bold text-black dark:text-white" id="alumno-grado"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Deudas (Si tiene) -->
        <div id="seccionDeudas" style="display: none;">
            <div class="rounded-xl border-2 border-red-200 bg-white shadow-xl dark:border-red-900 dark:bg-boxdark mb-6 overflow-hidden">
                <div class="border-b-2 border-red-200 bg-gradient-to-r from-red-50 to-red-100 px-7 py-5 dark:border-red-900 dark:from-red-900/20 dark:to-red-900/20">
                    <div class="flex items-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-red-600">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <h3 class="ml-4 text-xl font-semibold text-red-600 dark:text-red-400">
                            ⚠️ Deudas Pendientes Detectadas
                        </h3>
                    </div>
                </div>
                <div class="p-7">
                    <!-- Alerta Prominente -->
                    <div class="mb-6 rounded-xl border-l-4 border-red-600 bg-red-50 p-6 shadow-md dark:bg-red-900/20">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="mb-2 text-lg font-bold text-red-800 dark:text-red-300">
                                    No se puede procesar la solicitud de traslado
                                </h4>
                                <p class="text-base leading-relaxed text-red-700 dark:text-red-200">
                                    El alumno tiene deudas pendientes. Debe regularizar su situación financiera antes de solicitar el traslado.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Deudas Mejorada -->
                    <div class="rounded-lg border-2 border-red-100 overflow-hidden dark:border-red-900/50">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gradient-to-r from-red-600 to-red-700 text-white">
                                        <th class="px-5 py-4 text-left text-sm font-bold uppercase tracking-wider">Concepto</th>
                                        <th class="px-5 py-4 text-left text-sm font-bold uppercase tracking-wider">Período</th>
                                        <th class="px-5 py-4 text-left text-sm font-bold uppercase tracking-wider">Fecha Límite</th>
                                        <th class="px-5 py-4 text-right text-sm font-bold uppercase tracking-wider">Monto Total</th>
                                        <th class="px-5 py-4 text-right text-sm font-bold uppercase tracking-wider">Pendiente</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaDeudas" class="divide-y divide-red-100 dark:divide-red-900/50">
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gradient-to-r from-red-600 to-red-700 text-white">
                                        <td colspan="4" class="px-5 py-4 text-right text-base font-bold">TOTAL PENDIENTE:</td>
                                        <td class="px-5 py-4 text-right text-xl font-bold">S/ <span id="totalPendiente"></span></td>
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
            <!-- Alerta de Éxito Mejorada -->
            <div class="mb-6 rounded-xl border-2 border-green-200 bg-gradient-to-r from-green-50 to-emerald-50 p-6 shadow-lg dark:border-green-900 dark:from-green-900/20 dark:to-emerald-900/20">
                <div class="flex items-center">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-600">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <h4 class="text-lg font-bold text-green-800 dark:text-green-300">
                            ✅ Sin Deudas Pendientes
                        </h4>
                        <p class="mt-1 text-base text-green-700 dark:text-green-200">
                            El alumno no tiene deudas pendientes. Puede proceder con la solicitud de traslado.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Formulario de Solicitud Mejorado -->
            <div class="rounded-xl border border-stroke bg-white shadow-xl dark:border-strokedark dark:bg-boxdark overflow-hidden">
                <div class="border-b border-stroke bg-gradient-to-r from-blue-50 to-indigo-50 px-7 py-5 dark:border-strokedark dark:from-meta-4 dark:to-meta-4">
                    <div class="flex items-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-600">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="ml-4 text-xl font-semibold text-black dark:text-white">
                            Datos de la Solicitud de Traslado
                        </h3>
                    </div>
                </div>
                <div class="p-7">
                    <form id="formSolicitudTraslado" class="space-y-6">
                        <input type="hidden" id="id_alumno" name="id_alumno">

                        <!-- Fila 1 -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label class="mb-3 block text-sm font-bold text-black dark:text-white" for="colegio_destino">
                                    Colegio de Destino <span class="text-red-500">*</span>
                                </label>
                                <input
                                    class="w-full rounded-lg border-2 border-stroke bg-gray-50 py-3.5 px-4.5 text-black transition focus:border-primary focus:bg-white focus-visible:outline-none dark:border-strokedark dark:bg-meta-4 dark:text-white dark:focus:border-primary"
                                    type="text"
                                    name="colegio_destino"
                                    id="colegio_destino"
                                    placeholder="Ej: I.E. San Juan Bosco"
                                    required
                                />
                            </div>

                            <div>
                                <label class="mb-3 block text-sm font-bold text-black dark:text-white" for="fecha_traslado">
                                    Fecha de Traslado <span class="text-red-500">*</span>
                                </label>
                                <input
                                    class="w-full rounded-lg border-2 border-stroke bg-gray-50 py-3.5 px-4.5 text-black transition focus:border-primary focus:bg-white focus-visible:outline-none dark:border-strokedark dark:bg-meta-4 dark:text-white dark:focus:border-primary"
                                    type="date"
                                    name="fecha_traslado"
                                    id="fecha_traslado"
                                    required
                                />
                            </div>
                        </div>

                        <!-- Fila 2 -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label class="mb-3 block text-sm font-bold text-black dark:text-white" for="direccion_nuevo_colegio">
                                    Dirección del Nuevo Colegio
                                </label>
                                <input
                                    class="w-full rounded-lg border-2 border-stroke bg-gray-50 py-3.5 px-4.5 text-black transition focus:border-primary focus:bg-white focus-visible:outline-none dark:border-strokedark dark:bg-meta-4 dark:text-white dark:focus:border-primary"
                                    type="text"
                                    name="direccion_nuevo_colegio"
                                    id="direccion_nuevo_colegio"
                                    placeholder="Av. Los Pinos 123, Lima"
                                />
                            </div>

                            <div>
                                <label class="mb-3 block text-sm font-bold text-black dark:text-white" for="telefono_nuevo_colegio">
                                    Teléfono del Nuevo Colegio
                                </label>
                                <input
                                    class="w-full rounded-lg border-2 border-stroke bg-gray-50 py-3.5 px-4.5 text-black transition focus:border-primary focus:bg-white focus-visible:outline-none dark:border-strokedark dark:bg-meta-4 dark:text-white dark:focus:border-primary"
                                    type="text"
                                    name="telefono_nuevo_colegio"
                                    id="telefono_nuevo_colegio"
                                    placeholder="(01) 234-5678"
                                />
                            </div>
                        </div>

                        <!-- Motivo -->
                        <div>
                            <label class="mb-3 block text-sm font-bold text-black dark:text-white" for="motivo_traslado">
                                Motivo del Traslado <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                class="w-full rounded-lg border-2 border-stroke bg-gray-50 py-3.5 px-4.5 text-black transition focus:border-primary focus:bg-white focus-visible:outline-none dark:border-strokedark dark:bg-meta-4 dark:text-white dark:focus:border-primary"
                                name="motivo_traslado"
                                id="motivo_traslado"
                                rows="4"
                                placeholder="Explique brevemente el motivo del traslado..."
                                required
                            ></textarea>
                        </div>

                        <!-- Observaciones -->
                        <div>
                            <label class="mb-3 block text-sm font-bold text-black dark:text-white" for="observaciones">
                                Observaciones Adicionales
                            </label>
                            <textarea
                                class="w-full rounded-lg border-2 border-stroke bg-gray-50 py-3.5 px-4.5 text-black transition focus:border-primary focus:bg-white focus-visible:outline-none dark:border-strokedark dark:bg-meta-4 dark:text-white dark:focus:border-primary"
                                name="observaciones"
                                id="observaciones"
                                rows="3"
                                placeholder="Observaciones adicionales (opcional)"
                            ></textarea>
                        </div>

                        <!-- Botones Mejorados -->
                        <div class="flex gap-4 pt-4">
                            <button
                                type="submit"
                                class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 py-4 font-bold text-white shadow-lg transition hover:from-blue-700 hover:to-purple-700 hover:shadow-xl active:scale-98"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Enviar Solicitud de Traslado
                            </button>
                            <button
                                type="button"
                                onclick="location.reload()"
                                class="flex flex-1 items-center justify-center gap-2 rounded-lg border-2 border-stroke bg-white py-4 font-bold text-black shadow-md transition hover:bg-gray-50 hover:shadow-lg active:scale-98 dark:border-strokedark dark:bg-boxdark dark:text-white dark:hover:bg-meta-4"
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
