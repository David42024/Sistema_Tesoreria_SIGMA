@extends('base.administrativo.blank')

@section('titulo')
  Dashboard de Matrículas y Pagos
@endsection

@section('contenido')
    <div class="p-8 bg-gradient-to-br min-h-screen font-sans antialiased">
        {{-- Fondo degradado suave con tonos azules/índigo para una sensación educativa/financiera --}}

        @if($esDirector)
            {{-- Mensaje personalizado para el Director --}}
            <div class="mb-10 p-10 bg-gradient-to-br from-indigo-600 via-purple-600 to-blue-700 dark:from-indigo-800 dark:via-purple-800 dark:to-blue-900 rounded-3xl shadow-2xl transform transition-all duration-500 hover:shadow-purple-500/40 hover:scale-[1.01] border-2 border-white/20">
                <div class="flex items-center gap-6 mb-4">
                    <div class="relative flex-shrink-0">
                        <div class="w-24 h-24 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-2xl flex items-center justify-center shadow-2xl transform transition-transform duration-300 hover:rotate-6 hover:scale-110">
                            <svg class="w-14 h-14 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="absolute -top-1 -right-1 w-6 h-6 bg-yellow-300 rounded-full animate-ping"></div>
                        <div class="absolute -top-1 -right-1 w-6 h-6 bg-yellow-400 rounded-full"></div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-yellow-300 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                            </svg>
                            <p class="text-sm font-bold text-yellow-300 uppercase tracking-widest">Director General</p>
                        </div>
                        <h2 class="text-5xl font-black text-white leading-tight drop-shadow-lg">
                            ¡Bienvenido, <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-200 via-yellow-300 to-amber-200">Director {{ explode(',', $nombreCompleto)[0] }}</span>!
                        </h2>
                        <div class="flex items-center gap-2 mt-3">
                            <svg class="w-5 h-5 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <p class="text-lg text-white/90 font-medium">
                                Panel de control estratégico | Gestión integral de la institución
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-6 p-4 bg-white/10 backdrop-blur-sm rounded-xl border border-white/20">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-white/95 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <p class="text-base text-white/95 leading-relaxed">
                            <strong>Desde aquí puede supervisar</strong> todas las operaciones académicas y financieras de la institución. 
                            Monitoree matrículas, pagos, deudas y tome decisiones informadas basadas en datos en tiempo real.
                        </p>
                    </div>
                </div>
            </div>
        @else
            {{-- Mensaje estándar para otros usuarios --}}
            <div class="mb-10 p-10 bg-gradient-to-r from-white via-blue-50/30 to-indigo-50/30 dark:from-gray-800 dark:via-gray-800 dark:to-gray-800 rounded-3xl shadow-2xl transform transition-all duration-500 hover:shadow-blue-500/30 border-2 border-blue-200/50 dark:border-gray-700 backdrop-blur-sm">
                <div class="flex items-center gap-6 mb-4">
                    <div class="flex-shrink-0 w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg transform transition-transform duration-300 hover:scale-110">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-1">Dashboard Administrativo</p>
                        <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white leading-tight">
                            ¡Bienvenido/a, <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 dark:from-blue-400 dark:via-indigo-400 dark:to-purple-400">{{ $nombreCompleto }}</span>!
                        </h2>
                    </div>
                </div>
                <div class="flex items-center gap-3 ml-26">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <p class="text-lg text-gray-600 dark:text-gray-300">
                        Tu centro de control para la gestión de matrículas y el seguimiento de pagos institucionales.
                    </p>
                </div>
            </div>
        @endif

        {{-- Banner de Guía de Pagos --}}
        <div class="mb-10 bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500 dark:from-emerald-700 dark:via-teal-700 dark:to-cyan-700 rounded-2xl shadow-2xl p-8 transform transition-all duration-300 hover:scale-[1.01] hover:shadow-emerald-500/40 border-2 border-white/20">
            <div class="flex items-center justify-between gap-6 flex-wrap">
                <div class="flex items-center gap-5 flex-1">
                    <div class="flex-shrink-0 w-16 h-16 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                        <svg class="w-9 h-9 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-white mb-1">¿Primera vez realizando un pago?</h3>
                        <p class="text-white/90 text-base">Sigue nuestra guía interactiva paso a paso para realizar un pago correctamente</p>
                    </div>
                </div>
                <button id="btnIniciarGuiaPagos" class="group relative px-8 py-4 bg-white hover:bg-gray-50 text-emerald-600 font-bold text-lg rounded-xl shadow-lg transform transition-all duration-300 hover:scale-105 hover:shadow-2xl flex items-center gap-3">
                    <svg class="w-6 h-6 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span>Iniciar Guía</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
            {{-- Tarjeta 1: Matrículas activas --}}
            <div class="relative bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl p-7 shadow-2xl text-white overflow-hidden transform transition-all duration-300 hover:scale-[1.02] hover:shadow-indigo-500/40 cursor-pointer group">
                {{-- Degradado azul-índigo fuerte, overflow-hidden para los íconos de fondo, y un grupo para efectos hover --}}
                <div class="absolute top-0 right-0 -mr-6 -mt-6 opacity-15 text-white group-hover:rotate-12 group-hover:scale-110 transition-all duration-500">
                    {{-- Icono de birrete/graduación para matrículas --}}
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                    </svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-base font-semibold opacity-90 mb-2">Matrículas activas</h3>
                    <p class="text-4xl font-extrabold mb-1">{{$totalMatriculas}}</p>
                    <div class="text-sm opacity-80">Estudiantes actualmente inscritos y con matrícula válida.</div>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-white opacity-20 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                {{-- Línea inferior que se expande al pasar el ratón, simbolizando progreso --}}
            </div>

            {{-- Tarjeta 2: Pagos del mes --}}
            <div class="relative bg-gradient-to-br from-green-500 to-emerald-600 rounded-3xl p-7 shadow-2xl text-white overflow-hidden transform transition-all duration-300 hover:scale-[1.02] hover:shadow-emerald-500/40 cursor-pointer group">
                <div class="absolute top-0 right-0 -mr-6 -mt-6 opacity-15 text-white group-hover:rotate-12 group-hover:scale-110 transition-all duration-500">
                    {{-- Icono de dinero/moneda para pagos --}}
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-base font-semibold opacity-90 mb-2">Pagos del mes</h3>
                    <p class="text-4xl font-extrabold mb-1">S/. {{ number_format($totalPagosMes, 2, ',', '.') }}</p>
                    <div class="text-sm opacity-80">Total de ingresos recibidos durante el mes actual.</div>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-white opacity-20 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
            </div>

            {{-- Tarjeta 3: Alumnos registrados --}}
            <div class="relative bg-gradient-to-br from-purple-600 to-pink-700 rounded-3xl p-7 shadow-2xl text-white overflow-hidden transform transition-all duration-300 hover:scale-[1.02] hover:shadow-pink-500/40 cursor-pointer group">
                <div class="absolute top-0 right-0 -mr-6 -mt-6 opacity-15 text-white group-hover:rotate-12 group-hover:scale-110 transition-all duration-500">
                    {{-- Icono de grupo de personas para alumnos --}}
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-base font-semibold opacity-90 mb-2">Alumnos registrados</h3>
                    <p class="text-4xl font-extrabold mb-1">{{$totalAlumnos}}</p>
                    <div class="text-sm opacity-80">Cantidad total de alumnos en la base de datos del sistema.</div>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-white opacity-20 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
            </div>

            {{-- Tarjeta 4: Deudas pendientes --}}
            <div class="relative bg-gradient-to-br from-red-600 to-orange-700 rounded-3xl p-7 shadow-2xl text-white overflow-hidden transform transition-all duration-300 hover:scale-[1.02] hover:shadow-orange-500/40 cursor-pointer group">
                <div class="absolute top-0 right-0 -mr-6 -mt-6 opacity-15 text-white group-hover:rotate-12 group-hover:scale-110 transition-all duration-500">
                    {{-- Icono de alerta/exclamación para deudas --}}
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-base font-semibold opacity-90 mb-2">Deudas pendientes</h3>
                    <p class="text-4xl font-extrabold mb-1">S/. {{ number_format($totalDeudasPendientes, 2, ',', '.') }}</p>
                    <div class="text-sm opacity-80">Monto acumulado por matrículas o cuotas aún no cubiertas.</div>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-white opacity-20 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
            </div>
        </div>
    </div>

    {{-- Overlay y Tooltip para Guía Interactiva --}}
    <div id="overlayGuia" class="hidden fixed inset-0 z-[9998]">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
    </div>

    {{-- Tooltip flotante --}}
    <div id="tooltipGuia" class="hidden fixed z-[9999] max-w-md">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border-4 border-emerald-500 overflow-hidden">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-4">
                <div class="flex items-center justify-between mb-2">
                    <span id="tooltipPaso" class="text-sm font-bold text-white uppercase tracking-wider">Paso 1 de 4</span>
                    <button id="btnCerrarGuia" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <h3 id="tooltipTitulo" class="text-xl font-bold text-white">Título del paso</h3>
                <div class="mt-3 h-1.5 bg-white/30 rounded-full overflow-hidden">
                    <div id="tooltipProgreso" class="h-full bg-white transition-all duration-500" style="width: 25%"></div>
                </div>
            </div>

            {{-- Contenido --}}
            <div class="p-6">
                <p id="tooltipDescripcion" class="text-gray-700 dark:text-gray-300 text-base mb-4 leading-relaxed">
                    Descripción del paso
                </p>
                
                {{-- Botones de navegación --}}
                <div class="flex items-center justify-between gap-3 mt-6">
                    <button id="btnAnteriorGuia" class="px-4 py-2 text-gray-600 dark:text-gray-400 font-semibold rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2" disabled>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span>Anterior</span>
                    </button>

                    <button id="btnSiguienteGuia" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold rounded-lg transition-all shadow-lg flex items-center gap-2">
                        <span>Siguiente</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Flecha apuntando al elemento --}}
        <div id="tooltipFlecha" class="absolute w-0 h-0"></div>
    </div>

    {{-- Script para la Guía Interactiva --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnIniciarGuia = document.getElementById('btnIniciarGuiaPagos');
            const overlay = document.getElementById('overlayGuia');
            const tooltip = document.getElementById('tooltipGuia');
            const btnCerrarGuia = document.getElementById('btnCerrarGuia');
            const btnAnteriorGuia = document.getElementById('btnAnteriorGuia');
            const btnSiguienteGuia = document.getElementById('btnSiguienteGuia');

            let pasoActual = 0;
            let elementoActual = null;

            // Definición de los pasos de la guía
            const pasos = [
                {
                    selector: '#menu-gestion-financiera',
                    titulo: 'Abre Gestión Financiera',
                    descripcion: 'Haz clic en el menú <strong>"Gestión Financiera"</strong> ubicado en el menú lateral izquierdo para expandir las opciones.',
                    posicion: 'right',
                    esperarClick: true
                },
                {
                    selector: '#submenu-orden-de-pago',
                    titulo: 'Selecciona Orden de Pago',
                    descripcion: 'Ahora haz clic en <strong>"Orden de Pago"</strong> para crear una nueva orden de pago.',
                    posicion: 'right',
                    esperarClick: true
                },
                // Más pasos se agregarán después
            ];

            // Iniciar guía
            if (btnIniciarGuia) {
                btnIniciarGuia.addEventListener('click', function() {
                    pasoActual = 0;
                    iniciarGuia();
                });
            }

            // Cerrar guía
            if (btnCerrarGuia) {
                btnCerrarGuia.addEventListener('click', cerrarGuia);
            }

            // Navegación
            if (btnAnteriorGuia) {
                btnAnteriorGuia.addEventListener('click', function() {
                    if (pasoActual > 0) {
                        pasoActual--;
                        mostrarPaso();
                    }
                });
            }

            if (btnSiguienteGuia) {
                btnSiguienteGuia.addEventListener('click', function() {
                    if (pasoActual < pasos.length - 1) {
                        pasoActual++;
                        mostrarPaso();
                    } else {
                        cerrarGuia();
                        alert('¡Guía completada! Ahora sabes cómo realizar un pago correctamente.');
                    }
                });
            }

            // Función para avanzar al siguiente paso
            function avanzarPaso() {
                if (pasoActual < pasos.length - 1) {
                    pasoActual++;
                    mostrarPaso();
                } else {
                    // En el último paso, dejar que navegue naturalmente
                    // La guía continuará en la siguiente página
                    cerrarGuia();
                }
            }

            function iniciarGuia() {
                // IMPORTANTE: Asegurarse de que el sidebar esté visible y expandido
                const esPantallaPequena = window.innerWidth < 1024; // lg breakpoint
                const sidebar = document.querySelector('aside.sidebar');
                
                if (!sidebar) {
                    iniciarGuiaReal();
                    return;
                }
                
                try {
                    const sidebarBtn = document.querySelector('[\\@click\\.stop="sidebarToggle = !sidebarToggle"]');
                    
                    // Verificar si el sidebar está colapsado (solo íconos en escritorio)
                    const sidebarColapsado = !esPantallaPequena && sidebar.classList.contains('lg:w-[90px]');
                    
                    // Verificar si el sidebar está oculto (móvil)
                    const sidebarOculto = sidebar.classList.contains('-translate-x-full');
                    
                    // Si está oculto en móvil, mostrarlo
                    if (sidebarOculto && esPantallaPequena && sidebarBtn) {
                        sidebarBtn.click();
                        
                        setTimeout(() => {
                            iniciarGuiaReal();
                        }, 400);
                        return;
                    }
                    
                    // Si está colapsado en escritorio, expandirlo y BLOQUEAR HOVER
                    if (sidebarColapsado && !esPantallaPequena && sidebarBtn) {
                        // Agregar clase para desactivar el hover
                        sidebar.classList.add('guia-activa-no-hover');
                        
                        // Agregar estilo inline para forzar expansión durante la guía
                        const style = document.createElement('style');
                        style.id = 'guia-sidebar-style';
                        style.textContent = `
                            .sidebar.guia-activa-no-hover {
                                width: 290px !important;
                                pointer-events: none;
                            }
                            .sidebar.guia-activa-no-hover * {
                                pointer-events: auto;
                            }
                            .sidebar.guia-activa-no-hover .logo {
                                display: block !important;
                            }
                            .sidebar.guia-activa-no-hover .logo-icon {
                                display: none !important;
                            }
                            .sidebar.guia-activa-no-hover .menu-group-title,
                            .sidebar.guia-activa-no-hover .menu-item-text,
                            .sidebar.guia-activa-no-hover .menu-item-arrow,
                            .sidebar.guia-activa-no-hover .menu-dropdown {
                                display: block !important;
                            }
                            .sidebar.guia-activa-no-hover .menu-group-icon {
                                display: none !important;
                            }
                        `;
                        document.head.appendChild(style);
                        
                        setTimeout(() => {
                            iniciarGuiaReal();
                        }, 400);
                        return;
                    }
                    
                } catch (e) {
                    console.error('Error al manipular sidebar:', e);
                }
                
                // Si no se pudo manipular el sidebar o ya está visible, continuar
                iniciarGuiaReal();
            }
            
            function iniciarGuiaReal() {
                overlay.classList.remove('hidden');
                tooltip.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                
                // Bloquear todos los clics excepto en elementos permitidos
                document.addEventListener('click', bloquearClics, true);
                
                mostrarPaso();
            }

            function cerrarGuia() {
                overlay.classList.add('hidden');
                tooltip.classList.add('hidden');
                document.body.style.overflow = 'auto';
                
                // Remover bloqueo de clics
                document.removeEventListener('click', bloquearClics, true);
                
                // IMPORTANTE: Limpiar estilos del sidebar
                const sidebar = document.querySelector('aside.sidebar');
                if (sidebar) {
                    sidebar.classList.remove('guia-activa-no-hover');
                }
                
                // Remover el style tag temporal
                const guiaStyle = document.getElementById('guia-sidebar-style');
                if (guiaStyle) {
                    guiaStyle.remove();
                }
                
                if (elementoActual) {
                    elementoActual.style.position = '';
                    elementoActual.style.zIndex = '';
                    elementoActual.style.border = '';
                    elementoActual.style.outline = '';
                    elementoActual.style.borderRadius = '';
                    elementoActual.style.pointerEvents = '';
                    elementoActual.removeEventListener('click', elementoClickHandler);
                }
            }

            // Función para bloquear todos los clics excepto en elementos permitidos
            function bloquearClics(e) {
                // Elementos permitidos para clic
                const elementosPermitidos = [
                    elementoActual,
                    btnCerrarGuia,
                    btnAnteriorGuia,
                    tooltip
                ];

                // Verificar si el clic es en un elemento permitido o sus hijos
                let permitido = false;
                for (let elem of elementosPermitidos) {
                    if (elem && (elem === e.target || elem.contains(e.target))) {
                        permitido = true;
                        break;
                    }
                }

                // Si no está permitido, bloquear el evento
                if (!permitido) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    return false;
                }
            }

            function mostrarPaso() {
                const paso = pasos[pasoActual];
                const elemento = document.querySelector(paso.selector);

                if (!elemento) {
                    console.error('Elemento no encontrado:', paso.selector);
                    return;
                }

                // Limpiar elemento anterior y remover listener
                if (elementoActual) {
                    elementoActual.style.position = '';
                    elementoActual.style.zIndex = '';
                    elementoActual.style.border = '';
                    elementoActual.style.outline = '';
                    elementoActual.style.borderRadius = '';
                    elementoActual.style.pointerEvents = '';
                    elementoActual.removeEventListener('click', elementoClickHandler);
                }

                // Destacar nuevo elemento
                elementoActual = elemento;
                const rect = elemento.getBoundingClientRect();
                
                elemento.style.position = 'relative';
                elemento.style.zIndex = '9999';
                elemento.style.border = '3px solid rgb(16, 185, 129)';
                elemento.style.outline = '3px solid rgba(16, 185, 129, 0.3)';
                elemento.style.borderRadius = '8px';
                elemento.style.pointerEvents = 'auto';

                // Scroll al elemento
                elemento.scrollIntoView({ behavior: 'smooth', block: 'center' });

                // Posicionar tooltip
                setTimeout(() => {
                    posicionarTooltip(rect, paso.posicion);
                }, 300);

                // Actualizar contenido del tooltip
                document.getElementById('tooltipPaso').textContent = `Paso ${pasoActual + 1} de ${pasos.length}`;
                document.getElementById('tooltipTitulo').textContent = paso.titulo;
                document.getElementById('tooltipDescripcion').innerHTML = paso.descripcion;
                
                // Actualizar barra de progreso
                const progreso = ((pasoActual + 1) / pasos.length) * 100;
                document.getElementById('tooltipProgreso').style.width = progreso + '%';

                // Actualizar botones
                btnAnteriorGuia.disabled = pasoActual === 0;

                // Si el paso espera click, ocultar botón siguiente y agregar listener
                if (paso.esperarClick) {
                    btnSiguienteGuia.style.display = 'none';
                    elemento.addEventListener('click', elementoClickHandler);
                } else {
                    btnSiguienteGuia.style.display = 'flex';
                    btnSiguienteGuia.querySelector('span').textContent = pasoActual === pasos.length - 1 ? 'Finalizar' : 'Siguiente';
                }
            }

            // Handler para el clic en el elemento
            function elementoClickHandler(e) {
                // Si es el último paso (Orden de Pago), guardar en sessionStorage
                if (pasoActual === pasos.length - 1) {
                    const timestamp = Date.now();
                    const guiaData = {
                        activa: true,
                        paso: 'ordenPago',
                        subpaso: 0,
                        timestamp: timestamp,
                        expira: timestamp + (5 * 60 * 1000) // 5 minutos
                    };
                    sessionStorage.setItem('guiaPagos', JSON.stringify(guiaData));
                }
                
                avanzarPaso();
            }

            function posicionarTooltip(rect, posicion) {
                const tooltipRect = tooltip.getBoundingClientRect();
                const margen = 20;
                let top, left;

                if (posicion === 'right') {
                    left = rect.right + margen;
                    top = rect.top + (rect.height / 2) - (tooltipRect.height / 2);
                } else if (posicion === 'left') {
                    left = rect.left - tooltipRect.width - margen;
                    top = rect.top + (rect.height / 2) - (tooltipRect.height / 2);
                } else if (posicion === 'bottom') {
                    left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);
                    top = rect.bottom + margen;
                } else { // top
                    left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);
                    top = rect.top - tooltipRect.height - margen;
                }

                // Ajustar si se sale de la pantalla
                if (left + tooltipRect.width > window.innerWidth) {
                    left = window.innerWidth - tooltipRect.width - 20;
                }
                if (left < 20) left = 20;
                if (top < 20) top = 20;

                tooltip.style.left = left + 'px';
                tooltip.style.top = top + 'px';
            }
        });
    </script>
@endsection


