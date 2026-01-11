@extends('base.administrativo.blank')

@section('titulo')
    Registrar una Matricula
@endsection

@section('extracss')
@endsection

@section('contenido')
    <div class="p-8 m-4 bg-gray-100 dark:bg-white/[0.03] rounded-2xl">
        <!-- Header -->
        <div class="flex pb-6 justify-between items-center border-b border-gray-200 dark:border-gray-700">
            <div>
                <h2 class="text-2xl font-bold dark:text-gray-200 text-gray-800">Nueva Matrícula</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Registra una nueva matrícula en el sistema</p>
            </div>
            <div class="flex gap-3">
                <input form="form" type="submit" id="btnCrearMatriculaTop"
                    class="cursor-pointer inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-400 disabled:border-gray-400 disabled:hover:bg-gray-400"
                    value="Crear Matrícula"
                >
                <a href="{{ $data['return'] }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    Cancelar
                </a>
            </div>
        </div>



        
        <form method="POST" id="form" action="" class="mt-8">
            @method('PUT')
            @csrf

            <!-- Búsqueda de Estudiante -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Búsqueda de Estudiante
                </h3>

                {{-- Contenedor de Filtros (Oculto por defecto) --}}
                <div id="contenedorFiltros" class="hidden mb-4 p-4 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/10 dark:to-purple-900/10 rounded-lg border border-indigo-200 dark:border-indigo-800">
                    {{-- Botones para activar cada filtro --}}
                    <div class="flex flex-wrap gap-2 mb-4">
                        <button type="button" id="btnActivarDNI"
                            class="flex items-center gap-2 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-green-50 dark:hover:bg-green-900/20 px-3 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 transition-all">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 4h10m-10 4h10"></path>
                            </svg>
                            DNI
                        </button>
                        <button type="button" id="btnActivarCodigo"
                            class="flex items-center gap-2 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 px-3 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 transition-all">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                            </svg>
                            Código Educando
                        </button>
                        <button type="button" id="btnBuscarConFiltros"
                            class="ml-auto flex items-center gap-2 rounded-lg border-2 border-blue-500 bg-blue-500 hover:bg-blue-600 px-4 py-2 text-sm font-bold text-white transition-all">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Buscar
                        </button>
                    </div>

                    {{-- Filtros individuales --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Filtro DNI --}}
                        <div id="contenedorFiltroDNI" class="hidden flex-col">
                            <label for="filtro_dni" class="text-sm font-semibold text-green-700 dark:text-green-300 mb-2 flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 4h10m-10 4h10"></path>
                                </svg>
                                DNI
                            </label>
                            <input type="text" id="filtro_dni" placeholder="Ingrese DNI..."
                                class="w-full rounded-lg border-2 border-green-300 dark:border-green-700 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90 focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm">
                        </div>

                        {{-- Filtro por Código --}}
                        <div id="contenedorFiltroCodigo" class="hidden flex-col">
                            <label for="filtro_codigo" class="text-sm font-semibold text-indigo-700 dark:text-indigo-300 mb-2 flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                </svg>
                                Código Educando
                            </label>
                            <input type="text" id="filtro_codigo" placeholder="Ingrese código..."
                                class="w-full rounded-lg border-2 border-indigo-300 dark:border-indigo-700 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                        </div>
                    </div>
                </div>

                {{-- Línea de búsqueda principal --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 items-start">
                    {{-- Campo de búsqueda por nombre con botones --}}
                    <div class="lg:col-span-8">
                        <div class="flex gap-3 items-end">
                            {{-- Campo de búsqueda --}}
                            <div class="flex-1 relative">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Buscar por Nombre
                                </label>
                                <div class="relative">
                                    <input type="text" id="buscar_alumno" autocomplete="off"
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 px-4 py-3 pl-10 text-sm text-gray-800 dark:text-white/90 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Escribe el nombre del estudiante...">
                                    <svg class="h-5 w-5 absolute left-3 top-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <div id="loading_busqueda" class="hidden absolute right-3 top-3.5">
                                        <svg class="animate-spin h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>
                                
                                {{-- Mensaje de error (posición absoluta) --}}
                                <p id="error_buscar_alumno" class="absolute left-0 -bottom-5 text-xs text-red-500 dark:text-red-400 hidden"></p>
                                
                                {{-- Dropdown de resultados --}}
                                <div id="resultados_busqueda" class="hidden absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg shadow-lg max-h-96 overflow-y-auto">
                                    {{-- Los resultados se insertarán aquí dinámicamente --}}
                                </div>
                            </div>

                            {{-- Botones Filtros y Limpiar --}}
                            <div class="flex gap-2 flex-shrink-0">
                                <button type="button" id="btnActivarFiltros"
                                    class="flex items-center justify-center gap-2 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:border-indigo-500 dark:hover:border-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 px-4 h-[46px] text-sm font-semibold text-gray-700 dark:text-gray-300 transition-all duration-200">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                    </svg>
                                    <span>Filtros</span>
                                </button>

                                <button type="button" id="btnLimpiarBusqueda"
                                    class="flex items-center justify-center gap-2 rounded-lg border-2 border-blue-300 bg-blue-100 hover:bg-blue-200 dark:border-blue-700 dark:bg-blue-900 dark:hover:bg-blue-800 px-4 h-[46px] text-sm font-semibold text-gray-800 dark:text-blue-100 transition-colors">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span>Limpiar</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Año Escolar (campo fijo al año actual) --}}
                    <div class="lg:col-span-4 flex justify-end">
                        <div class="flex flex-col">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Año Escolar
                            </label>
                            <input type="text" 
                                value="{{ date('Y') }}" 
                                readonly
                                class="w-40 h-[46px] rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 px-4 text-sm text-gray-800 dark:text-white/90 font-medium text-center">
                            <input type="hidden" 
                                name="año_escolar" 
                                id="año_escolar" 
                                value="{{ date('Y') }}">
                        </div>
                    </div>
                </div>

                {{-- Campo oculto para ID del alumno --}}
                <input type="hidden" id="alumno" name="alumno" value="{{ old('alumno') }}">
                
                {{-- Mensaje de advertencia para alumnos ya matriculados --}}
                <div id="mensajeAlumnoMatriculado" class="hidden mt-4 p-4 bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 rounded-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-amber-800 dark:text-amber-200 mb-1">Alumno Ya Matriculado</h4>
                            <p class="text-sm text-amber-700 dark:text-amber-300">
                                Este alumno ya tiene una matrícula activa en el sistema. No se puede crear una nueva matrícula hasta que la actual sea dada de baja.
                            </p>
                            <p class="text-xs text-amber-600 dark:text-amber-400 mt-2" id="detalleMatriculaActual"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Estudiante -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Información del Estudiante
                </h3>
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        {{-- Foto del estudiante --}}
                        <div class="lg:col-span-3 flex flex-col items-center">
                            <div class="relative">
                                <div id="foto_alumno_container" class="w-32 h-32 rounded-full border-4 border-gray-200 dark:border-gray-600 overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center shadow-md">
                                    {{-- Avatar por defecto (se actualizará con JS) --}}
                                    <svg class="w-20 h-20 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="absolute bottom-0 right-0 w-8 h-8 bg-blue-500 rounded-full border-2 border-white dark:border-gray-800 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="mt-3 text-xs text-center text-gray-500 dark:text-gray-400">Fotografía del estudiante</p>
                        </div>
                        
                        {{-- Datos del estudiante --}}
                        <div class="lg:col-span-9">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                        Alumno Seleccionado
                                    </label>
                                    <input type="text" id="nombre_alumno_display" readonly
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90"
                                        placeholder="Busque un alumno usando los filtros arriba">
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                        DNI
                                    </label>
                                    <input type="text" id="dni_alumno_display" readonly
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90"
                                        placeholder="-">
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                        Código de Educando
                                    </label>
                                    <input type="text" id="codigo_alumno_display" readonly
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90"
                                        placeholder="-">
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                        Año de Ingreso
                                    </label>
                                    <input type="text" id="anio_ingreso_alumno_display" readonly
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90"
                                        placeholder="-">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Académica -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Información Académica
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @include('components.forms.combo_dependient', [
                        'label' => 'Nivel Educativo',
                        'name' => 'nivel_educativo',
                        'error' => $errors->first(Str::snake('Nivel Educativo')) ?? false,
                        'placeholder' => 'Seleccionar nivel educativo...',
                        'value' => old(Str::snake('Nivel Educativo'), $data['nivelSeleccionado'] ?? null ),
                        'value_field' => 'id_nivel',
                        'text_field' => 'nombre_nivel',
                        'options' => $data['niveles'],
                        'enabled' => true,
                        'disableSearch' => true
                    ])

                    @include('components.forms.combo_dependient', [
                        'label' => 'Grado',
                        'name' => 'grado',
                        'error' => $errors->first(Str::snake('Grado')) ?? false,
                        'placeholder' => 'Seleccionar grado...',
                        'depends_on' => 'nivel_educativo',
                        'parent_field' => 'id_nivel',
                        'value' => old(Str::snake('Grado'), $data['gradoSeleccionado'] ?? null),
                        'value_field' => 'id_grado',
                        'text_field' => 'nombre_grado',
                        'options' => $data['grados'],
                        'enabled' => false,
                        'disableSearch' => true
                    ])

                    @include('components.forms.combo_dependient', [
                        'label' => 'Sección',
                        'name' => 'seccion',
                        'error' => $errors->first(Str::snake('Seccion')) ?? false,
                        'value' => old(Str::snake('Seccion'), $data['seccionSeleccionada'] ?? null),
                        'placeholder' => 'Seleccionar sección...',
                        'depends_on' => 'grado',
                        'parent_field' => 'id_grado',
                        'value_field' => ['id_grado', 'nombreSeccion'],
                        'text_field' => 'nombreSeccion',
                        'options' => $data['secciones'],
                        'enabled' => false,
                        'disableSearch' => true
                    ])
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Información Adicional
                </h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        @include('components.forms.text-area', [
                            'label' => 'Observaciones',
                            'name' => 'observaciones',
                            'error' => $errors->first(Str::snake('Observaciones')) ?? false,
                            'value' => old(Str::snake('Observaciones'))
                        ])
                    </div>
                    <div>
                        <!-- Info Box del Alumno -->
                        <div class="h-full">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Información Financiera
                            </label>
                            <div id="infoBox" class="h-full min-h-[120px] flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-800/50 text-gray-600 dark:text-gray-400 text-sm transition-all duration-300 ease-in-out">
                                <div class="text-center">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p>Selecciona un alumno para ver detalles financieros</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campo oculto -->
            <input type="hidden" name="escala" id="escalaInput" value="">

            <!-- Botones de acción -->
            <div class="flex justify-end gap-3 pt-6">
                <a href="{{ $data['return'] }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancelar
                </a>
                <input form="form" type="submit" id="btnCrearMatriculaBottom"
                    class="cursor-pointer inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-400 disabled:border-gray-400 disabled:hover:bg-gray-400"
                    value="Crear Matrícula"
                >
            </div>
        </form>
    </div>
@endsection

@section('custom-js')
    <script>
    // ========== FUNCIONALIDAD DE BÚSQUEDA Y FILTROS ==========
    document.addEventListener('DOMContentLoaded', function () {
        const btnActivarFiltros = document.getElementById('btnActivarFiltros');
        const contenedorFiltros = document.getElementById('contenedorFiltros');
        const btnActivarDNI = document.getElementById('btnActivarDNI');
        const btnActivarCodigo = document.getElementById('btnActivarCodigo');
        const contenedorFiltroDNI = document.getElementById('contenedorFiltroDNI');
        const contenedorFiltroCodigo = document.getElementById('contenedorFiltroCodigo');
        const btnBuscarConFiltros = document.getElementById('btnBuscarConFiltros');
        const btnLimpiarBusqueda = document.getElementById('btnLimpiarBusqueda');
        
        const buscarAlumnoInput = document.getElementById('buscar_alumno');
        const filtroDNI = document.getElementById('filtro_dni');
        const filtroCodigo = document.getElementById('filtro_codigo');
        const resultadosBusqueda = document.getElementById('resultados_busqueda');
        const loadingBusqueda = document.getElementById('loading_busqueda');
        const errorBuscarAlumno = document.getElementById('error_buscar_alumno');
        
        const alumnoHidden = document.getElementById('alumno');
        const nombreAlumnoDisplay = document.getElementById('nombre_alumno_display');
        const dniAlumnoDisplay = document.getElementById('dni_alumno_display');
        
        let timeoutBusqueda = null;
        let ultimosResultados = []; // Almacenar los últimos resultados
        
        // Toggle contenedor de filtros
        if (btnActivarFiltros) {
            btnActivarFiltros.addEventListener('click', function() {
                contenedorFiltros.classList.toggle('hidden');
            });
        }
        
        // Activar/desactivar filtros individuales
        if (btnActivarDNI) {
            btnActivarDNI.addEventListener('click', function() {
                contenedorFiltroDNI.classList.toggle('hidden');
                if (!contenedorFiltroDNI.classList.contains('hidden')) {
                    contenedorFiltroDNI.classList.remove('flex-col');
                    contenedorFiltroDNI.classList.add('flex', 'flex-col');
                    btnActivarDNI.classList.add('border-green-500', 'bg-green-50', 'dark:bg-green-900/30');
                } else {
                    btnActivarDNI.classList.remove('border-green-500', 'bg-green-50', 'dark:bg-green-900/30');
                }
            });
        }
        
        if (btnActivarCodigo) {
            btnActivarCodigo.addEventListener('click', function() {
                contenedorFiltroCodigo.classList.toggle('hidden');
                if (!contenedorFiltroCodigo.classList.contains('hidden')) {
                    contenedorFiltroCodigo.classList.remove('flex-col');
                    contenedorFiltroCodigo.classList.add('flex', 'flex-col');
                    btnActivarCodigo.classList.add('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/30');
                } else {
                    btnActivarCodigo.classList.remove('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/30');
                }
            });
        }
        
        // Función para realizar la búsqueda
        function realizarBusqueda() {
            const nombre = buscarAlumnoInput.value.trim();
            const dni = filtroDNI.value.trim();
            const codigo = filtroCodigo.value.trim();
            
            // Validar que al menos haya un criterio de búsqueda
            if (!nombre && !dni && !codigo) {
                errorBuscarAlumno.textContent = 'Ingrese al menos un criterio de búsqueda';
                errorBuscarAlumno.classList.remove('hidden');
                resultadosBusqueda.classList.add('hidden');
                return;
            }
            
            errorBuscarAlumno.classList.add('hidden');
            loadingBusqueda.classList.remove('hidden');
            resultadosBusqueda.classList.add('hidden');
            
            // Priorizar: si hay DNI, buscar por DNI; si hay código, buscar por código; sino por nombre
            const termino = dni || codigo || nombre;
            
            fetch(`/orden-pago/buscar-alumnos-nombre?termino=${encodeURIComponent(termino)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.json();
                })
                .then(data => {
                    loadingBusqueda.classList.add('hidden');
                    
                    console.log('Respuesta del servidor:', data);
                    
                    if (data.success && data.alumnos && data.alumnos.length > 0) {
                        mostrarResultados(data.alumnos);
                    } else {
                        errorBuscarAlumno.innerHTML = `
                            ${data.message || 'No se encontraron estudiantes con los criterios especificados'}. 
                            <a href="{{ route('alumno_create') }}" class="text-blue-600 dark:text-blue-400 hover:underline font-semibold">
                                ¿Desea crear un nuevo alumno?
                            </a>
                        `;
                        errorBuscarAlumno.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    loadingBusqueda.classList.add('hidden');
                    errorBuscarAlumno.innerHTML = `
                        Error al buscar estudiantes. Intente nuevamente. 
                        <a href="{{ route('alumno_create') }}" class="text-blue-600 dark:text-blue-400 hover:underline font-semibold">
                            ¿Desea crear un nuevo alumno?
                        </a>
                    `;
                    errorBuscarAlumno.classList.remove('hidden');
                });
        }
        
        // Función para mostrar resultados
        function mostrarResultados(alumnos) {
            resultadosBusqueda.innerHTML = '';
            ultimosResultados = alumnos; // Guardar los resultados
            
            alumnos.forEach(alumno => {
                const div = document.createElement('div');
                div.className = 'p-4 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer transition-colors border-b border-gray-200 dark:border-gray-600 last:border-b-0';
                div.innerHTML = `
                    <div class="flex justify-between items-start gap-3">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 dark:text-white text-sm">${alumno.nombre_completo}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1.5">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 4h10m-10 4h10"></path>
                                    </svg>
                                    DNI: ${alumno.dni || 'N/A'}
                                </span>
                                <span class="mx-1">•</span>
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                    </svg>
                                    Código: ${alumno.codigo_educando || 'N/A'}
                                </span>
                            </p>
                            <p class="text-xs text-blue-600 dark:text-blue-400 mt-1 font-medium">${alumno.grado || ''} - ${alumno.seccion || ''}</p>
                        </div>
                        <span class="flex-shrink-0 text-xs bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-md font-medium transition-colors whitespace-nowrap">
                            Seleccionar
                        </span>
                    </div>
                `;
                
                div.addEventListener('click', function() {
                    seleccionarAlumno(alumno);
                });
                
                resultadosBusqueda.appendChild(div);
            });
            
            resultadosBusqueda.classList.remove('hidden');
        }
        
        // Función para seleccionar un alumno
        function seleccionarAlumno(alumno) {
            alumnoHidden.value = alumno.id_alumno;
            nombreAlumnoDisplay.value = alumno.nombre_completo;
            dniAlumnoDisplay.value = alumno.dni || 'N/A';
            
            // Actualizar nuevos campos
            const codigoAlumnoDisplay = document.getElementById('codigo_alumno_display');
            const anioIngresoAlumnoDisplay = document.getElementById('anio_ingreso_alumno_display');
            const fotoContainer = document.getElementById('foto_alumno_container');
            
            if (codigoAlumnoDisplay) {
                codigoAlumnoDisplay.value = alumno.codigo_educando || 'N/A';
            }
            
            if (anioIngresoAlumnoDisplay) {
                anioIngresoAlumnoDisplay.value = alumno.anio_ingreso || 'N/A';
            }
            
            // Actualizar foto del alumno
            if (fotoContainer) {
                if (alumno.foto && alumno.foto !== '') {
                    // Si tiene foto, mostrarla
                    fotoContainer.innerHTML = `<img src="${alumno.foto}" alt="Foto de ${alumno.nombre_completo}" class="w-full h-full object-cover">`;
                } else {
                    // Mostrar avatar según sexo
                    const sexo = alumno.sexo || 'M';
                    if (sexo === 'F') {
                        // Avatar femenino
                        fotoContainer.innerHTML = `
                            <svg class="w-20 h-20 text-pink-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                        `;
                    } else {
                        // Avatar masculino
                        fotoContainer.innerHTML = `
                            <svg class="w-20 h-20 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                        `;
                    }
                }
            }
            
            resultadosBusqueda.classList.add('hidden');
            buscarAlumnoInput.value = alumno.nombre_completo;
            
            // Verificar si el alumno ya está matriculado
            const mensajeMatriculado = document.getElementById('mensajeAlumnoMatriculado');
            const detalleMatricula = document.getElementById('detalleMatriculaActual');
            const btnTop = document.getElementById('btnCrearMatriculaTop');
            const btnBottom = document.getElementById('btnCrearMatriculaBottom');
            
            if (alumno.id_matricula) {
                // Alumno ya matriculado - mostrar advertencia y deshabilitar botones
                if (mensajeMatriculado) {
                    mensajeMatriculado.classList.remove('hidden');
                    if (detalleMatricula) {
                        detalleMatricula.textContent = `Matrícula actual: ${alumno.grado} - ${alumno.seccion}`;
                    }
                }
                if (btnTop) btnTop.disabled = true;
                if (btnBottom) btnBottom.disabled = true;
            } else {
                // Alumno sin matrícula - ocultar advertencia y habilitar botones
                if (mensajeMatriculado) {
                    mensajeMatriculado.classList.add('hidden');
                }
                if (btnTop) btnTop.disabled = false;
                if (btnBottom) btnBottom.disabled = false;
            }
            
            // Cargar información financiera
            cargarInfoFinanciera(alumno.id_alumno);
        }
        
        // Búsqueda por nombre con debounce
        if (buscarAlumnoInput) {
            buscarAlumnoInput.addEventListener('input', function() {
                clearTimeout(timeoutBusqueda);
                
                const nombre = this.value.trim();
                if (nombre.length >= 3) {
                    timeoutBusqueda = setTimeout(realizarBusqueda, 500);
                } else {
                    resultadosBusqueda.classList.add('hidden');
                    errorBuscarAlumno.classList.add('hidden');
                }
            });
            
            // Mostrar resultados previos al hacer focus
            buscarAlumnoInput.addEventListener('focus', function() {
                const nombre = this.value.trim();
                // Si hay texto y hay resultados previos, mostrarlos
                if (nombre.length >= 3 && ultimosResultados.length > 0) {
                    mostrarResultados(ultimosResultados);
                }
            });
        }
        
        // Botón buscar con filtros
        if (btnBuscarConFiltros) {
            btnBuscarConFiltros.addEventListener('click', realizarBusqueda);
        }
        
        // Botón limpiar
        if (btnLimpiarBusqueda) {
            btnLimpiarBusqueda.addEventListener('click', function() {
                buscarAlumnoInput.value = '';
                filtroDNI.value = '';
                filtroCodigo.value = '';
                alumnoHidden.value = '';
                nombreAlumnoDisplay.value = '';
                dniAlumnoDisplay.value = '';
                
                // Limpiar nuevos campos
                const codigoAlumnoDisplay = document.getElementById('codigo_alumno_display');
                const anioIngresoAlumnoDisplay = document.getElementById('anio_ingreso_alumno_display');
                const fotoContainer = document.getElementById('foto_alumno_container');
                
                if (codigoAlumnoDisplay) codigoAlumnoDisplay.value = '';
                if (anioIngresoAlumnoDisplay) anioIngresoAlumnoDisplay.value = '';
                
                // Resetear foto al avatar por defecto
                if (fotoContainer) {
                    fotoContainer.innerHTML = `
                        <svg class="w-20 h-20 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                    `;
                }
                
                // Ocultar mensaje de advertencia y habilitar botones
                const mensajeMatriculado = document.getElementById('mensajeAlumnoMatriculado');
                const btnTop = document.getElementById('btnCrearMatriculaTop');
                const btnBottom = document.getElementById('btnCrearMatriculaBottom');
                
                if (mensajeMatriculado) {
                    mensajeMatriculado.classList.add('hidden');
                }
                if (btnTop) btnTop.disabled = false;
                if (btnBottom) btnBottom.disabled = false;
                
                resultadosBusqueda.classList.add('hidden');
                errorBuscarAlumno.classList.add('hidden');
                ultimosResultados = []; // Limpiar los resultados almacenados
                
                // Limpiar info box
                const infoBox = document.getElementById('infoBox');
                if (infoBox) {
                    infoBox.innerHTML = `
                        <div class="flex items-center justify-center h-full p-6">
                            <div class="text-center">
                                <svg class="w-10 h-10 mx-auto mb-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Selecciona un alumno para ver</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">la información financiera</p>
                            </div>
                        </div>
                    `;
                }
            });
        }
        
        // Cerrar resultados al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!buscarAlumnoInput.contains(e.target) && !resultadosBusqueda.contains(e.target)) {
                resultadosBusqueda.classList.add('hidden');
            }
        });
    });

    // ========== FUNCIONALIDAD ORIGINAL DE GRADO Y SECCIÓN ==========
    document.addEventListener('DOMContentLoaded', function () {
        // Los IDs correctos que genera el componente combo_dependient
        const gradoInput = document.getElementById('combo_grado_hidden');
        const seccionContainer = document.querySelector('[data-field-name="seccion"]');
        const seccionButton = document.getElementById('combo_seccion');
        
        console.log('Grado hidden input:', gradoInput);
        console.log('Seccion container:', seccionContainer);
        console.log('Seccion button:', seccionButton);
        
        if (gradoInput && seccionContainer) {
            // Escuchar cambios en el input hidden de grado
            gradoInput.addEventListener('change', function () {
                const gradoValue = this.value;
                console.log('Grado changed to:', gradoValue);
                
                // Verificar cuántas opciones de sección están disponibles para este grado
                const seccionOptions = seccionContainer.querySelectorAll('.combo-option');
                let visibleCount = 0;
                
                seccionOptions.forEach(option => {
                    const parentValue = option.dataset.parent;
                    console.log('Seccion option:', option.dataset.text, 'Parent:', parentValue, 'Should show:', parentValue === gradoValue);
                    
                    if (parentValue === gradoValue) {
                        visibleCount++;
                    }
                });
                
                console.log('Total secciones visibles para grado', gradoValue, ':', visibleCount);
                
                if (visibleCount === 0) {
                    console.warn('⚠️ No hay secciones disponibles para el grado seleccionado');
                }
            });
        }
    });

    // ========== FUNCIONALIDAD DE INFORMACIÓN FINANCIERA ==========
    function cargarInfoFinanciera(alumnoId) {
        const infoBox = document.getElementById('infoBox');
        
        if (!infoBox || !alumnoId) return;
        
        infoBox.innerHTML = `
            <div class="text-center">
                <svg class="w-6 h-6 mx-auto mb-2 text-blue-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <p>Cargando información...</p>
            </div>
        `;
        
        fetch(`/matriculas/api/alumnos/${alumnoId}/info`)
            .then(response => {
                if (!response.ok) throw new Error("Error en la respuesta.");
                return response.json();
            })
            .then(data => {
                infoBox.innerHTML = `
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Escala:</span>
                            <span class="text-gray-900 dark:text-white">${data.escala ?? 'No registrada'}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Deuda mensual:</span>
                            <span class="text-gray-900 dark:text-white">S/ ${data.deuda_mensual}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Cuotas pendientes:</span>
                            <span class="text-gray-900 dark:text-white">${data.cuotas_pendientes}</span>
                        </div>
                        <div class="flex justify-between items-center border-t border-gray-200 dark:border-gray-600 pt-2">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">Deuda total:</span>
                            <span class="font-semibold text-red-600 dark:text-red-400">S/ ${data.deuda_total}</span>
                        </div>
                    </div>
                `;
                
                // Actualizar el input hidden
                const escalaInput = document.getElementById("escalaInput");
                if (escalaInput) {
                    escalaInput.value = data.escala;
                }
            })
            .catch(error => {
                console.error(error);
                infoBox.innerHTML = `
                    <div class="text-center text-red-500">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p>No se pudo cargar la información</p>
                    </div>
                `;
            });
    }
    </script>
@endsection
