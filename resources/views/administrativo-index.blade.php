@extends('base.administrativo.blank')

@section('titulo')
    Dashboard de Matrículas y Pagos
@endsection

@section('contenido')
    {{-- Contenedor principal con fondo base neutro y patrón sutil --}}
    <div class="min-h-screen bg-slate-50 dark:bg-slate-900 font-sans antialiased selection:bg-indigo-500 selection:text-white pb-12">
        
        {{-- Header / Breadcrumb area --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
            
            @if($esDirector)
                {{-- TARJETA DIRECTOR: Estilo Premium/Glassmorphism --}}
                <div class="relative overflow-hidden mb-10 p-8 sm:p-10 rounded-3xl shadow-2xl group">
                    {{-- Fondo animado --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-violet-600 to-blue-700 dark:from-indigo-900 dark:via-purple-900 dark:to-blue-950 transition-all duration-1000"></div>
                    <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150"></div>
                    
                    {{-- Elementos decorativos de fondo --}}
                    <div class="absolute -top-24 -right-24 w-64 h-64 bg-yellow-400/20 rounded-full blur-3xl group-hover:bg-yellow-400/30 transition-all duration-700"></div>
                    <div class="absolute bottom-0 right-1/4 w-40 h-40 bg-blue-400/20 rounded-full blur-2xl"></div>

                    <div class="relative z-10 flex flex-col md:flex-row items-center md:items-start gap-8">
                        {{-- Avatar / Icono --}}
                        <div class="relative flex-shrink-0 group-hover:-translate-y-2 transition-transform duration-500 ease-out">
                            <div class="w-24 h-24 sm:w-28 sm:h-28 bg-gradient-to-tr from-yellow-300 to-amber-500 rounded-2xl flex items-center justify-center shadow-xl shadow-amber-500/20 ring-4 ring-white/10 backdrop-blur-sm">
                                <svg class="w-14 h-14 text-white drop-shadow-md" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                </svg>
                            </div>
                            {{-- Badge de notificación --}}
                            <div class="absolute -top-2 -right-2 flex h-6 w-6">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-300 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-6 w-6 bg-yellow-400 border-2 border-indigo-600"></span>
                            </div>
                        </div>

                        {{-- Texto de bienvenida --}}
                        <div class="flex-1 text-center md:text-left space-y-3">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 backdrop-blur-md">
                                <svg class="w-4 h-4 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-xs font-bold text-yellow-100 uppercase tracking-widest">Vista Estratégica</span>
                            </div>
                            
                            <h2 class="text-4xl md:text-5xl font-black text-white tracking-tight leading-tight">
                                Bienvenido, <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-200 to-amber-100">Director {{ explode(',', $nombreCompleto)[0] }}</span>
                            </h2>
                            
                            <p class="text-lg text-indigo-100/90 font-medium max-w-2xl">
                                Panel de control integral para la toma de decisiones basada en datos financieros y académicos en tiempo real.
                            </p>
                        </div>
                    </div>
                </div>
            @else
                {{-- TARJETA ESTÁNDAR --}}
                <div class="mb-10 p-8 bg-white dark:bg-slate-800 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-black/30 border border-slate-100 dark:border-slate-700 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-50 dark:bg-blue-900/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                    
                    <div class="relative z-10 flex items-center gap-6">
                        <div class="hidden sm:flex w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl items-center justify-center shadow-lg shadow-blue-500/20 transform group-hover:rotate-3 transition-transform duration-300">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-1">Panel Administrativo</p>
                            <h2 class="text-3xl font-bold text-slate-800 dark:text-white">
                                Hola, <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400">{{ $nombreCompleto }}</span>
                            </h2>
                            <p class="text-slate-500 dark:text-slate-400 mt-1">Gestión eficiente de matrículas y control de pagos.</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Banner Guía Interactiva --}}
            <div class="mb-10 relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 dark:from-emerald-600 dark:to-teal-800 shadow-xl shadow-emerald-500/20 p-1">
                <div class="bg-white/5 backdrop-blur-sm p-6 sm:p-8 rounded-xl h-full flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0 animate-pulse">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white mb-1">¿Necesitas ayuda con los pagos?</h3>
                            <p class="text-emerald-50 dark:text-emerald-100 text-sm sm:text-base opacity-90">Activa nuestra guía paso a paso para aprender el proceso en segundos.</p>
                        </div>
                    </div>
                    
                    <button id="btnIniciarGuiaPagos" class="w-full md:w-auto px-6 py-3 bg-white text-emerald-700 font-bold rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-2 group">
                        <span>Iniciar Tutorial</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- GRID DE ESTADÍSTICAS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                
                {{-- Card 1: Matrículas --}}
                <div class="group relative bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none dark:border dark:border-slate-700 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 overflow-hidden cursor-default">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity duration-300 transform group-hover:scale-110">
                        <svg class="w-24 h-24 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-2 h-8 bg-blue-500 rounded-full"></div>
                            <h3 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Matrículas Activas</h3>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <p class="text-4xl font-black text-slate-800 dark:text-white tracking-tight">{{$totalMatriculas}}</p>
                        </div>
                        <div class="mt-4 flex items-center gap-1 text-xs font-medium text-slate-400">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                            <span>Ciclo actual</span>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-indigo-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                </div>

                {{-- Card 2: Pagos --}}
                <div class="group relative bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none dark:border dark:border-slate-700 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 overflow-hidden cursor-default">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity duration-300 transform group-hover:scale-110">
                        <svg class="w-24 h-24 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-2 h-8 bg-emerald-500 rounded-full"></div>
                            <h3 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Ingresos del Mes</h3>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-xl font-medium text-slate-400">S/.</span>
                            <p class="text-4xl font-black text-slate-800 dark:text-white tracking-tight tabular-nums">{{ number_format($totalPagosMes, 2, ',', '.') }}</p>
                        </div>
                        <div class="mt-4 flex items-center gap-1 text-xs font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-1 rounded w-fit">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            <span>Recaudación mensual</span>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-500 to-teal-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                </div>

                {{-- Card 3: Alumnos --}}
                <div class="group relative bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none dark:border dark:border-slate-700 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 overflow-hidden cursor-default">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity duration-300 transform group-hover:scale-110">
                        <svg class="w-24 h-24 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-2 h-8 bg-purple-500 rounded-full"></div>
                            <h3 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Alumnos</h3>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <p class="text-4xl font-black text-slate-800 dark:text-white tracking-tight">{{$totalAlumnos}}</p>
                        </div>
                        <div class="mt-4 flex items-center gap-1 text-xs font-medium text-slate-400">
                            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                            <span>Base de datos total</span>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-purple-500 to-pink-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                </div>

                {{-- Card 4: Deudas --}}
                <div class="group relative bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none dark:border dark:border-slate-700 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 overflow-hidden cursor-default">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity duration-300 transform group-hover:scale-110">
                        <svg class="w-24 h-24 text-red-500 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-2 h-8 bg-red-500 rounded-full"></div>
                            <h3 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Deudas Pendientes</h3>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-lg sm:text-xl font-medium text-slate-400">S/.</span>
                            <p class="text-4xl md:text-3xl  font-black text-slate-800 dark:text-white tracking-tight tabular-nums break-all sm:break-normal">{{ number_format($totalDeudasPendientes, 2, ',', '.') }}</p>
                        </div>
                        <div class="mt-4 flex items-center gap-1 text-xs font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 px-2 py-1 rounded w-fit">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>Requiere atención</span>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-red-500 to-orange-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Overlay y Tooltip para Guía Interactiva --}}
    <div id="overlayGuia" class="hidden fixed inset-0 z-[9998] transition-opacity duration-300 opacity-0">
        <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm"></div>
    </div>

    {{-- Tooltip flotante mejorado --}}
    <div id="tooltipGuia" class="hidden fixed z-[9999] w-96 transition-all duration-300 transform scale-95 opacity-0">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl ring-1 ring-black/5 flex flex-col overflow-hidden">
            {{-- Header --}}
            <div class="relative bg-gradient-to-r from-emerald-500 to-teal-600 p-5">
                <div class="absolute top-0 right-0 p-2">
                    <button id="btnCerrarGuia" class="text-white/60 hover:text-white transition-colors rounded-full p-1 hover:bg-white/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="flex items-center gap-2 text-xs font-bold text-emerald-100 uppercase tracking-wider mb-2">
                    <span class="bg-white/20 px-2 py-0.5 rounded">Tutorial</span>
                    <span id="tooltipPaso">PASO 1 DE 4</span>
                </div>
                <h3 id="tooltipTitulo" class="text-xl font-bold text-white leading-tight">Título del paso</h3>
                
                {{-- Barra de progreso --}}
                <div class="absolute bottom-0 left-0 w-full h-1 bg-black/10">
                    <div id="tooltipProgreso" class="h-full bg-yellow-400 transition-all duration-500 ease-out" style="width: 25%"></div>
                </div>
            </div>

            {{-- Body --}}
            <div class="p-6">
                <p id="tooltipDescripcion" class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-6">Descripción del paso</p>
                
                <div class="flex items-center justify-between">
                    <button id="btnAnteriorGuia" class="text-sm font-semibold text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors disabled:opacity-30 flex items-center gap-1" disabled>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Anterior
                    </button>
                    <button id="btnSiguienteGuia" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-full shadow-lg hover:shadow-emerald-500/30 transition-all flex items-center gap-2">
                        <span>Siguiente</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>
        
        {{-- Flecha --}}
        <div id="tooltipFlecha" class="absolute w-4 h-4 bg-white dark:bg-slate-800 transform rotate-45 border border-black/5 dark:border-slate-700 z-[-1]"></div>
    </div>

    {{-- Script para la Guía Interactiva (COMPLETADO) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnIniciarGuia = document.getElementById('btnIniciarGuiaPagos');
            const overlay = document.getElementById('overlayGuia');
            const tooltip = document.getElementById('tooltipGuia');
            const btnCerrarGuia = document.getElementById('btnCerrarGuia');
            const btnAnteriorGuia = document.getElementById('btnAnteriorGuia');
            const btnSiguienteGuia = document.getElementById('btnSiguienteGuia');
            const tooltipFlecha = document.getElementById('tooltipFlecha');

            let pasoActual = 0;
            let elementoActual = null;

            // Definición de los pasos (Asegúrate de que los selectores existan en tu layout base)
            const pasos = [
                {
                    selector: '#menu-gestion-financiera', // ID hipotético del menú
                    titulo: 'Gestión Financiera',
                    descripcion: 'Ubica en el menú lateral la sección de finanzas. Aquí centralizamos todas las operaciones monetarias.',
                    posicion: 'right'
                },
                {
                    selector: '#submenu-orden-de-pago',
                    titulo: 'Crear Orden',
                    descripcion: 'Selecciona "Orden de Pago" para generar un nuevo cobro a un alumno.',
                    posicion: 'right'
                },
                // Agrega más pasos según necesites
            ];

            function toggleOverlay(show) {
                if (show) {
                    overlay.classList.remove('hidden');
                    // Pequeño delay para la animación de opacidad
                    setTimeout(() => overlay.classList.remove('opacity-0'), 10);
                } else {
                    overlay.classList.add('opacity-0');
                    setTimeout(() => overlay.classList.add('hidden'), 300);
                }
            }

            function toggleTooltip(show) {
                if (show) {
                    tooltip.classList.remove('hidden');
                    setTimeout(() => {
                        tooltip.classList.remove('scale-95', 'opacity-0');
                        tooltip.classList.add('scale-100', 'opacity-100');
                    }, 10);
                } else {
                    tooltip.classList.remove('scale-100', 'opacity-100');
                    tooltip.classList.add('scale-95', 'opacity-0');
                    setTimeout(() => tooltip.classList.add('hidden'), 300);
                }
            }

            function posicionarTooltip(targetRect, posicion) {
                const tooltipRect = tooltip.getBoundingClientRect();
                const margin = 15;
                let top, left;
                
                // Lógica simple de posicionamiento (se puede mejorar con Popper.js si estuviera disponible)
                if (posicion === 'right') {
                    top = targetRect.top + (targetRect.height / 2) - (tooltipRect.height / 2);
                    left = targetRect.right + margin;
                    
                    // Ajuste de flecha
                    tooltipFlecha.style.left = '-6px';
                    tooltipFlecha.style.top = '50%';
                    tooltipFlecha.style.marginTop = '-8px';
                } else if (posicion === 'bottom') {
                    top = targetRect.bottom + margin;
                    left = targetRect.left + (targetRect.width / 2) - (tooltipRect.width / 2);

                    tooltipFlecha.style.top = '-6px';
                    tooltipFlecha.style.left = '50%';
                    tooltipFlecha.style.marginLeft = '-8px';
                }

                // Asegurar que no se salga de la pantalla
                if (top < 10) top = 10;
                
                tooltip.style.top = `${top}px`;
                tooltip.style.left = `${left}px`;
            }

            function resaltarElemento(elemento) {
                // Quitamos resaltado anterior
                if (elementoActual) {
                    elementoActual.style.zIndex = '';
                    elementoActual.style.position = '';
                    elementoActual.classList.remove('ring-4', 'ring-emerald-400', 'ring-offset-2');
                }

                elementoActual = elemento;
                
                // Aplicamos resaltado nuevo
                if (elemento) {
                    // Calculamos estilo computado para ver si position ya está definido
                    const style = window.getComputedStyle(elemento);
                    if (style.position === 'static') {
                        elemento.style.position = 'relative';
                    }
                    elemento.style.zIndex = '9999';
                    elemento.classList.add('ring-4', 'ring-emerald-400', 'ring-offset-4', 'ring-offset-slate-900', 'rounded-lg', 'transition-all');
                    
                    elemento.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }

            function mostrarPaso() {
                const paso = pasos[pasoActual];
                // Si no hay pasos definidos o el selector falla, salir
                if (!paso) return; 
                
                const elemento = document.querySelector(paso.selector);

                // Actualizar textos
                document.getElementById('tooltipTitulo').innerText = paso.titulo;
                document.getElementById('tooltipDescripcion').innerHTML = paso.descripcion;
                document.getElementById('tooltipPaso').innerText = `PASO ${pasoActual + 1} DE ${pasos.length}`;
                
                // Barra de progreso
                const porcentaje = ((pasoActual + 1) / pasos.length) * 100;
                document.getElementById('tooltipProgreso').style.width = `${porcentaje}%`;

                // Botones estado
                btnAnteriorGuia.disabled = pasoActual === 0;
                if (pasoActual === pasos.length - 1) {
                    btnSiguienteGuia.innerHTML = '<span>Finalizar</span> <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
                } else {
                    btnSiguienteGuia.innerHTML = '<span>Siguiente</span> <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>';
                }

                if (elemento) {
                    resaltarElemento(elemento);
                    // Esperar un poco a que el scroll termine antes de posicionar el tooltip exacto
                    setTimeout(() => {
                        posicionarTooltip(elemento.getBoundingClientRect(), paso.posicion);
                        toggleTooltip(true);
                    }, 500);
                } else {
                    // Fallback si el elemento no existe (ej. menú cerrado)
                    console.warn('Elemento de guía no encontrado: ' + paso.selector);
                    toggleTooltip(true); // Mostrar tooltip en el centro o posición por defecto
                }
            }

            function cerrarGuiaCompleta() {
                toggleOverlay(false);
                toggleTooltip(false);
                if (elementoActual) {
                    resaltarElemento(null); // Limpiar estilos
                }
            }

            // Event Listeners
            if(btnIniciarGuia) {
                btnIniciarGuia.addEventListener('click', () => {
                    toggleOverlay(true);
                    pasoActual = 0;
                    mostrarPaso();
                });
            }

            btnCerrarGuia.addEventListener('click', cerrarGuiaCompleta);

            btnSiguienteGuia.addEventListener('click', () => {
                if (pasoActual < pasos.length - 1) {
                    pasoActual++;
                    mostrarPaso();
                } else {
                    cerrarGuiaCompleta();
                    // Opcional: Mostrar mensaje de éxito
                }
            });

            btnAnteriorGuia.addEventListener('click', () => {
                if (pasoActual > 0) {
                    pasoActual--;
                    mostrarPaso();
                }
            });
        });
    </script>
@endsection