@extends('base.administrativo.blank')

@section('titulo')
    Reporte Financiero
@endsection

@section('extracss')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
<style>
    .flatpickr-calendar {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        border-radius: 12px;
        border: none;
        position: fixed !important;
        z-index: 9999;
    }
    .flatpickr-calendar.arrowTop:before,
    .flatpickr-calendar.arrowTop:after {
        display: none;
    }
    .flatpickr-day.selected {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .dark .flatpickr-calendar {
        background: #1f2937;
        border: 1px solid #374151;
    }
    .dark .flatpickr-day {
        color: #e5e7eb;
    }
    .dark .flatpickr-day:hover {
        background: #374151;
    }
    .dark .flatpickr-months .flatpickr-month {
        background: #1f2937;
        color: #e5e7eb;
    }
    .dark .flatpickr-weekday {
        color: #9ca3af;
    }
</style>
@endsection

@section('contenido')
<div class="p-8 m-4 bg-gray-100 dark:bg-white/[0.03] rounded-2xl">
    <!-- Header -->
    <div class="flex pb-6 justify-between items-center border-b border-gray-200 dark:border-gray-700">
        <div>
            <h2 class="text-2xl font-bold dark:text-gray-200 text-gray-800 flex items-center gap-3">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Reporte Financiero
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Visualiza los ingresos del sistema por período</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="mt-8">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
            </svg>
            Filtros de Búsqueda
        </h3>
        
        <form method="GET" id="filtroForm">
            <!-- Sección Única de Filtros -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6 mb-6">
                <!-- Header con toggle -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-500 shadow-sm">
                            <svg id="iconoSeccion" class="w-5 h-5 text-white transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 id="tituloSeccion" class="text-base font-semibold text-gray-800 dark:text-gray-200 transition-all duration-300">
                                Filtros Estándar
                            </h4>
                            <p id="subtituloSeccion" class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 transition-all duration-300">
                                Selecciona año, mes y tipo de pago
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Rango personalizado</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="activarFechas" name="usar_fechas" value="1" onchange="toggleFechas()" 
                                {{ request('usar_fechas') ? 'checked' : '' }}
                                class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>

                <!-- Contenido: Filtros Estándar -->
                <div id="filtrosEstandar" class="transition-all duration-300 {{ request('usar_fechas') ? 'hidden' : '' }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            @php
                                $aniosArray = collect($anios ?? [])->map(function($anio) {
                                    return ['valor' => $anio, 'texto' => $anio];
                                })->toArray();
                                array_unshift($aniosArray, ['valor' => 'all', 'texto' => 'Todos los años']);
                            @endphp
                            @include('components.forms.combo', [
                                'label' => 'Año',
                                'name' => 'anio',
                                'error' => false,
                                'value' => request('anio', 'all'),
                                'options' => $aniosArray,
                                'options_attributes' => ['valor', 'texto'],
                                'disableSearch' => true
                            ])
                        </div>

                        <div>
                            @php
                                $mesesArray = [
                                    ['valor' => '', 'texto' => 'Todos los meses'],
                                    ['valor' => 'ENERO', 'texto' => 'Enero'],
                                    ['valor' => 'FEBRERO', 'texto' => 'Febrero'],
                                    ['valor' => 'MARZO', 'texto' => 'Marzo'],
                                    ['valor' => 'ABRIL', 'texto' => 'Abril'],
                                    ['valor' => 'MAYO', 'texto' => 'Mayo'],
                                    ['valor' => 'JUNIO', 'texto' => 'Junio'],
                                    ['valor' => 'JULIO', 'texto' => 'Julio'],
                                    ['valor' => 'AGOSTO', 'texto' => 'Agosto'],
                                    ['valor' => 'SEPTIEMBRE', 'texto' => 'Septiembre'],
                                    ['valor' => 'OCTUBRE', 'texto' => 'Octubre'],
                                    ['valor' => 'NOVIEMBRE', 'texto' => 'Noviembre'],
                                    ['valor' => 'DICIEMBRE', 'texto' => 'Diciembre'],
                                ];
                            @endphp
                            @include('components.forms.combo', [
                                'label' => 'Mes',
                                'name' => 'mes',
                                'error' => false,
                                'value' => request('mes'),
                                'options' => $mesesArray,
                                'options_attributes' => ['valor', 'texto'],
                                'disableSearch' => true
                            ])
                        </div>

                        <div>
                            @php
                                $tiposPagoArray = collect($tipos_pago ?? [])->map(function($tipo) {
                                    return ['valor' => $tipo, 'texto' => ucfirst(str_replace('_', ' ', $tipo))];
                                })->toArray();
                                array_unshift($tiposPagoArray, ['valor' => '', 'texto' => 'Todos los tipos']);
                            @endphp
                            @include('components.forms.combo', [
                                'label' => 'Tipo de pago',
                                'name' => 'tipo_pago',
                                'error' => false,
                                'value' => request('tipo_pago'),
                                'options' => $tiposPagoArray,
                                'options_attributes' => ['valor', 'texto'],
                                'disableSearch' => true
                            ])
                        </div>

                        <div>
                            @php
                                $metodosPagoArray = collect($metodos_pago ?? [])->map(function($metodo) {
                                    return ['valor' => $metodo, 'texto' => ucfirst(str_replace('_', ' ', $metodo))];
                                })->toArray();
                                array_unshift($metodosPagoArray, ['valor' => '', 'texto' => 'Todos los métodos']);
                            @endphp
                            @include('components.forms.combo', [
                                'label' => 'Método de pago',
                                'name' => 'metodo_pago',
                                'error' => false,
                                'value' => request('metodo_pago'),
                                'options' => $metodosPagoArray,
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
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 pl-10 pr-3 py-2.5 text-sm text-gray-800 dark:text-white/90 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
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
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 pl-10 pr-3 py-2.5 text-sm text-gray-800 dark:text-white/90 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón Generar Reporte -->
            <div class="flex justify-end">
                <button type="submit" id="btnGenerarReporte"
                    {{ request('usar_fechas') && (!request('fecha_inicio') || !request('fecha_fin')) ? 'disabled' : '' }}
                    class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-3 text-sm font-semibold text-white shadow-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:hover:from-blue-600 disabled:hover:to-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Generar Reporte
                </button>
            </div>
        </form>
    </div>

    <!-- Gráficos en Grid 2x3 -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        <!-- Gráfico 1: Barras - Ingresos por período -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Ingresos por período
            </h3>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6 relative">
                <button onclick="resetZoom('pagosPorMesChart')" class="absolute top-2 right-2 px-3 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors z-10" title="Resetear zoom">
                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
                <canvas id="pagosPorMesChart" height="300"></canvas>
            </div>
        </div>

        <!-- Gráfico 2: Dona - Distribución por Método de Pago -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                </svg>
                Distribución por Método de Pago
            </h3>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6 relative">
                <button onclick="resetZoom('metodosPagoChart')" class="absolute top-2 right-2 px-3 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors z-10" title="Resetear zoom">
                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
                <canvas id="metodosPagoChart" height="300"></canvas>
            </div>
        </div>

        <!-- Gráfico 3: Barras Apiladas - Composición por Tipo de Pago -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Composición por Tipo de Pago
            </h3>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6 relative">
                <button onclick="resetZoom('tiposPagoChart')" class="absolute top-2 right-2 px-3 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors z-10" title="Resetear zoom">
                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
                <canvas id="tiposPagoChart" height="300"></canvas>
            </div>
        </div>

        <!-- Gráfico 4: Línea Dual - Pagos vs Deudas -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                </svg>
                Pagos Recibidos vs Deudas Generadas
            </h3>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6 relative">
                <button onclick="resetZoom('pagosVsDeudasChart')" class="absolute top-2 right-2 px-3 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors z-10" title="Resetear zoom">
                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
                <canvas id="pagosVsDeudasChart" height="300"></canvas>
            </div>
        </div>

        <!-- Gráfico 5: Línea - Evolución Acumulada de Ingresos -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                Evolución Acumulada de Ingresos
            </h3>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6 relative">
                <button onclick="resetZoom('acumuladoChart')" class="absolute top-2 right-2 px-3 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors z-10" title="Resetear zoom">
                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
                <canvas id="acumuladoChart" height="300"></canvas>
            </div>
        </div>

        <!-- Gráfico 6: Barras - Resumen General -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                Resumen General Financiero
            </h3>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6 relative">
                <button onclick="resetZoom('resumenChart')" class="absolute top-2 right-2 px-3 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors z-10" title="Resetear zoom">
                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
                <canvas id="resumenChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
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

// PRIMERO: Inicializar gráficos (no depende de Flatpickr)
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DOM CONTENT LOADED ===');
    
    // Verificar que Chart.js esté disponible
    if (typeof Chart === 'undefined') {
        console.error('Chart.js no está cargado');
        return;
    }
    
    console.log('✓ Chart.js cargado correctamente');
    
    // Función para obtener colores según el modo actual
    function getChartColors() {
        const isDark = document.documentElement.classList.contains('dark');
        return {
            axisColor: isDark ? '#ffffff' : '#6b7280',
            gridColor: isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
        };
    }
    
    const colors = getChartColors();
    
    // Datos de los gráficos desde el backend
    const pagosPorMes = @json($pagosPorMes ?? []);
    const labels = @json($labels ?? []);
    const metodosLabels = @json($metodosLabels ?? []);
    const metodosData = @json($metodosData ?? []);
    const pagosPorTipo = @json($pagosPorTipo ?? []);
    const deudasData = @json($deudasData ?? []);
    const acumuladoData = @json($acumuladoData ?? []);
    const resumenLabels = @json($resumenLabels ?? []);
    const resumenData = @json($resumenData ?? []);
    const resumenColors = @json($resumenColors ?? []);
    
    console.log('Datos recibidos:', {
        pagosPorMes,
        labels,
        metodosLabels,
        metodosData,
        pagosPorTipo,
        deudasData
    });
    
    // GRÁFICO 1: Barras - Ingresos por período
    const canvas1 = document.getElementById('pagosPorMesChart');
    if (canvas1) {
        const hayDatos = pagosPorMes && pagosPorMes.length > 0 && pagosPorMes.some(val => val > 0);
        
        if (hayDatos) {
            try {
                window.chartInstances['pagosPorMesChart'] = new Chart(canvas1.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Ingresos (S/)',
                            data: pagosPorMes,
                            backgroundColor: 'rgba(59, 130, 246, 0.7)',
                            borderColor: '#3b82f6',
                            borderWidth: 2,
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                ticks: {
                                    color: Chart.defaults.color
                                },
                                grid: {
                                    color: Chart.defaults.scale.grid.color
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: Chart.defaults.color,
                                    callback: function(value) {
                                        return 'S/ ' + value.toLocaleString('es-PE');
                                    }
                                },
                                grid: {
                                    color: Chart.defaults.scale.grid.color
                                }
                            }
                        },
                        plugins: {
                            zoom: {
                                zoom: {
                                    wheel: {
                                        enabled: true,
                                    },
                                    pinch: {
                                        enabled: true
                                    },
                                    mode: 'xy',
                                },
                                pan: {
                                    enabled: true,
                                    mode: 'xy',
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Ingresos: S/ ' + context.parsed.y.toLocaleString('es-PE', {minimumFractionDigits: 2});
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('✓ Gráfico 1 (Barras) creado');
            } catch(e) {
                console.error('Error creando gráfico 1:', e);
            }
        } else {
            canvas1.style.display = 'none';
            const mensaje = document.createElement('div');
            mensaje.className = 'flex flex-col items-center justify-center py-20';
            mensaje.innerHTML = '<p class="text-gray-500 dark:text-gray-400">No hay datos disponibles</p>';
            canvas1.parentElement.appendChild(mensaje);
        }
    }
    
    // GRÁFICO 2: Dona - Distribución por métodos de pago
    const canvas2 = document.getElementById('metodosPagoChart');
    if (canvas2) {
        const hayDatos = metodosData && metodosData.length > 0 && metodosData.some(val => val > 0);
        
        if (hayDatos) {
            try {
                window.chartInstances['metodosPagoChart'] = new Chart(canvas2.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: metodosLabels,
                        datasets: [{
                            data: metodosData,
                            backgroundColor: [
                                'rgba(59, 130, 246, 0.7)',
                                'rgba(16, 185, 129, 0.7)',
                                'rgba(245, 158, 11, 0.7)',
                                'rgba(239, 68, 68, 0.7)',
                                'rgba(139, 92, 246, 0.7)',
                                'rgba(236, 72, 153, 0.7)'
                            ],
                            borderColor: [
                                '#3b82f6',
                                '#10b981',
                                '#f59e0b',
                                '#ef4444',
                                '#8b5cf6',
                                '#ec4899'
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        aspectRatio: 2,
                        plugins: {
                            zoom: {
                                zoom: {
                                    wheel: {
                                        enabled: true,
                                    },
                                    pinch: {
                                        enabled: true
                                    },
                                    mode: 'xy',
                                },
                                pan: {
                                    enabled: true,
                                    mode: 'xy',
                                }
                            },
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: Chart.defaults.color
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const porcentaje = ((context.parsed / total) * 100).toFixed(1);
                                        return context.label + ': S/ ' + context.parsed.toLocaleString('es-PE', {minimumFractionDigits: 2}) + ' (' + porcentaje + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('✓ Gráfico 2 (Dona) creado');
            } catch(e) {
                console.error('Error creando gráfico 2:', e);
            }
        } else {
            canvas2.style.display = 'none';
            const mensaje = document.createElement('div');
            mensaje.className = 'flex flex-col items-center justify-center py-20';
            mensaje.innerHTML = '<p class="text-gray-500 dark:text-gray-400">No hay datos disponibles</p>';
            canvas2.parentElement.appendChild(mensaje);
        }
    }
    
    // GRÁFICO 3: Barras apiladas - Composición por tipo de pago
    const canvas3 = document.getElementById('tiposPagoChart');
    if (canvas3) {
        const hayDatos = pagosPorTipo && Object.keys(pagosPorTipo).length > 0;
        
        if (hayDatos) {
            try {
                window.chartInstances['tiposPagoChart'] = new Chart(canvas3.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: ['Tipos de Pago'],
                        datasets: Object.entries(pagosPorTipo).map(([tipo, monto], index) => ({
                            label: tipo,
                            data: [monto],
                            backgroundColor: [
                                'rgba(59, 130, 246, 0.7)',
                                'rgba(16, 185, 129, 0.7)',
                                'rgba(245, 158, 11, 0.7)',
                                'rgba(239, 68, 68, 0.7)',
                                'rgba(139, 92, 246, 0.7)'
                            ][index % 5],
                            borderColor: [
                                '#3b82f6',
                                '#10b981',
                                '#f59e0b',
                                '#ef4444',
                                '#8b5cf6'
                            ][index % 5],
                            borderWidth: 2,
                            borderRadius: 6
                        }))
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        aspectRatio: 2,
                        scales: {
                            x: {
                                stacked: true,
                                ticks: {
                                    color: Chart.defaults.color
                                },
                                grid: {
                                    color: Chart.defaults.scale.grid.color
                                }
                            },
                            y: {
                                stacked: true,
                                beginAtZero: true,
                                ticks: {
                                    color: Chart.defaults.color,
                                    callback: function(value) {
                                        return 'S/ ' + value.toLocaleString('es-PE');
                                    }
                                },
                                grid: {
                                    color: Chart.defaults.scale.grid.color
                                }
                            }
                        },
                        plugins: {
                            zoom: {
                                zoom: {
                                    wheel: {
                                        enabled: true,
                                    },
                                    pinch: {
                                        enabled: true
                                    },
                                    mode: 'xy',
                                },
                                pan: {
                                    enabled: true,
                                    mode: 'xy',
                                }
                            },
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: Chart.defaults.color
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': S/ ' + context.parsed.y.toLocaleString('es-PE', {minimumFractionDigits: 2});
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('✓ Gráfico 3 (Barras apiladas) creado');
            } catch(e) {
                console.error('Error creando gráfico 3:', e);
            }
        } else {
            canvas3.style.display = 'none';
            const mensaje = document.createElement('div');
            mensaje.className = 'flex flex-col items-center justify-center py-20';
            mensaje.innerHTML = '<p class="text-gray-500 dark:text-gray-400">No hay datos disponibles</p>';
            canvas3.parentElement.appendChild(mensaje);
        }
    }
    
    // GRÁFICO 4: Línea dual - Pagos vs Deudas
    const canvas4 = document.getElementById('pagosVsDeudasChart');
    if (canvas4) {
        const hayDatosPagos = pagosPorMes && pagosPorMes.length > 0 && pagosPorMes.some(val => val > 0);
        const hayDatosDeudas = deudasData && deudasData.length > 0 && deudasData.some(val => val > 0);
        
        if (hayDatosPagos || hayDatosDeudas) {
            try {
                window.chartInstances['pagosVsDeudasChart'] = new Chart(canvas4.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Pagos (S/)',
                                data: pagosPorMes,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                borderWidth: 3,
                                tension: 0.4,
                                fill: true
                            },
                            {
                                label: 'Deudas (S/)',
                                data: deudasData,
                                borderColor: '#ef4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                borderWidth: 3,
                                tension: 0.4,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        aspectRatio: 2,
                        scales: {
                            x: {
                                ticks: {
                                    color: Chart.defaults.color
                                },
                                grid: {
                                    color: Chart.defaults.scale.grid.color
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: Chart.defaults.color,
                                    callback: function(value) {
                                        return 'S/ ' + value.toLocaleString('es-PE');
                                    }
                                },
                                grid: {
                                    color: Chart.defaults.scale.grid.color
                                }
                            }
                        },
                        plugins: {
                            zoom: {
                                zoom: {
                                    wheel: {
                                        enabled: true,
                                    },
                                    pinch: {
                                        enabled: true
                                    },
                                    mode: 'xy',
                                },
                                pan: {
                                    enabled: true,
                                    mode: 'xy',
                                }
                            },
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: Chart.defaults.color
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': S/ ' + context.parsed.y.toLocaleString('es-PE', {minimumFractionDigits: 2});
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('✓ Gráfico 4 (Línea dual) creado');
            } catch(e) {
                console.error('Error creando gráfico 4:', e);
            }
        } else {
            canvas4.style.display = 'none';
            const mensaje = document.createElement('div');
            mensaje.className = 'flex flex-col items-center justify-center py-20';
            mensaje.innerHTML = '<p class="text-gray-500 dark:text-gray-400">No hay datos disponibles</p>';
            canvas4.parentElement.appendChild(mensaje);
        }
    }
    
    // GRÁFICO 5: Línea - Evolución acumulada de ingresos
    const canvas5 = document.getElementById('acumuladoChart');
    if (canvas5) {
        const hayDatos = acumuladoData && acumuladoData.length > 0 && acumuladoData.some(val => val > 0);
        
        if (hayDatos) {
            try {
                window.chartInstances['acumuladoChart'] = new Chart(canvas5.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Acumulado (S/)',
                            data: acumuladoData,
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#6366f1',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                ticks: {
                                    color: Chart.defaults.color
                                },
                                grid: {
                                    color: Chart.defaults.scale.grid.color
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: Chart.defaults.color,
                                    callback: function(value) {
                                        return 'S/ ' + value.toLocaleString('es-PE');
                                    }
                                },
                                grid: {
                                    color: Chart.defaults.scale.grid.color
                                }
                            }
                        },
                        plugins: {
                            zoom: {
                                zoom: {
                                    wheel: {
                                        enabled: true,
                                    },
                                    pinch: {
                                        enabled: true
                                    },
                                    mode: 'xy',
                                },
                                pan: {
                                    enabled: true,
                                    mode: 'xy',
                                }
                            },
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Acumulado: S/ ' + context.parsed.y.toLocaleString('es-PE', {minimumFractionDigits: 2});
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('✓ Gráfico 5 (Evolución acumulada) creado');
            } catch(e) {
                console.error('Error creando gráfico 5:', e);
            }
        } else {
            canvas5.style.display = 'none';
            const mensaje = document.createElement('div');
            mensaje.className = 'flex flex-col items-center justify-center py-20';
            mensaje.innerHTML = '<p class="text-gray-500 dark:text-gray-400">No hay datos disponibles</p>';
            canvas5.parentElement.appendChild(mensaje);
        }
    }
    
    // GRÁFICO 6: Barras - Resumen general financiero
    const canvas6 = document.getElementById('resumenChart');
    if (canvas6) {
        const hayDatos = resumenData && resumenData.length > 0 && resumenData.some(val => val > 0);
        
        if (hayDatos) {
            try {
                window.chartInstances['resumenChart'] = new Chart(canvas6.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: resumenLabels,
                        datasets: [{
                            label: 'Monto (S/)',
                            data: resumenData,
                            backgroundColor: resumenColors,
                            borderColor: [
                                '#10b981',
                                '#ef4444',
                                '#3b82f6'
                            ],
                            borderWidth: 2,
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                ticks: {
                                    color: Chart.defaults.color
                                },
                                grid: {
                                    color: Chart.defaults.scale.grid.color
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: Chart.defaults.color,
                                    callback: function(value) {
                                        return 'S/ ' + value.toLocaleString('es-PE');
                                    }
                                },
                                grid: {
                                    color: Chart.defaults.scale.grid.color
                                }
                            }
                        },
                        plugins: {
                            zoom: {
                                zoom: {
                                    wheel: {
                                        enabled: true,
                                    },
                                    pinch: {
                                        enabled: true
                                    },
                                    mode: 'xy',
                                },
                                pan: {
                                    enabled: true,
                                    mode: 'xy',
                                }
                            },
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.label + ': S/ ' + context.parsed.y.toLocaleString('es-PE', {minimumFractionDigits: 2});
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('✓ Gráfico 6 (Resumen general) creado');
            } catch(e) {
                console.error('Error creando gráfico 6:', e);
            }
        } else {
            canvas6.style.display = 'none';
            const mensaje = document.createElement('div');
            mensaje.className = 'flex flex-col items-center justify-center py-20';
            mensaje.innerHTML = '<p class="text-gray-500 dark:text-gray-400">No hay datos disponibles</p>';
            canvas6.parentElement.appendChild(mensaje);
        }
    }
    
    console.log('=== Inicialización de gráficos completada ===');
});

// SEGUNDO: Flatpickr (se carga después)
window.addEventListener('load', function() {
    console.log('=== WINDOW LOAD (Flatpickr) ===');
    
    if (typeof flatpickr === 'undefined') {
        console.error('Flatpickr no cargado');
        return;
    }
    
    // Inicializar datepickers con calendario
    const fechaInicioInput = document.getElementById('fecha_inicio');
    const fechaFinInput = document.getElementById('fecha_fin');
        
        // Función para posicionar el calendario dinámicamente
        function posicionarCalendario(input, calendar) {
            const rect = input.getBoundingClientRect();
            const calendarHeight = calendar.offsetHeight || 320;
            const windowHeight = window.innerHeight;
            
            // Determinar si hay espacio abajo
            const espacioAbajo = windowHeight - rect.bottom;
            const abrirArriba = espacioAbajo < calendarHeight && rect.top > calendarHeight;
            
            calendar.style.left = rect.left + 'px';
            
            if (abrirArriba) {
                calendar.style.top = (rect.top - calendarHeight - 5) + 'px';
                calendar.classList.remove('arrowBottom');
                calendar.classList.add('arrowTop');
            } else {
                calendar.style.top = (rect.bottom + 5) + 'px';
                calendar.classList.remove('arrowTop');
                calendar.classList.add('arrowBottom');
            }
        }
        
        // Configuración de Flatpickr para fecha inicio
        const fpInicio = flatpickr(fechaInicioInput, {
            dateFormat: 'd/m/Y',
            locale: flatpickr.l10ns.es || flatpickr.l10ns.default,
            allowInput: false,
            disableMobile: true,
            onChange: function(selectedDates, dateStr, instance) {
                // Si hay fecha fin seleccionada, validar que fecha inicio sea menor o igual
                if (fechaFinInput.value) {
                    const [diaF, mesF, anioF] = fechaFinInput.value.split('/').map(Number);
                    const fechaFin = new Date(anioF, mesF - 1, diaF);
                    
                    if (selectedDates[0] > fechaFin) {
                        // Ajustar fecha fin automáticamente
                        fpFin.setDate(selectedDates[0]);
                    }
                }
                actualizarEstadoBoton();
            },
            onOpen: function(selectedDates, dateStr, instance) {
                posicionarCalendario(fechaInicioInput, instance.calendarContainer);
                
                // Reposicionar en scroll
                const scrollHandler = function() {
                    if (instance.isOpen) {
                        posicionarCalendario(fechaInicioInput, instance.calendarContainer);
                    }
                };
                window.addEventListener('scroll', scrollHandler, true);
                
                // Limpiar listener al cerrar
                instance.config.onClose.push(function() {
                    window.removeEventListener('scroll', scrollHandler, true);
                });
            }
        });
        
        // Configuración de Flatpickr para fecha fin
        const fpFin = flatpickr(fechaFinInput, {
            dateFormat: 'd/m/Y',
            locale: flatpickr.l10ns.es || flatpickr.l10ns.default,
            allowInput: false,
            disableMobile: true,
            onChange: function(selectedDates, dateStr, instance) {
                // Si hay fecha inicio seleccionada, validar que fecha fin sea mayor o igual
                if (fechaInicioInput.value) {
                    const [diaI, mesI, anioI] = fechaInicioInput.value.split('/').map(Number);
                    const fechaInicio = new Date(anioI, mesI - 1, diaI);
                    
                    if (selectedDates[0] < fechaInicio) {
                        // Ajustar fecha inicio automáticamente
                        fpInicio.setDate(selectedDates[0]);
                    }
                }
                actualizarEstadoBoton();
            },
            onOpen: function(selectedDates, dateStr, instance) {
                posicionarCalendario(fechaFinInput, instance.calendarContainer);
                
                // Reposicionar en scroll
                const scrollHandler = function() {
                    if (instance.isOpen) {
                        posicionarCalendario(fechaFinInput, instance.calendarContainer);
                    }
                };
                window.addEventListener('scroll', scrollHandler, true);
                
                // Limpiar listener al cerrar
                instance.config.onClose.push(function() {
                    window.removeEventListener('scroll', scrollHandler, true);
                });
            }
        });
        
        // Función para validar el estado del botón
        window.actualizarEstadoBoton = function() {
            const boton = document.getElementById('btnGenerarReporte');
            const usarFechas = document.getElementById('activarFechas').checked;
            
            if (!usarFechas) {
                // Si no usa fechas personalizadas, siempre habilitado
                boton.disabled = false;
                return;
            }
            
            // Si usa fechas, validar que ambas estén seleccionadas
            const fechaInicio = fechaInicioInput.value;
            const fechaFin = fechaFinInput.value;
            
            boton.disabled = !(fechaInicio && fechaFin);
        };
        
        // Validar estado inicial del botón
        actualizarEstadoBoton();
}); // Cerrar window.addEventListener('load')

// Función global para toggle de fechas (fuera de event listeners)
function toggleFechas() {
    const activar = document.getElementById('activarFechas').checked;
    const inputsFechas = document.getElementById('inputsFechas');
    const filtrosEstandar = document.getElementById('filtrosEstandar');
    const titulo = document.getElementById('tituloSeccion');
    const subtitulo = document.getElementById('subtituloSeccion');
    const icono = document.getElementById('iconoSeccion');
    
    if (activar) {
        // Mostrar fechas personalizadas
        inputsFechas.classList.remove('hidden');
        filtrosEstandar.classList.add('hidden');
        
        // Cambiar título e ícono
        titulo.textContent = 'Rango de Fechas Personalizado';
        subtitulo.textContent = 'Define un período específico para el reporte';
        icono.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>';
    } else {
        // Mostrar filtros estándar
        inputsFechas.classList.add('hidden');
        filtrosEstandar.classList.remove('hidden');
        
        // Limpiar fechas
        document.getElementById('fecha_inicio').value = '';
        document.getElementById('fecha_fin').value = '';
        
        // Cambiar título e ícono
        titulo.textContent = 'Filtros Estándar';
        subtitulo.textContent = 'Selecciona año, mes y tipo de pago';
        icono.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>';
    }
    
    // Actualizar estado del botón (si existe la función)
    if (typeof window.actualizarEstadoBoton === 'function') {
        window.actualizarEstadoBoton();
    }
}
</script>
@endpush

