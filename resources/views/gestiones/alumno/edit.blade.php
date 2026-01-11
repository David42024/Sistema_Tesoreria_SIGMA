@extends('base.administrativo.blank')

@section('titulo')
    Alumnos | Edición
@endsection

@section('contenido')
    <div class="p-8 m-4 bg-gray-100 dark:bg-white/[0.03] rounded-2xl">
        <!-- Header -->
        <div class="flex pb-6 justify-between items-center border-b border-gray-200 dark:border-gray-700">
            <div>
                <h2 class="text-2xl font-bold dark:text-gray-200 text-gray-800">Editar Alumno</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">ID: {{ $data['id'] }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('alumno_add_familiares', ['id' => $data['id']]) }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                    </svg>
                    Asignar Familiar
                </a>
                <input form="form" type="submit"
                    class="cursor-pointer inline-flex items-center gap-2 rounded-lg border border-green-300 bg-green-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:border-green-600 dark:bg-green-600 dark:hover:bg-green-700"
                    value="Guardar Cambios"
                >
                <a href="{{ $data['return'] }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    Cancelar
                </a>
            </div>
        </div>

        <form method="POST" id="form" action="" enctype="multipart/form-data" class="mt-8">
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
                                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-4">Fotografía del Alumno</h3>
                                
                                <div class="flex flex-col items-center">
                                    <!-- Drop Zone -->
                                    <div id="drop-zone" class="relative w-60 h-72 mb-4 bg-white dark:bg-gray-900 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg transition-colors hover:border-blue-400 dark:hover:border-blue-500">
                                        <!-- Ícono de cámara por defecto -->
                                        <div id="camera-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-gray-300 dark:text-gray-600 {{ isset($data['default']['foto']) && $data['default']['foto'] ? 'hidden' : '' }}">
                                            <svg class="w-20 h-20 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <p class="text-sm font-medium">Arrastra o haz clic</p>
                                            <p class="text-xs mt-1">para subir foto</p>
                                        </div>
                                        
                                        <img id="preview-foto" 
                                            src="{{ isset($data['default']['foto']) && $data['default']['foto'] ? asset('storage/' . $data['default']['foto']) : '' }}" 
                                            alt="Preview" 
                                            class="{{ isset($data['default']['foto']) && $data['default']['foto'] ? '' : 'hidden' }} w-full h-full object-cover rounded-lg"
                                        >
                                        
                                        <!-- Botón para eliminar foto -->
                                        <button type="button" id="remove-photo-btn" onclick="removePhoto()" class="{{ isset($data['default']['foto']) && $data['default']['foto'] ? '' : 'hidden' }} absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-2 shadow-lg transition-all hover:scale-110 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 z-10">
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
                                        {{ isset($data['default']['foto']) && $data['default']['foto'] ? 'Cambiar foto' : 'Seleccionar foto' }}
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
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @include('components.forms.string', [
                                'label' => 'Código Educando',
                                'name' => Str::snake('Codigo Educando'),
                                'error' => $errors->first(Str::snake('Codigo Educando')) ?? false,
                                'value' => old(Str::snake('Codigo Educando')) ?? $data['default']['codigo_educando'],
                                'readonly' => true
                            ])
                            @include('components.forms.string', [
                                'label' => 'Código Modular',
                                'name' => Str::snake('Codigo Modular'),
                                'error' => $errors->first(Str::snake('Codigo Modular')) ?? false,
                                'value' => old(Str::snake('Codigo Modular')) ?? $data['default']['codigo_modular'],
                                'required' => false
                            ])
                            @include('components.forms.date-año', [
                                'label' => 'Año de Ingreso',
                                'name' => Str::snake('Año de Ingreso'),
                                'error' => $errors->first(Str::snake('Año de Ingreso')) ?? false,
                                'value' => old(Str::snake('Año de Ingreso')) ?? $data['default']['año_de_ingreso']
                            ])
                            @include('components.forms.string', [
                                'label' => 'DNI',
                                'name' => Str::snake('DNI'),
                                'error' => $errors->first(Str::snake('DNI')) ?? false,
                                'value' => old(Str::snake('DNI')) ?? $data['default']['d_n_i'],
                                'readonly' => true
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
                                'value' => old(Str::snake('Apellido Paterno')) ?? $data['default']['apellido_paterno'],
                                'readonly' => true
                            ])
                            @include('components.forms.string', [
                                'label' => 'Apellido Materno',
                                'name' => Str::snake('Apellido Materno'),
                                'error' => $errors->first(Str::snake('Apellido Materno')) ?? false,
                                'value' => old(Str::snake('Apellido Materno')) ?? $data['default']['apellido_materno'],
                                'readonly' => true
                            ])
                            @include('components.forms.string', [
                                'label' => 'Primer Nombre',
                                'name' => Str::snake('Primer Nombre'),
                                'error' => $errors->first(Str::snake('Primer Nombre')) ?? false,
                                'value' => old(Str::snake('Primer Nombre')) ?? $data['default']['primer_nombre'],
                                'readonly' => true
                            ])
                            @include('components.forms.string', [
                                'label' => 'Otros Nombres',
                                'name' => Str::snake('Otros Nombres'),
                                'error' => $errors->first(Str::snake('Otros Nombres')) ?? false,
                                'value' => old(Str::snake('Otros Nombres')) ?? $data['default']['otros_nombres'],
                                'readonly' => true,
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
                                'value' => old(Str::snake('Sexo')) ?? $data['default'][Str::snake('Sexo')],
                                'options' => $data['sexos'],
                                'options_attributes' => ['id', 'descripcion'],
                                'disableSearch' => true
                            ])
                            @include('components.forms.date', [
                                'label' => 'Fecha Nacimiento',
                                'name' => Str::snake('Fecha Nacimiento'),
                                'error' => $errors->first(Str::snake('Fecha Nacimiento')) ?? false,
                                'value' => old(Str::snake('Fecha Nacimiento')) ?? $data['default']['fecha_nacimiento'],
                                'readonly' => true
                            ])
                            @include('components.forms.string', [
                                'label' => 'Teléfono',
                                'name' => Str::snake('Telefono'),
                                'error' => $errors->first(Str::snake('Telefono')) ?? false,
                                'value' => old(Str::snake('Telefono')) ?? $data['default']['telefono'],
                                'required' => false
                            ])
                            @include('components.forms.combo', [
                                'label' => 'Escala',
                                'name' => Str::snake('Escala'),
                                'error' => $errors->first(Str::snake('Escala')) ?? false,
                                'value' => old(Str::snake('Escala')) ?? $data['default'][Str::snake('Escala')],
                                'options' => $data['escalas'],
                                'options_attributes' => ['id', 'descripcion'],
                                'disableSearch' => true,
                                'required' => false
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
                                'value' => old(Str::snake('Lengua Materna')) ?? $data['default'][Str::snake('Lengua Materna')],
                                'options' => $data['lenguasmaternas'],
                                'options_attributes' => ['id', 'descripcion'],
                                'disableSearch' => true
                            ])
                            @include('components.forms.combo', [
                                'label' => 'Estado Civil',
                                'name' => Str::snake('Estado Civil'),
                                'error' => $errors->first(Str::snake('Estado Civil')) ?? false,
                                'value' => old(Str::snake('Estado Civil')) ?? $data['default'][Str::snake('Estado Civil')],
                                'options' => $data['estadosciviles'],
                                'options_attributes' => ['id', 'descripcion'],
                                'disableSearch' => true
                            ])
                            @include('components.forms.string', [
                                'label' => 'Religión',
                                'name' => Str::snake('Religion'),
                                'error' => $errors->first(Str::snake('Religion')) ?? false,
                                'value' => old(Str::snake('Religion')) ?? $data['default']['religion'],
                                'required' => false
                            ])
                            @include('components.forms.date', [
                                'label' => 'Fecha Bautizo',
                                'name' => Str::snake('Fecha Bautizo'),
                                'error' => $errors->first(Str::snake('Fecha Bautizo')) ?? false,
                                'value' => old(Str::snake('Fecha Bautizo')) ?? $data['default']['fecha_bautizo'],
                                'required' => false
                            ])
                        </div>
                    </div>

                    <div class="flex justify-between gap-3 mt-6">
                        <button type="button" onclick="switchTab('basica')" 
                            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Anterior
                        </button>
                        <button type="button" onclick="switchTab('ubicacion')" 
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                        <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-6">Ubicación Geográfica</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @include('components.forms.combo_dependient', [
                                'label' => 'País',
                                'name' => 'pais',
                                'error' => $errors->first(Str::snake('pais')) ?? false,
                                'placeholder' => 'Seleccionar país...',
                                'value' => old(Str::snake('país')) ?? $data['default'][Str::snake('país')],
                                'value_field' => 'id_pais',
                                'text_field' => 'descripcion',
                                'options' => $data['paises'],
                                'enabled' => true,
                            ])
                            @include('components.forms.combo_dependient', [
                                'label' => 'Departamento',
                                'name' => 'departamento',
                                'error' => $errors->first(Str::snake('departamento')) ?? false,
                                'placeholder' => 'Seleccionar departamento...',
                                'depends_on' => 'pais',
                                'parent_field' => 'id_pais',
                                'value' => old(Str::snake('departamento')) ?? $data['default'][Str::snake('departamento')],
                                'value_field' => 'id_departamento',
                                'text_field' => 'descripcion',
                                'options' => $data['departamentos'],
                                'enabled' => false,
                            ])
                            @include('components.forms.combo_dependient', [
                                'label' => 'Provincia',
                                'name' => 'provincia',
                                'error' => $errors->first(Str::snake('provincia')) ?? false,
                                'value' => old(Str::snake('provincia')) ?? $data['default'][Str::snake('provincia')],
                                'placeholder' => 'Seleccionar provincia...',
                                'depends_on' => 'departamento',
                                'parent_field' => 'id_departamento',
                                'value_field' => 'id_provincia',
                                'text_field' => 'descripcion',
                                'options' => $data['provincias'],
                                'enabled' => false,
                            ])
                            @include('components.forms.combo_dependient', [
                                'label' => 'Distrito',
                                'name' => 'distrito',
                                'error' => $errors->first(Str::snake('distrito')) ?? false,
                                'value' => old(Str::snake('distrito')) ?? $data['default'][Str::snake('distrito')],
                                'placeholder' => 'Seleccionar distrito...',
                                'depends_on' => 'provincia',
                                'parent_field' => 'id_provincia',
                                'value_field' => 'id_distrito',
                                'text_field' => 'descripcion',
                                'options' => $data['distritos'],
                                'enabled' => false,
                            ])
                            <div class="md:col-span-2">
                                @include('components.forms.string', [
                                    'label' => 'Dirección',
                                    'name' => Str::snake('Direccion'),
                                    'error' => $errors->first(Str::snake('Direccion')) ?? false,
                                    'value' => old(Str::snake('Direccion')) ?? $data['default']['direccion']
                                ])
                            </div>
                            @include('components.forms.string', [
                                'label' => 'Medio de Transporte',
                                'name' => Str::snake('Medio de Transporte'),
                                'error' => $errors->first(Str::snake('Medio de Transporte')) ?? false,
                                'value' => old(Str::snake('Medio de Transporte')) ?? $data['default']['medio_de_transporte']
                            ])
                            @include('components.forms.string', [
                                'label' => 'Tiempo de Demora',
                                'name' => Str::snake('Tiempo de demora'),
                                'error' => $errors->first(Str::snake('Tiempo de demora')) ?? false,
                                'value' => old(Str::snake('Tiempo de demora')) ?? $data['default']['tiempo_de_demora'],
                                'required' => false
                            ])
                        </div>
                    </div>

                    <div class="flex justify-between gap-3 mt-6">
                        <button type="button" onclick="switchTab('personal')" 
                            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Anterior
                        </button>
                        <button type="button" onclick="switchTab('vivienda')" 
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @include('components.forms.string', [
                                'label' => 'Material Vivienda',
                                'name' => Str::snake('Material Vivienda'),
                                'error' => $errors->first(Str::snake('Material Vivienda')) ?? false,
                                'value' => old(Str::snake('Material Vivienda')) ?? $data['default']['material_vivienda']
                            ])
                            @include('components.forms.string', [
                                'label' => 'Energía Eléctrica',
                                'name' => Str::snake('Energia Electrica'),
                                'error' => $errors->first(Str::snake('Energia Electrica')) ?? false,
                                'value' => old(Str::snake('Energia Electrica')) ?? $data['default']['energia_electrica']
                            ])
                            @include('components.forms.string', [
                                'label' => 'Agua Potable',
                                'name' => Str::snake('Agua Potable'),
                                'error' => $errors->first(Str::snake('Agua Potable')) ?? false,
                                'value' => old(Str::snake('Agua Potable')) ?? $data['default']['agua_potable'],
                                'required' => false
                            ])
                            @include('components.forms.string', [
                                'label' => 'Desagüe',
                                'name' => Str::snake('Desague'),
                                'error' => $errors->first(Str::snake('Desague')) ?? false,
                                'value' => old(Str::snake('Desague')) ?? $data['default']['desague'],
                                'required' => false
                            ])
                            @include('components.forms.string', [
                                'label' => 'SS.HH.',
                                'name' => Str::snake('SS_HH'),
                                'error' => $errors->first(Str::snake('SS_HH')) ?? false,
                                'value' => old(Str::snake('SS_HH')) ?? $data['default']['s_s__h_h'],
                                'required' => false
                            ])
                            @include('components.forms.string', [
                                'label' => 'Número de Habitaciones',
                                'name' => Str::snake('Numero de Habitaciones'),
                                'error' => $errors->first(Str::snake('Numero de Habitaciones')) ?? false,
                                'value' => old(Str::snake('Numero de Habitaciones')) ?? $data['default']['numero_de_habitaciones'],
                                'required' => false
                            ])
                            @include('components.forms.string', [
                                'label' => 'Número de Habitantes',
                                'name' => Str::snake('Numero de Habitantes'),
                                'error' => $errors->first(Str::snake('Numero de Habitantes')) ?? false,
                                'value' => old(Str::snake('Numero de Habitantes')) ?? $data['default']['numero_de_habitantes'],
                                'required' => false
                            ])
                            @include('components.forms.string', [
                                'label' => 'Situación de Vivienda',
                                'name' => Str::snake('Situacion de vivienda'),
                                'error' => $errors->first(Str::snake('Situacion de vivienda')) ?? false,
                                'value' => old(Str::snake('Situacion de vivienda')) ?? $data['default']['situacion_de_vivienda']
                            ])
                        </div>
                    </div>

                    <div class="flex justify-between gap-3 mt-6">
                        <button type="button" onclick="switchTab('ubicacion')" 
                            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Anterior
                        </button>
                        <button type="button" onclick="switchTab('educativa')" 
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                            @include('components.forms.string', [
                                'label' => 'Parroquia de Bautizo',
                                'name' => Str::snake('Parroquia de Bautizo'),
                                'error' => $errors->first(Str::snake('Parroquia de Bautizo')) ?? false,
                                'value' => old(Str::snake('Parroquia de Bautizo')) ?? $data['default']['parroquia_de_bautizo'],
                                'required' => false
                            ])
                            @include('components.forms.string', [
                                'label' => 'Colegio de Procedencia',
                                'name' => Str::snake('Colegio de Procedencia'),
                                'error' => $errors->first(Str::snake('Colegio de Procedencia')) ?? false,
                                'value' => old(Str::snake('Colegio de Procedencia')) ?? $data['default']['colegio_de_procedencia'],
                                'required' => false
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
                    Cancelar
                </a>
                <a href="{{ route('alumno_add_familiares', ['id' => $data['id']]) }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                    </svg>
                    Asignar Familiar
                </a>
                <input form="form" type="submit"
                    class="cursor-pointer inline-flex items-center gap-2 rounded-lg border border-green-300 bg-green-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:border-green-600 dark:bg-green-600 dark:hover:bg-green-700"
                    value="💾 Guardar Cambios"
                >
            </div>
        </form>
    </div>
@endsection

@section('custom-js')
    <script src="{{ asset('js/tables.js') }}"></script>
    <script>
        // Función para previsualizar imagen
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                handleImageFile(file);
            }
        }

        function handleImageFile(file) {
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('preview-foto');
                    const placeholder = document.getElementById('camera-placeholder');
                    const removeBtn = document.getElementById('remove-photo-btn');
                    
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                    removeBtn.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        function removePhoto() {
            const preview = document.getElementById('preview-foto');
            const placeholder = document.getElementById('camera-placeholder');
            const removeBtn = document.getElementById('remove-photo-btn');
            const fileInput = document.getElementById('foto');
            
            preview.src = '';
            preview.classList.add('hidden');
            placeholder.classList.remove('hidden');
            removeBtn.classList.add('hidden');
            fileInput.value = '';
        }

        // Función para cambiar entre pestañas
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

        // Función para actualizar la posición de la barra deslizante
        function updateSlider(button) {
            const slider = document.getElementById('tab-slider');
            const buttonRect = button.getBoundingClientRect();
            const containerRect = button.parentElement.getBoundingClientRect();
            
            const left = buttonRect.left - containerRect.left;
            const width = buttonRect.width;
            
            slider.style.left = left + 'px';
            slider.style.width = width + 'px';
        }

        // Inicializar al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar la posición de la barra deslizante
            const firstTab = document.querySelector('[data-tab="basica"]');
            if (firstTab) {
                setTimeout(() => updateSlider(firstTab), 100);
            }

            // Drag and drop para foto
            const dropZone = document.getElementById('drop-zone');
            const fileInput = document.getElementById('foto');
            const dragOverlay = document.getElementById('drag-overlay');

            if (dropZone && fileInput) {
                dropZone.addEventListener('click', function(e) {
                    if (e.target.id !== 'remove-photo-btn' && !e.target.closest('#remove-photo-btn')) {
                        fileInput.click();
                    }
                });

                dropZone.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dragOverlay.classList.remove('hidden');
                }, false);

                dropZone.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dragOverlay.classList.add('hidden');
                }, false);

                dropZone.addEventListener('drop', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dragOverlay.classList.add('hidden');
                    
                    const dt = e.dataTransfer;
                    const files = dt.files;

                    if (files.length > 0) {
                        const file = files[0];
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        fileInput.files = dataTransfer.files;
                        
                        handleImageFile(file);
                    }
                }, false);
            }
            
            // Si hay errores de validación, mostrar la pestaña correspondiente
            @if($errors->any())
                const errorFields = @json($errors->keys());
                
                // Mapear campos a pestañas
                const tabMapping = {
                    'codigo_educando': 'basica',
                    'codigo_modular': 'basica',
                    'año_de_ingreso': 'basica',
                    'd_n_i': 'basica',
                    'apellido_paterno': 'personal',
                    'apellido_materno': 'personal',
                    'primer_nombre': 'personal',
                    'otros_nombres': 'personal',
                    'sexo': 'personal',
                    'fecha_nacimiento': 'personal',
                    'telefono': 'personal',
                    'escala': 'personal',
                    'lengua_materna': 'personal',
                    'estado_civil': 'personal',
                    'religion': 'personal',
                    'fecha_bautizo': 'personal',
                    'pais': 'ubicacion',
                    'departamento': 'ubicacion',
                    'provincia': 'ubicacion',
                    'distrito': 'ubicacion',
                    'direccion': 'ubicacion',
                    'medio_de_transporte': 'ubicacion',
                    'tiempo_de_demora': 'ubicacion',
                    'material_vivienda': 'vivienda',
                    'energia_electrica': 'vivienda',
                    'agua_potable': 'vivienda',
                    'desague': 'vivienda',
                    's_s__h_h': 'vivienda',
                    'numero_de_habitaciones': 'vivienda',
                    'numero_de_habitantes': 'vivienda',
                    'situacion_de_vivienda': 'vivienda',
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
        });
    </script>
@endsection
