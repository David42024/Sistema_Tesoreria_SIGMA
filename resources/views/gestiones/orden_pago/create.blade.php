@extends('base.administrativo.blank')

@section('title')
    Generar Orden de Pago
@endsection

@push('styles')
<style>
    /* Ocultar flechas nativas del input number en todos los navegadores */
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    
    input[type="number"] {
        -moz-appearance: textfield;
    }
</style>
@endpush

@section('contenido')

<div class="p-8 m-4 dark:bg-white/[0.03] rounded-2xl">
    <div class="flex pb-4 justify-between items-center">
        <h2 class="text-lg dark:text-gray-200 text-gray-800">
            Estás generando una Orden de Pago
        </h2>

        <div class="flex gap-4">
            <input form="formOrdenPago" type="submit" id="btnGenerar"
                class="cursor-pointer inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-gray-200 px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-300 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
                value="Generar">

            <a href="{{ route('orden_pago_view') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-gray-200 px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-300 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                Cancelar
            </a>
        </div>
    </div>

    <form method="POST" id="formOrdenPago" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" 
        action="{{ route('orden_pago_store') }}">
        @csrf

        {{-- Búsqueda de Alumno --}}
        <div class="sm:col-span-3 lg:col-span-3">
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-300 dark:border-gray-600">
                <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200">
                    Búsqueda de Estudiante
                </h3>
            </div>
            
            {{-- Contenedor de Filtros (Oculto por defecto) --}}
            <div id="contenedorFiltros" class="hidden mb-4 p-4 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/10 dark:to-purple-900/10 rounded-lg border border-indigo-200 dark:border-indigo-800">
                {{-- Botones para activar cada filtro --}}
                <div class="flex flex-wrap gap-2 mb-4">
                    <button type="button" id="btnActivarNivel"
                        class="flex items-center gap-2 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-green-50 dark:hover:bg-green-900/20 px-3 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Nivel Educativo
                    </button>
                    <button type="button" id="btnActivarGrado"
                        class="flex items-center gap-2 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 px-3 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        Grado
                    </button>
                    <button type="button" id="btnActivarSeccion"
                        class="flex items-center gap-2 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-purple-50 dark:hover:bg-purple-900/20 px-3 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Sección
                    </button>
                    <button type="button" id="btnBuscarConFiltros"
                        class="ml-auto flex items-center gap-2 rounded-lg border-2 border-blue-500 bg-blue-500 hover:bg-blue-600 px-4 py-2 text-sm font-bold text-white transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Buscar
                    </button>
                </div>

                {{-- Filtros individuales --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    {{-- Filtro Nivel Educativo --}}
                    <div id="contenedorFiltroNivel" class="hidden flex-col">
                        <label for="filtro_nivel" class="text-sm font-semibold text-green-700 dark:text-green-300 mb-2 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Nivel Educativo
                        </label>
                        <select id="filtro_nivel" class="w-full rounded-lg border-2 border-green-300 dark:border-green-700 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90 focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm">
                            <option value="">Todos los niveles</option>
                            <option value="Inicial">Inicial</option>
                            <option value="Primaria">Primaria</option>
                            <option value="Secundaria">Secundaria</option>
                        </select>
                    </div>

                    {{-- Filtro por Grado --}}
                    <div id="contenedorFiltroGrado" class="hidden flex-col">
                        <label for="filtro_grado" class="text-sm font-semibold text-indigo-700 dark:text-indigo-300 mb-2 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            Grado
                        </label>
                        <select id="filtro_grado" class="w-full rounded-lg border-2 border-indigo-300 dark:border-indigo-700 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                            <option value="">Todos los grados</option>
                        </select>
                    </div>

                    {{-- Filtro por Sección --}}
                    <div id="contenedorFiltroSeccion" class="hidden flex-col">
                        <label for="filtro_seccion" class="text-sm font-semibold text-purple-700 dark:text-purple-300 mb-2 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Sección
                        </label>
                        <select id="filtro_seccion" class="w-full rounded-lg border-2 border-purple-300 dark:border-purple-700 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 shadow-sm">
                            <option value="">Todas las secciones</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Campo de búsqueda --}}
            <div class="flex gap-2 items-start">
                <div class="relative flex-1">
                    <div class="flex flex-col">
                        <div class="relative">
                            <input type="text" id="buscar_alumno" autocomplete="off"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 px-4 py-3 pl-10 text-sm text-gray-800 dark:text-white/90 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Escribe el nombre o código del estudiante...">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <div id="loading_busqueda" class="hidden absolute right-3 top-3.5">
                                <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="min-h-[20px]">
                            <p id="error_buscar_alumno" class="text-xs text-red-500 mt-1 hidden"></p>
                        </div>
                    </div>

                    {{-- Dropdown de resultados --}}
                    <div id="resultados_busqueda" class="hidden absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg shadow-lg max-h-96 overflow-y-auto">
                        {{-- Los resultados se insertarán aquí dinámicamente --}}
                    </div>
                </div>
                
                {{-- Botón Filtros --}}
                <button type="button" id="btnActivarFiltros"
                    class="flex items-center gap-2 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:border-indigo-500 dark:hover:border-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 px-4 h-[46px] text-sm font-semibold text-gray-700 dark:text-gray-300 transition-all duration-200 whitespace-nowrap"
                    title="Mostrar/Ocultar Filtros">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <span>Filtros</span>
                </button>

                {{-- Botón Limpiar Búsqueda --}}
                <button type="button" id="btnLimpiarBusqueda"
                    class="flex items-center gap-2 rounded-lg border-2 border-blue-300 bg-blue-100 hover:bg-blue-200 dark:border-blue-700 dark:bg-blue-900 dark:hover:bg-blue-800 px-4 h-[46px] text-sm font-semibold text-gray-800 dark:text-blue-100 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400 whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Limpiar</span>
                </button>
            </div>
        </div>

        {{-- Campos ocultos para mantener compatibilidad --}}
        <input type="hidden" id="codigo_alumno" name="codigo_alumno" value="{{ old('codigo_alumno') }}">
        <div id="error_codigo_alumno" class="hidden text-sm text-red-600 dark:text-red-400 mt-2"></div>

        {{-- Datos del Estudiante (visibles desde el inicio) --}}
        <div class="sm:col-span-3 lg:col-span-3 mt-6">
            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-4 pb-2 border-b border-gray-300 dark:border-gray-600">
                Datos del Estudiante
            </h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <input type="hidden" id="id_alumno" name="id_alumno">
                <input type="hidden" id="id_matricula" name="id_matricula">
                <input type="hidden" id="tiene_deudas_anteriores" name="tiene_deudas_anteriores" value="0">

                <div class="flex flex-col">
                    <label for="nombre_completo" class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                        Nombre Completo
                    </label>
                    <input type="text" id="nombre_completo" readonly
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90"
                        placeholder="Busque un alumno primero">
                    <div class="min-h-[20px]"></div>
                </div>

                <div class="flex flex-col">
                    <label for="dni_alumno" class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                        DNI
                    </label>
                    <input type="text" id="dni_alumno" readonly
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90"
                        placeholder="-">
                    <div class="min-h-[20px]"></div>
                </div>
            </div>
        </div>

        {{-- Datos de la Matrícula (visibles desde el inicio) --}}
        <div class="sm:col-span-3 lg:col-span-3 mt-4">
            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-4 pb-2 border-b border-gray-300 dark:border-gray-600">
                Datos de la Matrícula
            </h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="flex flex-col">
                    <label for="nivel_educativo" class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                        Nivel Educativo
                    </label>
                    <input type="text" id="nivel_educativo" readonly
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90"
                        placeholder="-">
                    <div class="min-h-[20px]"></div>
                </div>

                <div class="flex flex-col">
                    <label for="grado" class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                        Grado
                    </label>
                    <input type="text" id="grado" readonly
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90"
                        placeholder="-">
                    <div class="min-h-[20px]"></div>
                </div>

                <div class="flex flex-col">
                    <label for="seccion" class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                        Sección
                    </label>
                    <input type="text" id="seccion" readonly
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90"
                        placeholder="-">
                    <div class="min-h-[20px]"></div>
                </div>

                <div class="flex flex-col">
                    <label for="escala" class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                        Escala - Monto Mensual
                    </label>
                    <div class="flex gap-1">
                        <input type="text" id="escala" readonly
                            class="w-12 rounded-l-lg border border-r-0 border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 px-2 py-2.5 text-sm text-center text-gray-800 dark:text-white/90"
                            placeholder="-">
                        <input type="text" id="monto_mensual" readonly
                            class="flex-1 rounded-r-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90"
                            placeholder="-">
                    </div>
                    <div class="min-h-[20px]"></div>
                </div>
            </div>
        </div>

        {{-- Selección de Deuda --}}
        <div id="contenedorDeuda" class="sm:col-span-3 lg:col-span-3 hidden mt-6">
            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-4 pb-2 border-b border-gray-300 dark:border-gray-600">
                Selección de Deudas
            </h3>
            
            {{-- Mensaje informativo para deudas múltiples --}}
            <div id="mensajeDeudasMultiples" class="hidden bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-3 mb-4">
                <p class="text-sm text-amber-700 dark:text-amber-400 flex items-start gap-2">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span><strong>Importante:</strong> Puedes seleccionar varias deudas para generar una sola orden de pago. Esta orden te permitirá pagar las deudas por partes. La fecha de vencimiento será calculada automáticamente.</span>
                </p>
            </div>
            
            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                    <span id="tituloDeudas">Deudas Atrasadas (Selecciona una o varias)</span>
                </label>
                <div id="listaDeudasCheckbox" class="space-y-2 max-h-64 overflow-y-auto border border-gray-300 dark:border-gray-700 rounded-lg p-3 bg-white dark:bg-gray-800">
                    <!-- Las deudas se llenarán dinámicamente aquí -->
                </div>
                <div id="error_deudas" class="hidden">
                    <p class="mt-2 text-xs text-red-500 flex items-start gap-1">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293z" clip-rule="evenodd"/>
                        </svg>
                        <span id="error_deudas_texto"></span>
                    </p>
                </div>
                <div class="min-h-[20px]">
                    @error('deudas')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Adelanto de Pagos --}}
        <div id="contenedorMeses" class="sm:col-span-3 lg:col-span-3 hidden mt-6">
            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-4 pb-2 border-b border-gray-300 dark:border-gray-600">
                Adelanto de Pagos
            </h3>
            
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3 mb-4">
                    <p class="text-sm text-green-700 dark:text-green-400 flex items-start gap-2">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span><strong>Beneficio:</strong> Si adelanta el pago de varios meses, recibirá un descuento del 10% sobre el monto total adelantado.</span>
                    </p>
                </div>
                
                <div id="preguntaAdelantar" class="space-y-3">
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                        ¿Desea adelantar el pago de varios meses?
                    </p>
                    <button type="button" id="btnSiAdelantar"
                        class="px-5 py-2.5 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                        Sí, deseo adelantar pagos
                    </button>
                </div>

                <div id="inputMesesAdelantar" class="hidden mt-4 space-y-3">
                    <div class="flex flex-col">
                        <label for="meses_adelantar" class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                            ¿Cuántos meses desea adelantar?
                        </label>
                        <div class="flex items-stretch">
                            <input type="text" id="meses_adelantar" name="meses_adelantar" value="0" inputmode="numeric" pattern="[0-9]*" maxlength="2"
                                class="w-24 rounded-l-lg border border-r-0 border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-center text-gray-800 dark:text-white/90 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all">
                            <div class="flex flex-col border border-gray-300 dark:border-gray-700 rounded-r-lg overflow-hidden">
                                <button type="button" id="btnIncrementar" 
                                    class="px-3 py-1.5 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                                    </svg>
                                </button>
                                <div class="h-px bg-gray-300 dark:bg-gray-600"></div>
                                <button type="button" id="btnDecrementar"
                                    class="px-3 py-1.5 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Use los botones o escriba la cantidad de meses a adelantar</p>
                        @error('meses_adelantar')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="button" id="btnCancelarAdelantar"
                        class="text-sm text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 underline font-medium">
                        Cancelar y no adelantar
                    </button>
                </div>
            </div>
        </div>

        {{-- Fecha de Orden de Pago (readonly - fecha actual) --}}
        <div id="contenedorFecha" class="sm:col-span-3 lg:col-span-3 hidden mt-6">
            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-4 pb-2 border-b border-gray-300 dark:border-gray-600">
                Información de Fechas
            </h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex flex-col">
                    <label for="fecha_orden" class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                        Fecha de Orden de Pago
                    </label>
                    <input type="text" id="fecha_orden" readonly
                        value="{{ date('d/m/Y') }}"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Fecha de emisión de la orden</p>
                </div>

                <div class="flex flex-col">
                    <label for="fecha_vencimiento_display" class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                        Fecha de Vencimiento
                    </label>
                    <input type="text" id="fecha_vencimiento_display" readonly
                        value="{{ date('d/m/Y', strtotime('+7 days')) }}"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90">
                    <input type="hidden" id="fecha_vencimiento" name="fecha_vencimiento" value="{{ date('Y-m-d', strtotime('+7 days')) }}">
                    <p id="textoVencimiento" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tiene 7 días para realizar el pago</p>
                </div>
            </div>
        </div>

        {{-- Resumen (inicialmente hidden) --}}
        <div id="contenedorResumen" class="sm:col-span-3 lg:col-span-3 hidden">
            <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200 mb-3">Resumen de la Orden de Pago</h3>
                
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div class="text-gray-600 dark:text-gray-400">Meses a pagar:</div>
                    <div class="font-semibold text-gray-800 dark:text-white text-right" id="resumen_meses">1 mes</div>
                    
                    <div class="text-gray-600 dark:text-gray-400">Monto Base:</div>
                    <div class="font-semibold text-gray-800 dark:text-white text-right" id="resumen_base">S/ 0.00</div>
                    
                    <div class="text-green-600 dark:text-green-400">Descuentos:</div>
                    <div class="font-semibold text-green-600 dark:text-green-400 text-right" id="resumen_descuento">- S/ 0.00</div>
                    
                    <div class="text-red-600 dark:text-red-400">Moras:</div>
                    <div class="font-semibold text-red-600 dark:text-red-400 text-right" id="resumen_mora">+ S/ 0.00</div>
                    
                    <!-- Detalle de política aplicada -->
                    <div id="contenedor_detalle_politica" class="col-span-2 hidden mt-2 pt-2 border-t border-gray-300 dark:border-gray-600">
                        <div class="text-xs italic text-gray-600 dark:text-gray-400" id="resumen_detalle_politica"></div>
                    </div>
                    
                    <div class="border-t border-gray-300 dark:border-gray-600 pt-2 text-gray-700 dark:text-gray-200 font-bold">Total a Pagar:</div>
                    <div class="border-t border-gray-300 dark:border-gray-600 pt-2 font-bold text-indigo-600 dark:text-indigo-400 text-right text-lg" id="resumen_total">S/ 0.00</div>
                </div>
            </div>
        </div>

        {{-- Errores del servidor --}}
        <div id="error_servidor" class="sm:col-span-3 lg:col-span-3 hidden">
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 class="font-semibold text-red-800 dark:text-red-300 mb-1">Error al generar la orden de pago</h4>
                        <p class="text-sm text-red-700 dark:text-red-400" id="error_servidor_texto"></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Botón Generar Orden de Pago (deshabilitado inicialmente) --}}
        <div class="sm:col-span-3 lg:col-span-3 mt-6">
            <button type="submit" id="btnGenerarOrden" disabled
                class="w-full inline-flex items-center justify-center gap-2 rounded-lg border px-6 py-3 text-base font-semibold shadow-theme-xs transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-300 disabled:border-gray-300 disabled:text-gray-500 dark:disabled:bg-gray-700 dark:disabled:border-gray-700 dark:disabled:text-gray-500 enabled:border-indigo-600 enabled:bg-indigo-600 enabled:text-white enabled:hover:bg-indigo-700 enabled:hover:border-indigo-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Generar Orden de Pago
            </button>
        </div>
    </form>
</div>

@endsection

@section('custom-js')
    <script src="{{ asset('js/orden-pago-create.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/orden-pago-busqueda.js') }}?v={{ time() }}"></script>
@endsection
