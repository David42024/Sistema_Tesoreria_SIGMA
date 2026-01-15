<div class="p-8 m-4 bg-gray-100 dark:bg-white/[0.03] rounded-2xl" id="view-container">
    <!-- Mensaje de éxito -->
    @if(session('success'))
        <div id="success-message" class="mb-6 rounded-lg bg-green-50 p-4 text-sm text-green-800 dark:bg-green-900/20 dark:text-green-400 border border-green-200 dark:border-green-800">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Header -->
    <div class="flex pb-6 justify-between items-center border-b border-gray-200 dark:border-gray-700">
        <div>
            <h2 class="text-2xl font-bold dark:text-gray-200 text-gray-800">Datos del Estudiante</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">ID: {{ $data['id'] }}</p>
        </div>
        <div class="flex gap-3">
            <button onclick="toggleEditMode()" id="btn-editar"
                class="inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Editar
            </button>
            <a href="{{ $data['return'] }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
            >
                Volver
            </a>
        </div>
    </div>

    <form method="POST" id="form" action="">
        @method('PATCH')
        @csrf

        <!-- Tabs Navigation -->
        <div class="border-b border-gray-200 dark:border-gray-700 mb-6 relative">
            <!-- Barra deslizante animada -->
            <div id="tab-slider" class="absolute bottom-0 h-0.5 bg-gradient-to-r from-blue-400 to-blue-600 transition-all duration-500 ease-out shadow-lg shadow-blue-500/50" style="width: 0; left: 0;"></div>
            
            <nav class="-mb-px flex space-x-8 overflow-x-auto relative" aria-label="Tabs">
                <button type="button" onclick="switchTab('basica')" 
                    class="tab-button whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium border-blue-500 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors" 
                    data-tab="basica">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 4h10m-10 4h10"></path>
                        </svg>
                        Información Básica
                    </span>
                </button>
                <button type="button" onclick="switchTab('personal')" 
                    class="tab-button whitespace-nowrap border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300" 
                    data-tab="personal">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Información Personal
                    </span>
                </button>
                <button type="button" onclick="switchTab('ubicacion')" 
                    class="tab-button whitespace-nowrap border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300" 
                    data-tab="ubicacion">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Ubicación
                    </span>
                </button>
                <button type="button" onclick="switchTab('vivienda')" 
                    class="tab-button whitespace-nowrap border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300" 
                    data-tab="vivienda">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Vivienda
                    </span>
                </button>
                <button type="button" onclick="switchTab('educativa')" 
                    class="tab-button whitespace-nowrap border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300" 
                    data-tab="educativa">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        Cultural y Educativa
                    </span>
                </button>
            </nav>
        </div>

        <!-- Tab Contents -->
        <div class="tab-content">
            <!-- Tab 1: Información Básica y Foto -->
            <div id="tab-basica" class="tab-pane">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Columna de Foto -->
                    <div class="lg:col-span-1">
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-4">Fotografía del Estudiante</h3>
                            
                            <div class="flex flex-col items-center">
                                <div class="relative w-60 h-72 mb-4 bg-white dark:bg-gray-900 border-2 border-gray-300 dark:border-gray-600 rounded-lg flex items-center justify-center">
                                    @if($data['alumno']->foto)
                                        <img
                                            src="{{ $data['foto_url'] }}"
                                            alt="Foto del estudiante"
                                            class="w-full h-full object-cover rounded-lg"
                                        >
                                    @else
                                        {{-- Avatar por defecto según sexo --}}
                                        @if(($data['default']['sexo'] ?? 'M') === 'F')
                                            <svg class="w-32 h-32 text-pink-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                            </svg>
                                        @else
                                            <svg class="w-32 h-32 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                    @endif
                                </div>

                                <!-- Información del estudiante -->
                                <div class="w-full space-y-2">
                                    <div class="text-center">
                                        <h4 class="text-lg font-bold text-gray-800 dark:text-white">
                                            {{ $data['default']['primer_nombre'] }}
                                            {{ $data['default']['otros_nombres'] }}
                                        </h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-300 font-medium">
                                            {{ $data['default']['apellido_paterno'] }}
                                            {{ $data['default']['apellido_materno'] }}
                                        </p>
                                    </div>
                                    <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 flex justify-between">
                                            <span class="font-medium">DNI:</span>
                                            <span class="text-gray-800 dark:text-gray-200 font-semibold">{{ $data['default']['d_n_i'] }}</span>
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 flex justify-between mt-1">
                                            <span class="font-medium">Código:</span>
                                            <span class="text-gray-800 dark:text-gray-200 font-semibold">{{ $data['default']['codigo_educando'] }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna de Datos Básicos -->
                    <div class="lg:col-span-2">
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-6">Datos Básicos</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @include('components.forms.string-ineditable-real', [
                                    'label' => 'Código Educando',
                                    'name' => Str::snake('Codigo Educando'),
                                    'value' => $data['default']['codigo_educando']
                                ])
                                @include('components.forms.string-ineditable-real', [
                                    'label' => 'Código Modular',
                                    'name' => Str::snake('Codigo Modular'),
                                    'value' => $data['default']['codigo_modular']
                                ])
                                @include('components.forms.string-ineditable-real', [
                                    'label' => 'Año de Ingreso',
                                    'name' => Str::snake('Año de Ingreso'),
                                    'value' => $data['default']['año_ingreso']
                                ])
                                @include('components.forms.string-ineditable-real', [
                                    'label' => 'DNI',
                                    'name' => Str::snake('DNI'),
                                    'value' => $data['default']['d_n_i']
                                ])
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="button" onclick="switchTab('personal')" 
                        class="inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600 dark:border-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700">
                        Siguiente
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Tab 2: Información Personal -->
            <div id="tab-personal" class="tab-pane hidden">
                <!-- Sección 1: Datos de Identidad -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 4h10m-10 4h10"></path>
                        </svg>
                        Datos de Identidad
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Apellido Paterno',
                            'name' => Str::snake('Apellido Paterno'),
                            'value' => $data['default']['apellido_paterno']
                        ])
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Apellido Materno',
                            'name' => Str::snake('Apellido Materno'),
                            'value' => $data['default']['apellido_materno']
                        ])
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Primer Nombre',
                            'name' => Str::snake('Primer Nombre'),
                            'value' => $data['default']['primer_nombre']
                        ])
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Otros Nombres',
                            'name' => Str::snake('Otros Nombres'),
                            'value' => $data['default']['otros_nombres']
                        ])
                    </div>
                </div>

                <!-- Sección 2: Datos Personales -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Datos Personales
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Sexo',
                            'name' => Str::snake('Sexo'),
                            'value' => $data['default'][Str::snake('Sexo')],
                            'options' => $data['sexos'],
                            'options_attributes' => ['id', 'descripcion']
                        ])
                        @include('components.forms.date-readonly', [
                            'label' => 'Fecha Nacimiento',
                            'name' => Str::snake('Fecha Nacimiento'),
                            'value' => $data['default']['fecha_nacimiento'],
                            'error' => false
                        ])
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Teléfono',
                            'name' => Str::snake('Telefono'),
                            'value' => $data['default']['telefono']
                        ])
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Escala',
                            'name' => Str::snake('Escala'),
                            'value' => $data['default'][Str::snake('Escala')],
                            'options' => $data['escalas'],
                            'options_attributes' => ['id', 'descripcion']
                        ])
                    </div>
                </div>

                <!-- Sección 3: Datos Culturales y Religiosos -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        Datos Culturales y Religiosos
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Lengua Materna',
                            'name' => Str::snake('Lengua Materna'),
                            'value' => $data['default'][Str::snake('Lengua Materna')],
                            'options' => $data['lenguasmaternas'],
                            'options_attributes' => ['id', 'descripcion']
                        ])
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Estado Civil',
                            'name' => Str::snake('Estado Civil'),
                            'value' => $data['default'][Str::snake('Estado Civil')],
                            'options' => $data['estadosciviles'],
                            'options_attributes' => ['id', 'descripcion']
                        ])
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Religión',
                            'name' => Str::snake('Religion'),
                            'value' => $data['default']['religion']
                        ])
                        @include('components.forms.date-readonly', [
                            'label' => 'Fecha Bautizo',
                            'name' => Str::snake('Fecha Bautizo'),
                            'value' => $data['default']['fecha_bautizo'],
                            'error' => false
                        ])
                    </div>
                </div>

                <div class="flex justify-between mt-6">
                    <button type="button" onclick="switchTab('basica')" 
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Anterior
                    </button>
                    <button type="button" onclick="switchTab('ubicacion')" 
                        class="inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600 dark:border-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700">
                        Siguiente
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Tab 3: Ubicación -->
            <div id="tab-ubicacion" class="tab-pane hidden">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-6">Ubicación Geográfica y Transporte</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'País',
                            'name' => 'pais',
                            'value' => $data['default'][Str::snake('pais')],
                            'value_field' => 'id_pais',
                            'text_field' => 'descripcion',
                            'options' => $data['paises'],
                        ])
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Departamento',
                            'name' => 'departamento',
                            'value' => collect($data['departamentos'])->firstWhere('id', $data['default'][Str::snake('departamento')])->descripcion ?? $data['default'][Str::snake('departamento')]
                        ])
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Provincia',
                            'name' => 'provincia',
                            'value' => collect($data['provincias'])->firstWhere('id', $data['default'][Str::snake('provincia')])->descripcion ?? $data['default'][Str::snake('provincia')]
                        ])
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Distrito',
                            'name' => 'distrito',
                            'value' => collect($data['distritos'])->firstWhere('id', $data['default'][Str::snake('distrito')])->descripcion ?? $data['default'][Str::snake('distrito')]
                        ])
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                        <div class="md:col-span-2 lg:col-span-1">
                            @include('components.forms.string-ineditable-real', [
                                'label' => 'Dirección',
                                'name' => Str::snake('Direccion'),
                                'value' => $data['default']['direccion']
                            ])
                        </div>
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Medio de Transporte',
                            'name' => Str::snake('Medio de Transporte'),
                            'value' => $data['default']['medio_de_transporte']
                        ])
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Tiempo de Demora',
                            'name' => Str::snake('Tiempo de demora'),
                            'value' => $data['default']['tiempo_de_demora']
                        ])
                    </div>
                </div>

                <div class="flex justify-between mt-6">
                    <button type="button" onclick="switchTab('personal')" 
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Anterior
                    </button>
                    <button type="button" onclick="switchTab('vivienda')" 
                        class="inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600 dark:border-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700">
                        Siguiente
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Tab 4: Vivienda -->
            <div id="tab-vivienda" class="tab-pane hidden">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-6">Información de Vivienda</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Material Vivienda',
                            'name' => Str::snake('Material Vivienda'),
                            'value' => $data['default']['material_vivienda']
                        ])
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Energía Eléctrica',
                            'name' => Str::snake('Energia Electrica'),
                            'value' => $data['default']['energia_electrica']
                        ])
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Agua Potable',
                            'name' => Str::snake('Agua Potable'),
                            'value' => $data['default']['agua_potable']
                        ])
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Desagüe',
                            'name' => Str::snake('Desague'),
                            'value' => $data['default']['desague']
                        ])
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'SS.HH.',
                            'name' => Str::snake('SS_HH'),
                            'value' => $data['default']['s_s__h_h']
                        ])
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Número de Habitaciones',
                            'name' => Str::snake('Numero de Habitaciones'),
                            'value' => $data['default']['numero_de_habitaciones']
                        ])
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Número de Habitantes',
                            'name' => Str::snake('Numero de Habitantes'),
                            'value' => $data['default']['numero_de_habitantes']
                        ])
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Situación de Vivienda',
                            'name' => Str::snake('Situacion de vivienda'),
                            'value' => $data['default']['situacion_de_vivienda']
                        ])
                    </div>
                </div>

                <div class="flex justify-between mt-6">
                    <button type="button" onclick="switchTab('ubicacion')" 
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Anterior
                    </button>
                    <button type="button" onclick="switchTab('educativa')" 
                        class="inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600 dark:border-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700">
                        Siguiente
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Tab 5: Cultural y Educativa -->
            <div id="tab-educativa" class="tab-pane hidden">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-6">Información Cultural y Educativa</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Parroquia de Bautizo',
                            'name' => Str::snake('Parroquia de Bautizo'),
                            'value' => $data['default']['parroquia_de_bautizo']
                        ])
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Colegio de Procedencia',
                            'name' => Str::snake('Colegio de Procedencia'),
                            'value' => $data['default']['colegio_de_procedencia']
                        ])
                    </div>
                </div>

                <div class="flex justify-start gap-3 mt-6">
                    <button type="button" onclick="switchTab('vivienda')" 
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Anterior
                    </button>
                </div>
            </div>
        </div>

        <!-- Botones de acción fijos al final -->
        <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ $data['return'] }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Volver
            </a>
            <button onclick="toggleEditMode()" type="button"
                class="inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Editar Datos
            </button>
        </div>
    </form>
</div>

<!-- Vista de Edición (Oculta por defecto) -->
<div id="edit-container" class="hidden">
    @php
        // Preparar datos para la vista de edición
        $editData = $data;
        $editData['alumno'] = (object) [
            'foto' => $data['alumno']->foto ?? null,
            'foto_url' => $data['foto_url'] ?? '',
            'codigo_educando' => $data['default']['codigo_educando'] ?? '',
            'codigo_modular' => $data['default']['codigo_modular'] ?? '',
            'año_ingreso' => $data['default']['año_ingreso'] ?? '',
            'd_n_i' => $data['default']['d_n_i'] ?? '',
            'primer_nombre' => $data['default']['primer_nombre'] ?? '',
            'apellido_paterno' => $data['default']['apellido_paterno'] ?? '',
            'apellido_materno' => $data['default']['apellido_materno'] ?? '',
            'otros_nombres' => $data['default']['otros_nombres'] ?? '',
            'fecha_nacimiento' => $data['default']['fecha_de_nacimiento'] ?? '',
            'sexo' => $data['default']['sexo'] ?? '',
            'escala' => $data['default']['escala'] ?? 'A',
            'lugar_nacimiento' => $data['default']['lugar_de_nacimiento'] ?? '',
            'estado_civil' => $data['default']['estado_civil'] ?? '',
            'talla' => $data['default']['talla'] ?? '',
            'peso' => $data['default']['peso'] ?? '',
            'fecha_bautizo' => $data['default']['fecha_de_bautizo'] ?? '',
            'parroquia_bautizo' => $data['default']['parroquia_de_bautizo'] ?? '',
            'religion' => $data['default']['religion'] ?? '',
            'sangre' => $data['default']['sangre'] ?? '',
            'enfermedad' => $data['default']['enfermedad'] ?? '',
            'alergia' => $data['default']['alergia'] ?? '',
            'departamento' => $data['default']['departamento'] ?? '',
            'provincia' => $data['default']['provincia'] ?? '',
            'distrito' => $data['default']['distrito'] ?? '',
            'telefono' => $data['default']['telefono'] ?? '',
            'direccion' => $data['default']['direccion'] ?? '',
            'medio_transporte' => $data['default']['medio_de_transporte'] ?? '',
            'tiempo_demora' => $data['default']['tiempo_de_demora'] ?? '',
            'material_vivienda' => $data['default']['material_vivienda'] ?? '',
            'energia_electrica' => $data['default']['energia_electrica'] ?? '',
            'agua_potable' => $data['default']['agua_potable'] ?? '',
            'desague' => $data['default']['desague'] ?? '',
            'ss_hh' => $data['default']['s_s__h_h'] ?? '',
            'num_habitantes' => $data['default']['numero_de_habitantes'] ?? '',
            'situacion_vivienda' => $data['default']['situacion_de_vivienda'] ?? '',
            'lengua_materna' => $data['default']['lengua_materna'] ?? '',
            'colegio_procedencia' => $data['default']['colegio_de_procedencia'] ?? '',
        ];
        
        // Asegurar que existen los arrays de opciones
        $editData['sangres'] = $data['sangres'] ?? [];
        $editData['sexos'] = $data['sexos'] ?? [];
        $editData['estadosciviles'] = $data['estadosciviles'] ?? [];
        $editData['lenguasmaternas'] = $data['lenguasmaternas'] ?? [];
        $editData['departamentos'] = $data['departamentos'] ?? [];
        $editData['provincias'] = $data['provincias'] ?? [];
        $editData['distritos'] = $data['distritos'] ?? [];
    @endphp

    <div class="p-8 m-4 bg-gray-100 dark:bg-white/[0.03] rounded-2xl">
        <!-- Header -->
        <div class="flex pb-6 justify-between items-center border-b border-gray-200 dark:border-gray-700">
            <div>
                <h2 class="text-2xl font-bold dark:text-gray-200 text-gray-800">Editar Datos del Estudiante</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Actualice la información del estudiante</p>
            </div>
            <div class="flex gap-3">
                <input form="form-datos" type="submit"
                    class="cursor-pointer inline-flex items-center gap-2 rounded-lg border border-green-300 bg-green-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:border-green-600 dark:bg-green-600 dark:hover:bg-green-700"
                    value="💾 Guardar Cambios"
                >
                <button onclick="toggleEditMode()" type="button"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    ✕ Cancelar
                </button>
            </div>
        </div>

        <form method="POST" id="form-datos" action="{{ route('familiar_alumno_guardar_datos') }}" enctype="multipart/form-data">
            @csrf

            <!-- Input hidden para marcar eliminación de foto -->
            <input type="hidden" name="remove_photo" id="remove_photo" value="0">

            <!-- Tab Navigation -->
            <div class="border-b border-gray-200 dark:border-gray-700 mb-6 relative">
                <!-- Barra deslizante animada -->
                <div id="edit-slider" class="absolute bottom-0 h-0.5 bg-gradient-to-r from-blue-400 to-blue-600 transition-all duration-500 ease-out shadow-lg shadow-blue-500/50" style="width: 0; left: 0;"></div>
                
                <nav class="-mb-px flex space-x-8 overflow-x-auto relative" aria-label="Tabs">
                    <button type="button" onclick="switchEditTab('basica-edit')" 
                        class="edit-tab-btn whitespace-nowrap border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 hover:border-gray-300 hover:text-gray-700 dark:text-blue-400 dark:hover:text-gray-300 transition-colors" 
                        data-edit-tab="basica-edit">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Información Básica
                        </span>
                    </button>
                    <button type="button" onclick="switchEditTab('personal-edit')" 
                        class="edit-tab-btn whitespace-nowrap border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors" 
                        data-edit-tab="personal-edit">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Información Personal
                        </span>
                    </button>
                    <button type="button" onclick="switchEditTab('ubicacion-edit')" 
                        class="edit-tab-btn whitespace-nowrap border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors" 
                        data-edit-tab="ubicacion-edit">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Ubicación
                        </span>
                    </button>
                    <button type="button" onclick="switchEditTab('vivienda-edit')" 
                        class="edit-tab-btn whitespace-nowrap border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors" 
                        data-edit-tab="vivienda-edit">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Vivienda
                        </span>
                    </button>
                    <button type="button" onclick="switchEditTab('cultural-edit')" 
                        class="edit-tab-btn whitespace-nowrap border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors" 
                        data-edit-tab="cultural-edit">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            Cultural y Educativa
                        </span>
                    </button>
                </nav>
            </div>

            <!-- Tab 1: Información Básica -->
            <div id="basica-edit" class="edit-tab-content">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Columna de Foto -->
                    <div class="lg:col-span-1">
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-4">Fotografía del Alumno</h3>
                            
                            <div class="flex flex-col items-center">
                                <!-- Drop Zone -->
                                <div id="drop-zone" class="relative w-60 h-72 mb-4 bg-white dark:bg-gray-900 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg transition-all duration-200 ease-in-out hover:border-blue-400 dark:hover:border-blue-500 flex items-center justify-center cursor-pointer">
                                    <!-- Avatar por defecto según sexo (se muestra cuando no hay foto) -->
                                    <div id="avatar-placeholder-edit" class="absolute inset-0 flex flex-col items-center justify-center {{ $editData['alumno']->foto ? 'hidden' : '' }}">
                                        @if(($editData['alumno']->sexo ?? 'M') === 'F')
                                            <svg class="w-32 h-32 text-pink-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                            </svg>
                                        @else
                                            <svg class="w-32 h-32 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                    </div>

                                    <img id="preview-foto"
                                        src="{{ $editData['alumno']->foto_url }}"
                                        alt="Preview"
                                        class="w-full h-full object-cover rounded-lg {{ $editData['alumno']->foto ? '' : 'hidden' }}"
                                    >

                                    <!-- Botón para eliminar foto -->
                                    @if($editData['alumno']->foto)
                                    <button type="button" id="remove-photo-btn" onclick="removePhoto()" class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-2 shadow-lg transition-all hover:scale-110 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 z-10">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                                
                                <input type="file" 
                                    id="foto" 
                                    name="foto" 
                                    accept="image/*" 
                                    class="hidden"
                                    onchange="previewImage(event)"
                                >
                                
                                <label for="foto" 
                                    class="cursor-pointer inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-blue-500 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Seleccionar foto
                                </label>
                                
                                @if($errors->has('foto'))
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $errors->first('foto') }}</p>
                                @endif
                                
                                <p class="mt-3 text-xs text-center text-gray-500 dark:text-gray-400">
                                    Formatos permitidos: JPG, PNG<br>
                                    Tamaño recomendado: 240 x 288 px<br>
                                    Tamaño máximo: 2MB
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Columna de Datos Básicos -->
                    <div class="lg:col-span-2">
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-6">Datos Básicos</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @include('components.forms.string-ineditable-real', [
                                    'label' => 'Código Educando',
                                    'name' => 'codigo_educando',
                                    'error' => $errors->first('codigo_educando') ?? false,
                                    'value' => old('codigo_educando', $editData['alumno']->codigo_educando)
                                ])
                                @include('components.forms.string-ineditable-real', [
                                    'label' => 'Código Modular',
                                    'name' => 'codigo_modular',
                                    'error' => $errors->first('codigo_modular') ?? false,
                                    'value' => old('codigo_modular', $editData['alumno']->codigo_modular)
                                ])
                                @include('components.forms.string-ineditable-real', [
                                    'label' => 'Año de Ingreso',
                                    'name' => 'año_ingreso',
                                    'error' => $errors->first('año_ingreso') ?? false,
                                    'value' => old('año_ingreso', $editData['alumno']->año_ingreso)
                                ])
                                @include('components.forms.string-ineditable-real', [
                                    'label' => 'DNI',
                                    'name' => 'd_n_i',
                                    'error' => $errors->first('d_n_i') ?? false,
                                    'value' => old('d_n_i', $editData['alumno']->d_n_i)
                                ])
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de navegación -->
                <div class="flex justify-end mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" onclick="switchEditTab('personal-edit')"
                        class="inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600">
                        Siguiente
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Tab 2: Información Personal -->
            <div id="personal-edit" class="edit-tab-content hidden">
                <!-- Sección 1: Datos de Identidad -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 4h10m-10 4h10"></path>
                        </svg>
                        Datos de Identidad
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Apellido Paterno',
                            'name' => 'apellido_paterno',
                            'error' => $errors->first('apellido_paterno') ?? false,
                            'value' => old('apellido_paterno', $editData['alumno']->apellido_paterno)
                        ])
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Apellido Materno',
                            'name' => 'apellido_materno',
                            'error' => $errors->first('apellido_materno') ?? false,
                            'value' => old('apellido_materno', $editData['alumno']->apellido_materno)
                        ])
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Primer Nombre',
                            'name' => 'primer_nombre',
                            'error' => $errors->first('primer_nombre') ?? false,
                            'value' => old('primer_nombre', $editData['alumno']->primer_nombre)
                        ])
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Otros Nombres',
                            'name' => 'otros_nombres',
                            'error' => $errors->first('otros_nombres') ?? false,
                            'value' => old('otros_nombres', $editData['alumno']->otros_nombres)
                        ])
                    </div>
                </div>

                <!-- Sección 2: Datos Personales -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Datos Personales
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @include('components.forms.combo', [
                            'label' => 'Sexo',
                            'name' => 'sexo',
                            'error' => $errors->first('sexo') ?? false,
                            'value' => old('sexo', $editData['alumno']->sexo),
                            'options' => $editData['sexos'],
                            'options_attributes' => ['id', 'descripcion'],
                            'disableSearch' => true
                        ])
                        @include('components.forms.date', [
                            'label' => 'Fecha Nacimiento',
                            'name' => 'fecha_nacimiento',
                            'error' => $errors->first('fecha_nacimiento') ?? false,
                            'value' => old('fecha_nacimiento', $editData['alumno']->fecha_nacimiento)
                        ])
                        @include('components.forms.string', [
                            'label' => 'Teléfono',
                            'name' => 'telefono',
                            'error' => $errors->first('telefono') ?? false,
                            'value' => old('telefono', $editData['alumno']->telefono)
                        ])
                        @include('components.forms.string-ineditable-real', [
                            'label' => 'Escala',
                            'name' => 'escala',
                            'error' => $errors->first('escala') ?? false,
                            'value' => old('escala', $editData['alumno']->escala ?? 'A')
                        ])
                    </div>
                </div>

                <!-- Sección 3: Datos Culturales y Religiosos -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        Datos Culturales y Religiosos
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @include('components.forms.combo', [
                            'label' => 'Lengua Materna',
                            'name' => 'lengua_materna',
                            'error' => $errors->first('lengua_materna') ?? false,
                            'value' => old('lengua_materna', $editData['alumno']->lengua_materna),
                            'options' => $editData['lenguasmaternas'],
                            'options_attributes' => ['id', 'descripcion'],
                            'disableSearch' => true
                        ])
                        @include('components.forms.combo', [
                            'label' => 'Estado Civil',
                            'name' => 'estado_civil',
                            'error' => $errors->first('estado_civil') ?? false,
                            'value' => old('estado_civil', $editData['alumno']->estado_civil),
                            'options' => $editData['estadosciviles'],
                            'options_attributes' => ['id', 'descripcion'],
                            'disableSearch' => true
                        ])
                        @include('components.forms.string', [
                            'label' => 'Religión',
                            'name' => 'religion',
                            'error' => $errors->first('religion') ?? false,
                            'value' => old('religion', $editData['alumno']->religion)
                        ])
                        @include('components.forms.date', [
                            'label' => 'Fecha Bautizo',
                            'name' => 'fecha_bautizo',
                            'error' => $errors->first('fecha_bautizo') ?? false,
                            'value' => old('fecha_bautizo', $editData['alumno']->fecha_bautizo)
                        ])
                    </div>
                </div>

                <!-- Botones de navegación -->
                <div class="flex justify-between mt-6">
                    <button type="button" onclick="switchEditTab('basica-edit')"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Anterior
                    </button>
                    <button type="button" onclick="switchEditTab('ubicacion-edit')"
                        class="inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600">
                        Siguiente
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Tab 3: Ubicación -->
            <div id="ubicacion-edit" class="edit-tab-content hidden">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Ubicación Geográfica y Contacto
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @include('components.forms.combo_dependient', [
                        'label' => 'Departamento',
                        'name' => 'departamento',
                        'error' => $errors->first('departamento') ?? false,
                        'placeholder' => 'Seleccionar departamento...',
                        'value' => old('departamento', $editData['alumno']->departamento),
                        'value_field' => 'id_departamento',
                        'text_field' => 'descripcion',
                        'options' => $editData['departamentos'],
                        'enabled' => true,
                        'disableSearch' => true
                    ])
                    @include('components.forms.combo_dependient', [
                        'label' => 'Provincia',
                        'name' => 'provincia',
                        'error' => $errors->first('provincia') ?? false,
                        'value' => old('provincia', $editData['alumno']->provincia),
                        'placeholder' => 'Seleccionar provincia...',
                        'depends_on' => 'departamento',
                        'parent_field' => 'id_departamento',
                        'value_field' => 'id_provincia',
                        'text_field' => 'descripcion',
                        'options' => $editData['provincias'],
                        'enabled' => !empty($editData['alumno']->departamento),
                        'disableSearch' => true
                    ])
                    @include('components.forms.combo_dependient', [
                        'label' => 'Distrito',
                        'name' => 'distrito',
                        'error' => $errors->first('distrito') ?? false,
                        'value' => old('distrito', $editData['alumno']->distrito),
                        'placeholder' => 'Seleccionar distrito...',
                        'depends_on' => 'provincia',
                        'parent_field' => 'id_provincia',
                        'value_field' => 'id_distrito',
                        'text_field' => 'descripcion',
                        'options' => $editData['distritos'],
                        'enabled' => !empty($editData['alumno']->provincia),
                        'disableSearch' => true
                    ])
                    @include('components.forms.string', [
                        'label' => 'Teléfono',
                        'name' => 'telefono',
                        'error' => $errors->first('telefono') ?? false,
                        'value' => old('telefono', $editData['alumno']->telefono)
                    ])
                </div>
                <div class="grid grid-cols-1 gap-6 mt-4">
                    @include('components.forms.string', [
                        'label' => 'Dirección',
                        'name' => 'direccion',
                        'error' => $errors->first('direccion') ?? false,
                        'value' => old('direccion', $editData['alumno']->direccion)
                    ])
                </div>

                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 mt-8 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    Transporte
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @include('components.forms.string', [
                        'label' => 'Medio de Transporte',
                        'name' => 'medio_transporte',
                        'error' => $errors->first('medio_transporte') ?? false,
                        'value' => old('medio_transporte', $editData['alumno']->medio_transporte)
                    ])
                    @include('components.forms.string', [
                        'label' => 'Tiempo de Demora',
                        'name' => 'tiempo_demora',
                        'error' => $errors->first('tiempo_demora') ?? false,
                        'value' => old('tiempo_demora', $editData['alumno']->tiempo_demora)
                    ])
                </div>

                <!-- Botones de navegación -->
                <div class="flex justify-between mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" onclick="switchEditTab('personal-edit')"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Anterior
                    </button>
                    <button type="button" onclick="switchEditTab('vivienda-edit')"
                        class="inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600">
                        Siguiente
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Tab 4: Vivienda -->
            <div id="vivienda-edit" class="edit-tab-content hidden">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Información de Vivienda
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @include('components.forms.string', [
                        'label' => 'Material Vivienda',
                        'name' => 'material_vivienda',
                        'error' => $errors->first('material_vivienda') ?? false,
                        'value' => old('material_vivienda', $editData['alumno']->material_vivienda)
                    ])
                    @include('components.forms.string', [
                        'label' => 'Energía Eléctrica',
                        'name' => 'energia_electrica',
                        'error' => $errors->first('energia_electrica') ?? false,
                        'value' => old('energia_electrica', $editData['alumno']->energia_electrica)
                    ])
                    @include('components.forms.string', [
                        'label' => 'Agua Potable',
                        'name' => 'agua_potable',
                        'error' => $errors->first('agua_potable') ?? false,
                        'value' => old('agua_potable', $editData['alumno']->agua_potable)
                    ])
                    @include('components.forms.string', [
                        'label' => 'Desagüe',
                        'name' => 'desague',
                        'error' => $errors->first('desague') ?? false,
                        'value' => old('desague', $editData['alumno']->desague)
                    ])
                    @include('components.forms.string', [
                        'label' => 'SS.HH.',
                        'name' => 'ss_hh',
                        'error' => $errors->first('ss_hh') ?? false,
                        'value' => old('ss_hh', $editData['alumno']->ss_hh)
                    ])
                    @include('components.forms.string', [
                        'label' => 'Número de Habitantes',
                        'name' => 'num_habitantes',
                        'error' => $errors->first('num_habitantes') ?? false,
                        'value' => old('num_habitantes', $editData['alumno']->num_habitantes)
                    ])
                    @include('components.forms.string', [
                        'label' => 'Situación de Vivienda',
                        'name' => 'situacion_vivienda',
                        'error' => $errors->first('situacion_vivienda') ?? false,
                        'value' => old('situacion_vivienda', $editData['alumno']->situacion_vivienda)
                    ])
                </div>

                <!-- Botones de navegación -->
                <div class="flex justify-between mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" onclick="switchEditTab('ubicacion-edit')"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Anterior
                    </button>
                    <button type="button" onclick="switchEditTab('cultural-edit')"
                        class="inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600">
                        Siguiente
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Tab 5: Cultural y Educativa -->
            <div id="cultural-edit" class="edit-tab-content hidden">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Información Cultural y Educativa
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @include('components.forms.string', [
                        'label' => 'Parroquia de Bautizo',
                        'name' => 'parroquia_bautizo',
                        'error' => $errors->first('parroquia_bautizo') ?? false,
                        'value' => old('parroquia_bautizo', $editData['alumno']->parroquia_bautizo)
                    ])
                    @include('components.forms.string', [
                        'label' => 'Colegio de Procedencia',
                        'name' => 'colegio_procedencia',
                        'error' => $errors->first('colegio_procedencia') ?? false,
                        'value' => old('colegio_procedencia', $editData['alumno']->colegio_procedencia)
                    ])
                </div>

                <!-- Botones de navegación -->
                <div class="flex justify-between mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" onclick="switchEditTab('vivienda-edit')"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Anterior
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
        // Ocultar mensaje de éxito después de 3 segundos
        document.addEventListener('DOMContentLoaded', function() {
            const successMsg = document.getElementById('success-message');
            if (successMsg) {
                setTimeout(() => {
                    successMsg.style.display = 'none';
                }, 3000);
            }
        });
    // Función para cambiar entre pestañas (Vista)
    function switchTab(tabName) {
        // Ocultar todas las pestañas
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.add('hidden');
        });
        
        // Mostrar la pestaña seleccionada con animación
        const selectedPane = document.getElementById('tab-' + tabName);
        selectedPane.classList.remove('hidden');
        selectedPane.style.opacity = '0';
        selectedPane.style.transform = 'translateY(10px)';
        
        setTimeout(() => {
            selectedPane.style.transition = 'all 0.3s ease-out';
            selectedPane.style.opacity = '1';
            selectedPane.style.transform = 'translateY(0)';
        }, 10);
        
        // Actualizar estilos de los botones
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
            button.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        });
        
        const selectedButton = document.querySelector(`[data-tab="${tabName}"]`);
        selectedButton.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        selectedButton.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        
        // Animar la barra deslizante
        updateSlider(selectedButton);
    }

    // Función para cambiar entre pestañas (Edición)
    function switchEditTab(tabName) {
        // Ocultar todas las pestañas de edición
        document.querySelectorAll('.edit-tab-content').forEach(pane => {
            pane.classList.add('hidden');
        });
        
        // Mostrar la pestaña seleccionada con animación
        const selectedPane = document.getElementById(tabName);
        selectedPane.classList.remove('hidden');
        selectedPane.style.opacity = '0';
        selectedPane.style.transform = 'translateY(10px)';
        
        setTimeout(() => {
            selectedPane.style.transition = 'all 0.3s ease-out';
            selectedPane.style.opacity = '1';
            selectedPane.style.transform = 'translateY(0)';
        }, 10);
        
        // Actualizar estilos de los botones
        document.querySelectorAll('.edit-tab-btn').forEach(button => {
            button.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
            button.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        });
        
        const selectedButton = document.querySelector(`[data-edit-tab="${tabName}"]`);
        selectedButton.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        selectedButton.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        
        // Animar la barra deslizante de edición
        updateEditSlider(selectedButton);

        // Scroll to top suave
        selectedPane.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Función para actualizar la posición de la barra deslizante (Vista)
    function updateSlider(button) {
        const slider = document.getElementById('tab-slider');
        const buttonRect = button.getBoundingClientRect();
        const containerRect = button.parentElement.getBoundingClientRect();
        
        const left = buttonRect.left - containerRect.left;
        const width = buttonRect.width;
        
        slider.style.left = left + 'px';
        slider.style.width = width + 'px';
    }

    // Función para actualizar la posición de la barra deslizante (Edición)
    function updateEditSlider(button) {
        const slider = document.getElementById('edit-slider');
        const buttonRect = button.getBoundingClientRect();
        const containerRect = button.parentElement.getBoundingClientRect();
        
        const left = buttonRect.left - containerRect.left;
        const width = buttonRect.width;
        
        slider.style.left = left + 'px';
        slider.style.width = width + 'px';
    }

    function toggleEditMode() {
        const viewContainer = document.getElementById('view-container');
        const editContainer = document.getElementById('edit-container');

        if (viewContainer.classList.contains('hidden')) {
            // Volver a vista de lectura
            viewContainer.classList.remove('hidden');
            editContainer.classList.add('hidden');
        } else {
            // Mostrar vista de edición
            viewContainer.classList.add('hidden');
            editContainer.classList.remove('hidden');

            // Inicializar la primera pestaña de edición
            setTimeout(() => {
                const firstEditBtn = document.querySelector('.edit-tab-btn');
                if (firstEditBtn) {
                    updateEditSlider(firstEditBtn);
                }
            }, 100);

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    function previewImage(eventOrInput) {
        // Manejar tanto evento como input directamente
        const input = eventOrInput.target || eventOrInput;
        const preview = document.getElementById('preview-foto');
        const filename = document.getElementById('foto_filename');
        const removeBtn = document.getElementById('remove-photo-btn');
        const avatarPlaceholder = document.getElementById('avatar-placeholder-edit');
        const removePhotoInput = document.getElementById('remove_photo');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if (avatarPlaceholder) avatarPlaceholder.classList.add('hidden');

                // Mostrar botón de eliminar (crear si no existe)
                let removeButton = document.getElementById('remove-photo-btn');
                if (!removeButton) {
                    const dropZone = preview.parentElement;
                    removeButton = document.createElement('button');
                    removeButton.type = 'button';
                    removeButton.id = 'remove-photo-btn';
                    removeButton.onclick = removePhoto;
                    removeButton.className = 'absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-2 shadow-lg transition-all hover:scale-110 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 z-10';
                    removeButton.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
                    dropZone.appendChild(removeButton);
                }
                removeButton.classList.remove('hidden');
            }

            reader.readAsDataURL(input.files[0]);

            if (filename) {
                filename.innerText = input.files[0].name;
                filename.classList.remove('text-gray-600');
                filename.classList.add('text-gray-900', 'dark:text-white');
            }

            // Resetear el flag de eliminación si se selecciona una nueva foto
            if (removePhotoInput) removePhotoInput.value = '0';
        }
    }

    function removePhoto() {
        const preview = document.getElementById('preview-foto');
        const fotoInput = document.getElementById('foto');
        const removeBtn = document.getElementById('remove-photo-btn');
        const avatarPlaceholder = document.getElementById('avatar-placeholder-edit');
        const removePhotoInput = document.getElementById('remove_photo');

        // Marcar para eliminación en el servidor
        if (removePhotoInput) removePhotoInput.value = '1';

        // Limpiar el input de archivo
        if (fotoInput) fotoInput.value = null;

        // Ocultar preview y botón de eliminar
        if (preview) preview.classList.add('hidden');
        if (removeBtn) removeBtn.classList.add('hidden');

        // Mostrar avatar placeholder según sexo
        if (avatarPlaceholder) avatarPlaceholder.classList.remove('hidden');
    }

    // Inicializar al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar la posición de la barra deslizante
        const firstTab = document.querySelector('[data-tab="basica"]');
        if (firstTab) {
            setTimeout(() => updateSlider(firstTab), 100);
        }

        // Manejar el botón volver dentro del edit-container después de cargar
        setTimeout(() => {
            const editContainer = document.getElementById('edit-container');
            if (editContainer) {
                // Encontrar todos los enlaces "Volver" dentro de la vista de edición
                const volverLinks = editContainer.querySelectorAll('a');
                volverLinks.forEach(link => {
                    // Verificar si el texto del link contiene "Volver"
                    if (link.textContent.trim().toLowerCase().includes('volver')) {
                        link.addEventListener('click', function(e) {
                            // Si estamos en modo edición, prevenir navegación y solo cambiar vista
                            if (!editContainer.classList.contains('hidden')) {
                                e.preventDefault();
                                toggleEditMode();
                            }
                        });
                    }
                });
            }
        }, 100);

        // Configurar Drag & Drop para la zona de foto
        const dropZone = document.getElementById('drop-zone');
        const fotoInput = document.getElementById('foto');

        if (dropZone && fotoInput) {
            // Prevenir comportamiento por defecto del navegador
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            // Resaltar zona al arrastrar
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, unhighlight, false);
            });

            function highlight(e) {
                dropZone.classList.remove('border-gray-300', 'dark:border-gray-600');
                dropZone.classList.add('border-blue-500', 'border-4', 'bg-blue-50', 'dark:bg-blue-900/20', 'scale-105');
            }

            function unhighlight(e) {
                dropZone.classList.remove('border-blue-500', 'border-4', 'bg-blue-50', 'dark:bg-blue-900/20', 'scale-105');
                dropZone.classList.add('border-gray-300', 'dark:border-gray-600');
            }

            // Manejar el drop
            dropZone.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;

                if (files.length > 0 && files[0].type.startsWith('image/')) {
                    // Asignar el archivo al input
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(files[0]);
                    fotoInput.files = dataTransfer.files;

                    // Disparar el evento change para que se ejecute previewImage
                    const event = new Event('change', { bubbles: true });
                    fotoInput.dispatchEvent(event);
                }
            }

            // Click en la zona también abre el selector de archivos
            dropZone.addEventListener('click', function(e) {
                // No abrir si se hizo click en el botón de eliminar
                if (!e.target.closest('#remove-photo-btn')) {
                    fotoInput.click();
                }
            });
        }
    });
</script>
