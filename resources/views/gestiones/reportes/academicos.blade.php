@extends('base.administrativo.blank')

@section('titulo')
    Reporte Académico
@endsection

@section('extracss')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .chart-container {
        position: relative;
        height: 300px;
    }
</style>
@endsection

@section('contenido')
<div class="p-8 m-4 bg-gray-100 dark:bg-white/[0.03] rounded-2xl">
    <!-- Header -->
    <div class="flex pb-6 justify-between items-center border-b border-gray-200 dark:border-gray-700">
        <div>
            <h2 class="text-2xl font-bold dark:text-gray-200 text-gray-800 flex items-center gap-3">
                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                Reporte Académico
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Análisis de matrículas y prematrículas del sistema</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="mt-8">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
            </svg>
            Filtros de Búsqueda
        </h3>
        
        <form method="GET" id="filtrosForm">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-500 shadow-sm">
                            <svg id="iconoSeccion" class="w-5 h-5 text-white transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 id="tituloSeccion" class="text-base font-semibold text-gray-800 dark:text-gray-200 transition-all duration-300">
                                Filtros Estándar
                            </h4>
                            <p id="subtituloSeccion" class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 transition-all duration-300">
                                Selecciona período, nivel, grado y estado
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Rango personalizado</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="activarFechas" name="usar_fechas" value="1" 
                                {{ request('usar_fechas') ? 'checked' : '' }}
                                class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                </div>

                <!-- Contenido: Filtros EstÃ¡ndar -->
                <div id="filtrosEstandar" class="transition-all duration-300 {{ request('usar_fechas') ? 'hidden' : '' }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Período Académico -->
                        <div>
                        @php
                            $periodosArray = collect($periodos ?? [])->map(function($periodo) {
                                return ['valor' => $periodo->id_periodo_academico, 'texto' => $periodo->nombre];
                            })->toArray();
                            array_unshift($periodosArray, ['valor' => 'all', 'texto' => 'Todos los periodos']);
                        @endphp
                        @include('components.forms.combo', [
                            'label' => 'Período Académico',
                            'name' => 'periodo_academico',
                            'error' => false,
                            'value' => request('periodo_academico', 'all'),
                            'options' => $periodosArray,
                            'options_attributes' => ['valor', 'texto'],
                            'disableSearch' => true
                        ])
                    </div>

                    <!-- Nivel Educativo -->
                    <div>
                        @php
                            $nivelesArray = collect($niveles ?? [])->map(function($nivel) {
                                return ['valor' => $nivel->id_nivel, 'texto' => $nivel->nombre_nivel];
                            })->toArray();
                            array_unshift($nivelesArray, ['valor' => '', 'texto' => 'Todos los niveles']);
                        @endphp
                        @include('components.forms.combo', [
                            'label' => 'Nivel Educativo',
                            'name' => 'nivel_educativo',
                            'error' => false,
                            'value' => request('nivel_educativo'),
                            'options' => $nivelesArray,
                            'options_attributes' => ['valor', 'texto'],
                            'disableSearch' => true
                        ])
                    </div>

                    <!-- Grado -->
                    <div>
                        @php
                            $gradosArray = collect($grados ?? [])->map(function($grado) {
                                return ['valor' => $grado->id_grado, 'texto' => $grado->nombre_grado];
                            })->toArray();
                            array_unshift($gradosArray, ['valor' => '', 'texto' => 'Todos los grados']);
                        @endphp
                        @include('components.forms.combo', [
                            'label' => 'Grado',
                            'name' => 'grado',
                            'error' => false,
                            'value' => request('grado'),
                            'options' => $gradosArray,
                            'options_attributes' => ['valor', 'texto'],
                            'disableSearch' => true
                        ])
                    </div>

                    <!-- Estado Solicitud -->
                    <div>
                        @php
                            $estadosArray = [
                                ['valor' => '', 'texto' => 'Todos los estados'],
                                ['valor' => 'pendiente', 'texto' => 'Pendiente'],
                                ['valor' => 'en_revision', 'texto' => 'En RevisiÃ³n'],
                                ['valor' => 'aprobado', 'texto' => 'Aprobada'],
                                ['valor' => 'rechazado', 'texto' => 'Rechazada'],
                            ];
                        @endphp
                        @include('components.forms.combo', [
                            'label' => 'Estado Prematrí­cula',
                            'name' => 'estado_solicitud',
                            'error' => false,
                            'value' => request('estado_solicitud'),
                            'options' => $estadosArray,
                            'options_attributes' => ['valor', 'texto'],
                            'disableSearch' => true
                        ])
                    </div>
                    </div>
                </div>

                <!-- Contenido: Fechas Personalizadas -->
                <div id="inputsFechas" class="transition-all duration-300 {{ request('usar_fechas') ? '' : 'hidden' }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1 block">
                                Fecha inicio
                            </label>
                            <div class="relative">
                                <input type="text" name="fecha_inicio" id="fecha_inicio" value="{{ request('fecha_inicio') }}" placeholder="dd/mm/yyyy" readonly
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 pl-10 pr-3 py-2.5 text-sm text-gray-800 dark:text-white/90 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 cursor-pointer">
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1 block">
                                Fecha fin
                            </label>
                            <div class="relative">
                                <input type="text" name="fecha_fin" id="fecha_fin" value="{{ request('fecha_fin') }}" placeholder="dd/mm/yyyy" readonly
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 pl-10 pr-3 py-2.5 text-sm text-gray-800 dark:text-white/90 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 cursor-pointer">
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BotÃ³n Generar Reporte -->
            <div class="flex justify-end">
                <button type="submit" id="btnGenerarReporte"
                    {{ request('usar_fechas') && (!request('fecha_inicio') || !request('fecha_fin')) ? 'disabled' : '' }}
                    class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-3 text-sm font-semibold text-white shadow-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:hover:from-indigo-600 disabled:hover:to-purple-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Generar Reporte
                </button>
            </div>
        </form>
    </div>

    <!-- GrÃ¡ficos en Grid 2x4 -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        <!-- GrÃ¡fico 1: DistribuciÃ³n por Nivel -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                </svg>
                Distribución por Nivel Educativo
            </h3>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6 relative">
                <button onclick="resetZoom('nivelChart')" class="absolute top-2 right-2 px-3 py-1 text-xs bg-indigo-500 text-white rounded hover:bg-indigo-600 transition-colors z-10" title="Resetear zoom">
                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
                <canvas id="nivelChart" height="300"></canvas>
            </div>
        </div>

        <!-- GrÃ¡fico 2: MatrÃ­culas por Grado -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Matrículas por Grado
            </h3>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6 relative">
                <button onclick="resetZoom('gradoChart')" class="absolute top-2 right-2 px-3 py-1 text-xs bg-indigo-500 text-white rounded hover:bg-indigo-600 transition-colors z-10" title="Resetear zoom">
                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
                <canvas id="gradoChart" height="300"></canvas>
            </div>
        </div>

        <!-- GrÃ¡fico 3: DistribuciÃ³n por Escala -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Distribución por Escala Económica
            </h3>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6 relative">
                <button onclick="resetZoom('escalaChart')" class="absolute top-2 right-2 px-3 py-1 text-xs bg-indigo-500 text-white rounded hover:bg-indigo-600 transition-colors z-10" title="Resetear zoom">
                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
                <canvas id="escalaChart" height="300"></canvas>
            </div>
        </div>

        <!-- GrÃ¡fico 4: DistribuciÃ³n por Sexo -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Distribución por Sexo
            </h3>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6 relative">
                <button onclick="resetZoom('sexoChart')" class="absolute top-2 right-2 px-3 py-1 text-xs bg-indigo-500 text-white rounded hover:bg-indigo-600 transition-colors z-10" title="Resetear zoom">
                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
                <canvas id="sexoChart" height="300"></canvas>
            </div>
        </div>

        <!-- GrÃ¡fico 5: Estado PrematrÃ­culas -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Estado de Prematrículas
            </h3>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6 relative">
                <button onclick="resetZoom('estadoPrematChart')" class="absolute top-2 right-2 px-3 py-1 text-xs bg-indigo-500 text-white rounded hover:bg-indigo-600 transition-colors z-10" title="Resetear zoom">
                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
                <canvas id="estadoPrematChart" height="300"></canvas>
            </div>
        </div>

        <!-- GrÃ¡fico 6: Solicitudes por Mes -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                </svg>
                Solicitudes por Mes
            </h3>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6 relative">
                <button onclick="resetZoom('mesChart')" class="absolute top-2 right-2 px-3 py-1 text-xs bg-indigo-500 text-white rounded hover:bg-indigo-600 transition-colors z-10" title="Resetear zoom">
                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
                <canvas id="mesChart" height="300"></canvas>
            </div>
        </div>

        <!-- GrÃ¡fico 7: Tasa de AprobaciÃ³n -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Tasa de Aprobación
            </h3>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6 relative">
                <button onclick="resetZoom('aprobacionChart')" class="absolute top-2 right-2 px-3 py-1 text-xs bg-indigo-500 text-white rounded hover:bg-indigo-600 transition-colors z-10" title="Resetear zoom">
                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
                <canvas id="aprobacionChart" height="300"></canvas>
            </div>
        </div>

        <!-- GrÃ¡fico 8: Capacidad vs OcupaciÃ³n -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                Capacidad vs Ocupación (Top 10)
            </h3>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6 relative">
                <button onclick="resetZoom('capacidadChart')" class="absolute top-2 right-2 px-3 py-1 text-xs bg-indigo-500 text-white rounded hover:bg-indigo-600 transition-colors z-10" title="Resetear zoom">
                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
                <canvas id="capacidadChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js"></script>
<script>
console.log('=== SCRIPT CARGADO ===');

// Objeto global para almacenar instancias de gráficos
window.chartInstances = {};

// Función global para resetear zoom
function resetZoom(chartId) {
    if (window.chartInstances[chartId]) {
        window.chartInstances[chartId].resetZoom();
    }
}

// Función global para toggle de fechas
function toggleFechas() {
    console.log('=== toggleFechas llamada ===');
    const activar = document.getElementById('activarFechas').checked;
    const inputsFechas = document.getElementById('inputsFechas');
    const filtrosEstandar = document.getElementById('filtrosEstandar');
    const titulo = document.getElementById('tituloSeccion');
    const subtitulo = document.getElementById('subtituloSeccion');
    const icono = document.getElementById('iconoSeccion');
    
    console.log('Activar:', activar);
    console.log('inputsFechas:', inputsFechas);
    console.log('filtrosEstandar:', filtrosEstandar);
    
    if (activar) {
        inputsFechas.classList.remove('hidden');
        filtrosEstandar.classList.add('hidden');
        titulo.textContent = 'Rango de Fechas Personalizado';
        subtitulo.textContent = 'Define un período específico para el reporte';
        icono.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>';
    } else {
        inputsFechas.classList.add('hidden');
        filtrosEstandar.classList.remove('hidden');
        document.getElementById('fecha_inicio').value = '';
        document.getElementById('fecha_fin').value = '';
        titulo.textContent = 'Filtros Estándar';
        subtitulo.textContent = 'Selecciona período, nivel, grado y estado';
        icono.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>';
    }
    
    console.log('Toggle completado');
    
    if (typeof window.actualizarEstadoBoton === 'function') {
        window.actualizarEstadoBoton();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DOM CONTENT LOADED ===');
    
    // Agregar event listener al toggle ANTES de todo lo demás
    const toggleCheckbox = document.getElementById('activarFechas');
    if (toggleCheckbox) {
        toggleCheckbox.addEventListener('change', toggleFechas);
        console.log('✓ Event listener del toggle agregado');
    } else {
        console.error('✗ No se encontró el checkbox activarFechas');
    }
    
    // Inicializar Flatpickr para fechas
    flatpickr("#fecha_inicio", {
        dateFormat: "d/m/Y",
        locale: "es",
        allowInput: true,
        onChange: function() {
            if (typeof window.actualizarEstadoBoton === 'function') {
                window.actualizarEstadoBoton();
            }
        }
    });

    flatpickr("#fecha_fin", {
        dateFormat: "d/m/Y",
        locale: "es",
        allowInput: true,
        onChange: function() {
            if (typeof window.actualizarEstadoBoton === 'function') {
                window.actualizarEstadoBoton();
            }
        }
    });

    // Función para validar y habilitar/deshabilitar botón
    window.actualizarEstadoBoton = function() {
        const usarFechas = document.getElementById('activarFechas').checked;
        const fechaInicio = document.getElementById('fecha_inicio').value;
        const fechaFin = document.getElementById('fecha_fin').value;
        const btnReporte = document.getElementById('btnGenerarReporte');
        
        if (usarFechas && (!fechaInicio || !fechaFin)) {
            btnReporte.disabled = true;
        } else {
            btnReporte.disabled = false;
        }
    };

    
    if (typeof Chart === 'undefined') {
        console.error('Chart.js no estÃ¡ cargado');
        return;
    }
    
    console.log('âœ“ Chart.js cargado correctamente');
    
    // Detectar modo dark y configurar Chart.js globalmente
    const isDark = document.documentElement.classList.contains('dark');
    
    if (isDark) {
        Chart.defaults.color = '#ffffff';
        Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';
        Chart.defaults.scale.grid.color = 'rgba(255, 255, 255, 0.1)';
        Chart.defaults.scale.ticks.color = '#ffffff';
    } else {
        Chart.defaults.color = '#6b7280';
        Chart.defaults.borderColor = 'rgba(0, 0, 0, 0.1)';
        Chart.defaults.scale.grid.color = 'rgba(0, 0, 0, 0.1)';
        Chart.defaults.scale.ticks.color = '#6b7280';
    }
    
    console.log('Modo dark:', isDark);
    
    // Datos desde el backend
    const nivelesLabels = @json($nivelesLabels ?? []);
    const nivelesData = @json($nivelesData ?? []);
    const gradosLabels = @json($gradosLabels ?? []);
    const gradosData = @json($gradosData ?? []);
    const escalasLabels = @json($escalasLabels ?? []);
    const escalasData = @json($escalasData ?? []);
    const sexoLabels = @json($sexoLabels ?? []);
    const sexoData = @json($sexoData ?? []);
    const estadosLabels = @json($estadosLabels ?? []);
    const estadosData = @json($estadosData ?? []);
    const mesesLabels = @json($mesesLabels ?? []);
    const mesesData = @json($mesesData ?? []);
    const aprobLabels = @json($aprobLabels ?? []);
    const aprobadas = @json($aprobadas ?? []);
    const rechazadas = @json($rechazadas ?? []);
    const capacidadLabels = @json($capacidadLabels ?? []);
    const capacidadMaxima = @json($capacidadMaxima ?? []);
    const capacidadOcupada = @json($capacidadOcupada ?? []);
    
    // GRÃFICO 1: Nivel Educativo
    const canvas1 = document.getElementById('nivelChart');
    if (canvas1 && nivelesLabels.length > 0) {
        window.chartInstances['nivelChart'] = new Chart(canvas1.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: nivelesLabels,
                datasets: [{
                    data: nivelesData,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(245, 158, 11, 0.7)'
                    ],
                    borderColor: ['#3b82f6', '#10b981', '#f59e0b'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    zoom: {
                        zoom: { wheel: { enabled: true }, pinch: { enabled: true }, mode: 'xy' },
                        pan: { enabled: true, mode: 'xy' }
                    },
                    legend: { position: 'bottom' }
                }
            }
        });
        console.log('âœ“ GrÃ¡fico 1 (Nivel) creado');
    }
    
    // GRÃFICO 2: Grados
    const canvas2 = document.getElementById('gradoChart');
    if (canvas2 && gradosLabels.length > 0) {
        window.chartInstances['gradoChart'] = new Chart(canvas2.getContext('2d'), {
            type: 'bar',
            data: {
                labels: gradosLabels,
                datasets: [{
                    label: 'Alumnos',
                    data: gradosData,
                    backgroundColor: 'rgba(16, 185, 129, 0.7)',
                    borderColor: '#10b981',
                    borderWidth: 2,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    zoom: {
                        zoom: { wheel: { enabled: true }, pinch: { enabled: true }, mode: 'xy' },
                        pan: { enabled: true, mode: 'xy' }
                    },
                    legend: { display: false }
                },
                scales: { y: { beginAtZero: true } }
            }
        });
        console.log('âœ“ GrÃ¡fico 2 (Grado) creado');
    }
    
    // GRÃFICO 3: Escalas
    const canvas3 = document.getElementById('escalaChart');
    if (canvas3 && escalasLabels.length > 0) {
        window.chartInstances['escalaChart'] = new Chart(canvas3.getContext('2d'), {
            type: 'bar',
            data: {
                labels: escalasLabels,
                datasets: [{
                    label: 'Alumnos',
                    data: escalasData,
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.7)',
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(245, 158, 11, 0.7)',
                        'rgba(239, 68, 68, 0.7)',
                        'rgba(139, 92, 246, 0.7)'
                    ],
                    borderWidth: 2,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    zoom: {
                        zoom: { wheel: { enabled: true }, pinch: { enabled: true }, mode: 'xy' },
                        pan: { enabled: true, mode: 'xy' }
                    },
                    legend: { display: false }
                },
                scales: { y: { beginAtZero: true } }
            }
        });
        console.log('âœ“ GrÃ¡fico 3 (Escala) creado');
    }
    
    // GRÃFICO 4: Sexo
    const canvas4 = document.getElementById('sexoChart');
    if (canvas4 && sexoLabels.length > 0) {
        window.chartInstances['sexoChart'] = new Chart(canvas4.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: sexoLabels,
                datasets: [{
                    data: sexoData,
                    backgroundColor: [
                        'rgba(139, 92, 246, 0.7)',
                        'rgba(236, 72, 153, 0.7)'
                    ],
                    borderColor: ['#8b5cf6', '#ec4899'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    zoom: {
                        zoom: { wheel: { enabled: true }, pinch: { enabled: true }, mode: 'xy' },
                        pan: { enabled: true, mode: 'xy' }
                    },
                    legend: { position: 'bottom' }
                }
            }
        });
        console.log('âœ“ GrÃ¡fico 4 (Sexo) creado');
    }
    
    // GRÃFICO 5: Estado PrematrÃ­culas
    const canvas5 = document.getElementById('estadoPrematChart');
    if (canvas5 && estadosLabels.length > 0) {
        window.chartInstances['estadoPrematChart'] = new Chart(canvas5.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: estadosLabels,
                datasets: [{
                    data: estadosData,
                    backgroundColor: [
                        'rgba(245, 158, 11, 0.7)',
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(34, 197, 94, 0.7)',
                        'rgba(239, 68, 68, 0.7)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    zoom: {
                        zoom: { wheel: { enabled: true }, pinch: { enabled: true }, mode: 'xy' },
                        pan: { enabled: true, mode: 'xy' }
                    },
                    legend: { position: 'bottom' }
                }
            }
        });
        console.log('âœ“ GrÃ¡fico 5 (Estado Premat) creado');
    }
    
    // GRÃFICO 6: Solicitudes por Mes
    const canvas6 = document.getElementById('mesChart');
    if (canvas6 && mesesLabels.length > 0) {
        window.chartInstances['mesChart'] = new Chart(canvas6.getContext('2d'), {
            type: 'line',
            data: {
                labels: mesesLabels,
                datasets: [{
                    label: 'Solicitudes',
                    data: mesesData,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    zoom: {
                        zoom: { wheel: { enabled: true }, pinch: { enabled: true }, mode: 'xy' },
                        pan: { enabled: true, mode: 'xy' }
                    }
                },
                scales: { y: { beginAtZero: true } }
            }
        });
        console.log('âœ“ GrÃ¡fico 6 (Mes) creado');
    }
    
    // GRÃFICO 7: AprobaciÃ³n
    const canvas7 = document.getElementById('aprobacionChart');
    if (canvas7 && aprobLabels.length > 0) {
        window.chartInstances['aprobacionChart'] = new Chart(canvas7.getContext('2d'), {
            type: 'bar',
            data: {
                labels: aprobLabels,
                datasets: [
                    {
                        label: 'Aprobadas',
                        data: aprobadas,
                        backgroundColor: 'rgba(34, 197, 94, 0.7)',
                        borderColor: '#22c55e',
                        borderWidth: 2,
                        borderRadius: 6
                    },
                    {
                        label: 'Rechazadas',
                        data: rechazadas,
                        backgroundColor: 'rgba(239, 68, 68, 0.7)',
                        borderColor: '#ef4444',
                        borderWidth: 2,
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    zoom: {
                        zoom: { wheel: { enabled: true }, pinch: { enabled: true }, mode: 'xy' },
                        pan: { enabled: true, mode: 'xy' }
                    }
                },
                scales: { y: { beginAtZero: true } }
            }
        });
        console.log('âœ“ GrÃ¡fico 7 (AprobaciÃ³n) creado');
    }
    
    // GRÃFICO 8: Capacidad
    const canvas8 = document.getElementById('capacidadChart');
    if (canvas8 && capacidadLabels.length > 0) {
        window.chartInstances['capacidadChart'] = new Chart(canvas8.getContext('2d'), {
            type: 'bar',
            data: {
                labels: capacidadLabels,
                datasets: [
                    {
                        label: 'Capacidad MÃ¡xima',
                        data: capacidadMaxima,
                        backgroundColor: 'rgba(100, 116, 139, 0.5)',
                        borderColor: '#64748b',
                        borderWidth: 2,
                        borderRadius: 6
                    },
                    {
                        label: 'Ocupados',
                        data: capacidadOcupada,
                        backgroundColor: 'rgba(236, 72, 153, 0.7)',
                        borderColor: '#ec4899',
                        borderWidth: 2,
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    zoom: {
                        zoom: { wheel: { enabled: true }, pinch: { enabled: true }, mode: 'xy' },
                        pan: { enabled: true, mode: 'xy' }
                    }
                },
                scales: { 
                    y: { 
                        beginAtZero: true,
                        ticks: {
                            stepSize: 5
                        }
                    } 
                }
            }
        });
        console.log('âœ“ GrÃ¡fico 8 (Capacidad) creado');
    }
    
    console.log('=== InicializaciÃ³n completada ===');
    
    // Validar estado inicial del botÃ³n
    if (typeof window.actualizarEstadoBoton === 'function') {
        window.actualizarEstadoBoton();
    }
});

</script>
@endsection
