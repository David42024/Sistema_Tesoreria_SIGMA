@extends('base.administrativo.blank')

@section('titulo')
    Crear un alumno
@endsection

@section('contenido')
    <div class="p-8 m-4 bg-gray-100 dark:bg-white/[0.03] rounded-2xl">
        <!-- Header -->
        <div class="flex pb-6 justify-between items-center border-b border-gray-200 dark:border-gray-700">
            <div>
                <h2 class="text-2xl font-bold dark:text-gray-200 text-gray-800">Nuevo Alumno</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Registra un nuevo alumno en el sistema</p>
            </div>
            <div class="flex gap-3">
                <input form="form" type="submit"
                    class="cursor-pointer inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700"
                    @click="document.querySelector('.definir_familiares').value = '1';" 
                    value="Crear alumno y definir familiares"
                >

                <input form="form" type="submit"
                    class="cursor-pointer inline-flex items-center gap-2 rounded-lg border border-blue-300 px-6 py-2.5 text-sm font-medium text-black dark:text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-blue-600"
                    value="Crear alumno únicamente"
                    @click="document.querySelector('.definir_familiares').value = '0';"
                >

                <a href="{{ $data['return'] }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    Cancelar
                </a>
            </div>
        </div>

        <form method="POST" id="form" action="" enctype="multipart/form-data" class="mt-8">
            @method('PUT')
            @csrf

            <input type="text" class="definir_familiares" name="definir_familiares" value="0" hidden>

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
            <div class="tab-content"
>
                <!-- Tab 1: Información Básica y Foto -->
            <!-- Tab Contents -->
            <div class="tab-content">
                <!-- Tab 1: Información Básica y Foto -->
                <div id="tab-basica" class="tab-pane">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Columna de Foto -->
                        <div class="lg:col-span-1">
                            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-4">Fotografía del Alumno</h3>
                                
                                <div class="flex flex-col items-center">
                                    <!-- Drop Zone -->
                                    <div id="drop-zone" class="relative w-60 h-72 mb-4 bg-white dark:bg-gray-900 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg transition-colors hover:border-blue-400 dark:hover:border-blue-500">
                                        <!-- Ícono de cámara por defecto -->
                                        <div id="camera-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-gray-300 dark:text-gray-600">
                                            <svg class="w-20 h-20 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <p class="text-sm font-medium">Arrastra o haz clic</p>
                                            <p class="text-xs mt-1">para subir foto</p>
                                        </div>
                                        
                                        <img id="preview-foto" 
                                            src="" 
                                            alt="Preview" 
                                            class="hidden w-full h-full object-cover rounded-lg"
                                        >
                                        
                                        <!-- Botón para eliminar foto -->
                                        <button type="button" id="remove-photo-btn" onclick="removePhoto()" class="hidden absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-2 shadow-lg transition-all hover:scale-110 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 z-10">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                        
                                        <!-- Overlay de arrastrar -->
                                        <div id="drag-overlay" class="hidden absolute inset-0 bg-blue-500 bg-opacity-20 border-4 border-blue-500 rounded-lg flex items-center justify-center">
                                            <div class="text-center">
                                                <svg class="w-12 h-12 mx-auto text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                </svg>
                                                <p class="mt-2 text-sm font-medium text-blue-600 dark:text-blue-400">Suelta la imagen aquí</p>
                                            </div>
                                        </div>
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
                                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-6">Datos de Identificación</h3>
                                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                                    <div class="flex items-start gap-3">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-blue-900 dark:text-blue-200">El Código de Educando se generará automáticamente</p>
                                            <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">El sistema asignará un código único al crear el alumno</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @include('components.forms.string', [
                                        'label' => 'Código Modular',
                                        'name' => Str::snake('Código Modular'),
                                        'error' => $errors->first(Str::snake('Código Modular')) ?? false,
                                        'value' => old(Str::snake('Código Modular'), $data['session_data']['código_modular'] ?? ''),
                                        'required' => false
                                    ])

                                    @include('components.forms.date-año', [
                                        'label' => 'Año de Ingreso',
                                        'name' => Str::snake('Año de Ingreso'),
                                        'error' => $errors->first(Str::snake('Año de Ingreso')) ?? false,
                                        'value' => old(Str::snake('Año de Ingreso'), $data['session_data']['año_de_ingreso'] ?? ''),
                                        'required' => true
                                    ])

                                    @include('components.forms.string', [
                                        'label' => 'DNI',
                                        'name' => Str::snake('DNI'),
                                        'error' => $errors->first(Str::snake('DNI')) ?? false,
                                        'value' => old(Str::snake('DNI'), $data['session_data']['d_n_i'] ?? ''),
                                        'required' => true
                                    ])
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="switchTab('personal')" 
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Siguiente
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Tab 2: Información Personal -->
                <div id="tab-personal" class="tab-pane hidden">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-6">Nombres y Apellidos</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @include('components.forms.string', [
                                'label' => 'Apellido Paterno',
                                'name' => Str::snake('Apellido Paterno'),
                                'error' => $errors->first(Str::snake('Apellido Paterno')) ?? false,
                                'value' => old(Str::snake('Apellido Paterno'), $data['session_data']['apellido_paterno'] ?? ''),
                                'required' => true
                            ])

                            @include('components.forms.string', [
                                'label' => 'Apellido Materno',
                                'name' => Str::snake('Apellido Materno'),
                                'error' => $errors->first(Str::snake('Apellido Materno')) ?? false,
                                'value' => old(Str::snake('Apellido Materno'), $data['session_data']['apellido_materno'] ?? ''),
                                'required' => true
                            ])

                            @include('components.forms.string', [
                                'label' => 'Primer Nombre',
                                'name' => Str::snake('Primer Nombre'),
                                'error' => $errors->first(Str::snake('Primer Nombre')) ?? false,
                                'value' => old(Str::snake('Primer Nombre'), $data['session_data']['primer_nombre'] ?? ''),
                                'required' => true
                            ])

                            @include('components.forms.string', [
                                'label' => 'Otros Nombres',
                                'name' => Str::snake('Otros Nombres'),
                                'error' => $errors->first(Str::snake('Otros Nombres')) ?? false,
                                'value' => old(Str::snake('Otros Nombres'), $data['session_data']['otros_nombres'] ?? ''),
                                'required' => false
                            ])
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-6">Datos Demográficos</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @include('components.forms.combo', [
                                'label' => 'Sexo',
                                'name' => Str::snake('Sexo'),
                                'error' => $errors->first(Str::snake('Sexo')) ?? false,
                                'value' => old(Str::snake('Sexo'), $data['session_data']['sexo'] ?? ''),
                                'options' => $data['sexos'],
                                'options_attributes' => ['id', 'descripcion'],
                                'disableSearch' => true
                            ])

                            @include('components.forms.date', [
                                'label' => 'Fecha Nacimiento',
                                'name' => Str::snake('Fecha Nacimiento'),
                                'error' => $errors->first(Str::snake('Fecha Nacimiento')) ?? false,
                                'value' => old(Str::snake('Fecha Nacimiento'), $data['session_data']['fecha_nacimiento'] ?? ''),
                            ])

                            @include('components.forms.string', [
                                'label' => 'Teléfono',
                                'name' => Str::snake('Teléfono'),
                                'error' => $errors->first(Str::snake('Teléfono')) ?? false,
                                'value' => old(Str::snake('Teléfono'), $data['session_data']['teléfono'] ?? ''),
                                'required' => false
                            ])

                            @include('components.forms.combo', [
                                'label' => 'Escala',
                                'name' => Str::snake('Escala'),
                                'error' => $errors->first(Str::snake('Escala')) ?? false,
                                'value' => old(Str::snake('Escala'), $data['session_data']['escala'] ?? ''),
                                'options' => $data['escalas'],
                                'options_attributes' => ['id', 'descripcion'],
                                'disableSearch' => true
                            ])
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-6">Información Cultural</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @include('components.forms.combo', [
                                'label' => 'Lengua Materna',
                                'name' => Str::snake('Lengua Materna'),
                                'error' => $errors->first(Str::snake('Lengua Materna')) ?? false,
                                'value' => old(Str::snake('Lengua Materna'), $data['session_data']['lengua_materna'] ?? ''),
                                'options' => $data['lenguasmaternas'],
                                'options_attributes' => ['id', 'descripcion'],
                                'disableSearch' => true
                            ])

                            @include('components.forms.combo', [
                                'label' => 'Estado Civil',
                                'name' => Str::snake('Estado Civil'),
                                'error' => $errors->first(Str::snake('Estado Civil')) ?? false,
                                'value' => old(Str::snake('Estado Civil'), $data['session_data']['estado_civil'] ?? ''),
                                'options' => $data['estadosciviles'],
                                'options_attributes' => ['id', 'descripcion'],
                                'disableSearch' => true
                            ])

                            @include('components.forms.string', [
                                'label' => 'Religión',
                                'name' => Str::snake('Religión'),
                                'error' => $errors->first(Str::snake('Religión')) ?? false,
                                'value' => old(Str::snake('Religión'), $data['session_data']['religión'] ?? ''),
                                'required' => false
                            ])

                            @include('components.forms.date', [
                                'label' => 'Fecha Bautizo',
                                'name' => Str::snake('Fecha Bautizo'),
                                'error' => $errors->first(Str::snake('Fecha Bautizo')) ?? false,
                                'value' => old(Str::snake('Fecha Bautizo'), $data['session_data']['fecha_bautizo'] ?? ''),
                                'required' => false
                            ])
                        </div>
                    </div>
                    
                    <div class="flex justify-between gap-3 mt-6">
                        <button type="button" onclick="switchTab('basica')" 
                            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Anterior
                        </button>
                        <button type="button" onclick="switchTab('ubicacion')" 
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600">
                            Siguiente
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Tab 3: Ubicación -->
                <div id="tab-ubicacion" class="tab-pane hidden">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-6">Ubicación Geográfica</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @include('components.forms.combo_dependient', [
                                'label' => 'País',
                                'name' => Str::snake('Pais'),
                                'error' => $errors->first(Str::snake('País')) ?? false,
                                'placeholder' => 'Seleccionar país...',
                                'value' => old(Str::snake('País'), $data['session_data']['país'] ?? ''),
                                'value_field' => 'id_pais',
                                'text_field' => 'descripcion',
                                'options' => $data['paises'],
                                'enabled' => true,
                                'disableSearch' => true
                            ])

                            @include('components.forms.combo_dependient', [
                                'label' => 'Departamento',
                                'name' => Str::snake('Departamento'),
                                'error' => $errors->first(Str::snake('Departamento')) ?? false,
                                'placeholder' => 'Seleccionar departamento...',
                                'depends_on' => Str::snake('Pais'),
                                'parent_field' => 'id_pais',
                                'value' => old(Str::snake('Departamento'), $data['session_data']['departamento'] ?? ''),
                                'value_field' => 'id_departamento',
                                'text_field' => 'descripcion',
                                'options' => $data['departamentos'],
                                'enabled' => false,
                                'disableSearch' => true
                            ])

                            @include('components.forms.combo_dependient', [
                                'label' => 'Provincia',
                                'name' => Str::snake('Provincia'),
                                'error' => $errors->first(Str::snake('Provincia')) ?? false,
                                'value' => old(Str::snake('Provincia'), $data['session_data']['provincia'] ?? ''),
                                'placeholder' => 'Seleccionar provincia...',
                                'depends_on' => Str::snake('Departamento'),
                                'parent_field' => 'id_departamento',
                                'value_field' => 'id_provincia',
                                'text_field' => 'descripcion',
                                'options' => $data['provincias'],
                                'enabled' => false,
                                'disableSearch' => true
                            ])

                            @include('components.forms.combo_dependient', [
                                'label' => 'Distrito',
                                'name' => Str::snake('Distrito'),
                                'error' => $errors->first(Str::snake('Distrito')) ?? false,
                                'value' => old(Str::snake('Distrito'), $data['session_data']['distrito'] ?? ''),
                                'placeholder' => 'Seleccionar distrito...',
                                'depends_on' => Str::snake('Provincia'),
                                'parent_field' => 'id_provincia',
                                'value_field' => 'id_distrito',
                                'text_field' => 'descripcion',
                                'options' => $data['distritos'],
                                'enabled' => false,
                                'disableSearch' => true
                            ])
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-6">Dirección y Transporte</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                @include('components.forms.string', [
                                    'label' => 'Dirección',
                                    'name' => Str::snake('Dirección'),
                                    'error' => $errors->first(Str::snake('Dirección')) ?? false,
                                    'value' => old(Str::snake('Dirección'), $data['session_data']['dirección'] ?? ''),
                                    'required' => true
                                ])
                            </div>

                            @include('components.forms.string', [
                                'label' => 'Medio de Transporte',
                                'name' => Str::snake('Medio de Transporte'),
                                'error' => $errors->first(Str::snake('Medio de Transporte')) ?? false,
                                'value' => old(Str::snake('Medio de Transporte'), $data['session_data']['medio_de_transporte'] ?? ''),
                                'required' => true
                            ])

                            @include('components.forms.string', [
                                'label' => 'Tiempo de Demora',
                                'name' => Str::snake('Tiempo de Demora'),
                                'error' => $errors->first(Str::snake('Tiempo de Demora')) ?? false,
                                'value' => old(Str::snake('Tiempo de Demora'), $data['session_data']['tiempo_de_demora'] ?? ''),
                                'required' => true
                            ])
                        </div>
                    </div>
                    
                    <div class="flex justify-between gap-3 mt-6">
                        <button type="button" onclick="switchTab('personal')" 
                            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Anterior
                        </button>
                        <button type="button" onclick="switchTab('vivienda')" 
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600">
                            Siguiente
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Tab 4: Vivienda -->
                <div id="tab-vivienda" class="tab-pane hidden">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-6">Características de la Vivienda</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @include('components.forms.string', [
                                'label' => 'Material Vivienda',
                                'name' => Str::snake('Material Vivienda'),
                                'error' => $errors->first(Str::snake('Material Vivienda')) ?? false,
                                'value' => old(Str::snake('Material Vivienda'), $data['session_data']['material_vivienda'] ?? ''),
                                'required' => true
                            ])

                            @include('components.forms.string', [
                                'label' => 'Situación de Vivienda',
                                'name' => Str::snake('Situación de Vivienda'),
                                'error' => $errors->first(Str::snake('Situación de Vivienda')) ?? false,
                                'value' => old(Str::snake('Situación de Vivienda'), $data['session_data']['situación_de_vivienda'] ?? ''),
                                'required' => true
                            ])

                            @include('components.forms.string', [
                                'label' => 'Número de Habitaciones',
                                'name' => Str::snake('Número de Habitaciones'),
                                'error' => $errors->first(Str::snake('Número de Habitaciones')) ?? false,
                                'value' => old(Str::snake('Número de Habitaciones'), $data['session_data']['número_de_habitaciones'] ?? ''),
                            ])

                            @include('components.forms.string', [
                                'label' => 'Número de Habitantes',
                                'name' => Str::snake('Número de Habitantes'),
                                'error' => $errors->first(Str::snake('Número de Habitantes')) ?? false,
                                'value' => old(Str::snake('Número de Habitantes'), $data['session_data']['número_de_habitantes'] ?? ''),
                            ])
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-6">Servicios Básicos</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @include('components.forms.string', [
                                'label' => 'Energía Eléctrica',
                                'name' => Str::snake('Energía Eléctrica'),
                                'error' => $errors->first(Str::snake('Energía Eléctrica')) ?? false,
                                'value' => old(Str::snake('Energía Eléctrica'), $data['session_data']['energía_eléctrica'] ?? ''),
                                'required' => true
                            ])

                            @include('components.forms.string', [
                                'label' => 'Agua Potable',
                                'name' => Str::snake('Agua Potable'),
                                'error' => $errors->first(Str::snake('Agua Potable')) ?? false,
                                'value' => old(Str::snake('Agua Potable'), $data['session_data']['agua_potable'] ?? ''),
                                'required' => false
                            ])

                            @include('components.forms.string', [
                                'label' => 'Desagüe',
                                'name' => Str::snake('Desagüe'),
                                'error' => $errors->first(Str::snake('Desagüe')) ?? false,
                                'value' => old(Str::snake('Desagüe'), $data['session_data']['desagüe'] ?? ''),
                                'required' => false
                            ])

                            @include('components.forms.string', [
                                'label' => 'Servicios Higiénicos',
                                'name' => Str::snake('Servicios Higiénicos'),
                                'error' => $errors->first(Str::snake('Servicios Higiénicos')) ?? false,
                                'value' => old(Str::snake('Servicios Higiénicos'), $data['session_data']['servicios_higiénicos'] ?? ''),
                                'required' => false
                            ])
                        </div>
                    </div>
                    
                    <div class="flex justify-between gap-3 mt-6">
                        <button type="button" onclick="switchTab('ubicacion')" 
                            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Anterior
                        </button>
                        <button type="button" onclick="switchTab('educativa')" 
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600">
                            Siguiente
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Tab 5: Educativa -->
                <div id="tab-educativa" class="tab-pane hidden">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-6">Información Educativa y Religiosa</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @include('components.forms.string', [
                                'label' => 'Parroquia de Bautizo',
                                'name' => Str::snake('Parroquia de Bautizo'),
                                'error' => $errors->first(Str::snake('Parroquia de Bautizo')) ?? false,
                                'value' => old(Str::snake('Parroquia de Bautizo'), $data['session_data']['parroquia_de_bautizo'] ?? ''),
                                'required' => false
                            ])

                            @include('components.forms.string', [
                                'label' => 'Colegio de Procedencia',
                                'name' => Str::snake('Colegio de Procedencia'),
                                'error' => $errors->first(Str::snake('Colegio de Procedencia')) ?? false,
                                'value' => old(Str::snake('Colegio de Procedencia'), $data['session_data']['colegio_de_procedencia'] ?? ''),
                                'required' => false
                            ])
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="switchTab('vivienda')" 
                            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Anterior
                        </button>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="flex justify-end gap-3 pt-6">
                <input form="form" type="submit"
                    class="cursor-pointer inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700"
                    value="Crear alumno y definir familiares" @click="document.querySelector('.definir_familiares').value = '1';"
                >

                <input form="form" type="submit"
                    class="cursor-pointer inline-flex items-center gap-2 rounded-lg border border-blue-300 px-6 py-2.5 text-sm font-medium text-black dark:text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-blue-600"
                    value="Crear alumno únicamente"
                    @click="document.querySelector('.definir_familiares').value = '0';"
                >

                <a href="{{ $data['return'] }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    Cancelar
                </a>
            </div>
        </form>
    </div>

    {{-- Modal de Código Generado --}}
    @if(session('codigo_educando'))
    <div id="codigoModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
            <div class="relative">
                {{-- Header con gradiente --}}
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-6 rounded-t-2xl">
                    <div class="flex items-center justify-center mb-4">
                        <div class="bg-white dark:bg-gray-800 rounded-full p-3">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-white text-center">¡Alumno Creado Exitosamente!</h3>
                </div>

                {{-- Body --}}
                <div class="p-6">
                    <div class="text-center mb-6">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Se ha generado el siguiente código:</p>
                        <div class="bg-gray-50 dark:bg-gray-900 border-2 border-green-200 dark:border-green-800 rounded-xl p-4 mb-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Código de Educando</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white tracking-wider">{{ session('codigo_educando') }}</p>
                        </div>
                        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <p class="text-sm text-amber-800 dark:text-amber-200 text-left">
                                    <span class="font-semibold">Importante:</span> Guarde este código, es esencial para operaciones en el sistema.
                                </p>
                            </div>
                        </div>
                    </div>

                    <button onclick="cerrarModal()" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors shadow-lg">
                        Entendido
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@section('custom-js')
    {{-- Script para el sistema de pestañas --}}
    <script>
        function switchTab(tabName) {
            // Ocultar todos los tab-panes
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.add('hidden');
            });
            
            // Remover estilos activos de todos los botones y restaurar hover
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                button.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400', 'hover:border-gray-300', 'hover:text-gray-700', 'dark:hover:text-gray-300', 'transition-colors');
            });
            
            // Mostrar el tab-pane seleccionado
            document.getElementById('tab-' + tabName).classList.remove('hidden');
            
            // Activar el botón seleccionado y remover hover
            const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
            activeButton.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400', 'hover:border-gray-300', 'hover:text-gray-700', 'dark:hover:text-gray-300');
            activeButton.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400', 'hover:text-blue-700', 'dark:hover:text-blue-300');
            
            // Animar la barra deslizante
            updateSlider(activeButton);
        }

        function updateSlider(activeButton) {
            const slider = document.getElementById('tab-slider');
            const buttonRect = activeButton.getBoundingClientRect();
            const navRect = activeButton.closest('nav').getBoundingClientRect();
            
            const left = buttonRect.left - navRect.left + activeButton.closest('nav').scrollLeft;
            const width = buttonRect.width;
            
            slider.style.width = width + 'px';
            slider.style.left = left + 'px';
        }

        // Función para previsualizar la imagen
        function previewImage(event) {
            const file = event.target.files[0];
            handleImageFile(file);
        }

        function handleImageFile(file) {
            const preview = document.getElementById('preview-foto');
            const placeholder = document.getElementById('camera-placeholder');
            const removeBtn = document.getElementById('remove-photo-btn');
            
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                    removeBtn.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        }

        // Función para eliminar la foto
        function removePhoto() {
            const preview = document.getElementById('preview-foto');
            const placeholder = document.getElementById('camera-placeholder');
            const removeBtn = document.getElementById('remove-photo-btn');
            const fileInput = document.getElementById('foto');
            
            // Limpiar preview
            preview.src = '';
            preview.classList.add('hidden');
            
            // Mostrar placeholder
            placeholder.classList.remove('hidden');
            
            // Ocultar botón de eliminar
            removeBtn.classList.add('hidden');
            
            // Limpiar input file
            fileInput.value = '';
        }

        // Funcionalidad de Drag & Drop
        document.addEventListener('DOMContentLoaded', function() {
            const dropZone = document.getElementById('drop-zone');
            const dragOverlay = document.getElementById('drag-overlay');
            const fileInput = document.getElementById('foto');

            // Prevenir comportamiento por defecto
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
                document.body.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            // Mostrar overlay al arrastrar sobre la zona
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => {
                    dragOverlay.classList.remove('hidden');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => {
                    dragOverlay.classList.add('hidden');
                }, false);
            });

            // Manejar el drop
            dropZone.addEventListener('drop', function(e) {
                const dt = e.dataTransfer;
                const files = dt.files;

                if (files.length > 0) {
                    const file = files[0];
                    // Asignar el archivo al input
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    fileInput.files = dataTransfer.files;
                    
                    // Previsualizar
                    handleImageFile(file);
                }
            }, false);
            // Inicializar la posición de la barra deslizante en la primera pestaña
            const firstTab = document.querySelector('[data-tab="basica"]');
            if (firstTab) {
                setTimeout(() => updateSlider(firstTab), 100);
            }
            
            // Si hay errores de validación, mostrar la pestaña correspondiente
            @if($errors->any())
                const errorFields = @json($errors->keys());
                
                // Mapear campos a pestañas
                const tabMapping = {
                    'código_educando': 'basica',
                    'código_modular': 'basica',
                    'año_de_ingreso': 'basica',
                    'd_n_i': 'basica',
                    'foto': 'basica',
                    'apellido_paterno': 'personal',
                    'apellido_materno': 'personal',
                    'primer_nombre': 'personal',
                    'otros_nombres': 'personal',
                    'sexo': 'personal',
                    'fecha_nacimiento': 'personal',
                    'teléfono': 'personal',
                    'escala': 'personal',
                    'lengua_materna': 'personal',
                    'estado_civil': 'personal',
                    'religión': 'personal',
                    'fecha_bautizo': 'personal',
                    'país': 'ubicacion',
                    'pais': 'ubicacion',
                    'departamento': 'ubicacion',
                    'provincia': 'ubicacion',
                    'distrito': 'ubicacion',
                    'dirección': 'ubicacion',
                    'medio_de_transporte': 'ubicacion',
                    'tiempo_de_demora': 'ubicacion',
                    'material_vivienda': 'vivienda',
                    'energía_eléctrica': 'vivienda',
                    'agua_potable': 'vivienda',
                    'desagüe': 'vivienda',
                    'servicios_higiénicos': 'vivienda',
                    'número_de_habitaciones': 'vivienda',
                    'número_de_habitantes': 'vivienda',
                    'situación_de_vivienda': 'vivienda',
                    'parroquia_de_bautizo': 'educativa',
                    'colegio_de_procedencia': 'educativa'
                };
                
                // Encontrar la primera pestaña con error
                for (let field of errorFields) {
                    if (tabMapping[field]) {
                        switchTab(tabMapping[field]);
                        break;
                    }
                }
            @endif

            // Si hay datos de sesión y el usuario cancela, limpiar la sesión
            @if(isset($data['has_session_data']) && $data['has_session_data'])
                const cancelButton = document.querySelector('a[href*="abort"]');
                if (cancelButton) {
                    cancelButton.addEventListener('click', function() {
                        // Hacer una petición para limpiar la sesión
                        fetch('{{ route("alumno_clear_session") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                            }
                        });
                    });
                }
            @endif
        });

        // Función para cerrar el modal del código generado
        function cerrarModal() {
            const modal = document.getElementById('codigoModal');
            if (modal) {
                modal.style.opacity = '0';
                modal.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    modal.remove();
                }, 300);
            }
        }

        // Cerrar modal con ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                cerrarModal();
            }
        });
    </script>
@endsection