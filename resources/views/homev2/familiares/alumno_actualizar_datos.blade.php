<div class="p-8 m-4 bg-gray-100 dark:bg-white/[0.03] rounded-2xl">
    <!-- Header -->
    <div class="flex pb-6 justify-between items-center border-b border-gray-200 dark:border-gray-700">
        <div>
            <h2 class="text-2xl font-bold dark:text-gray-200 text-gray-800">Actualizar Datos del Alumno</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Actualice la información personal y de vivienda</p>
        </div>
        <div class="flex gap-3">
            <input form="form-datos" type="submit"
                class="cursor-pointer inline-flex items-center gap-2 rounded-lg border border-green-300 bg-green-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:border-green-600 dark:bg-green-600 dark:hover:bg-green-700"
                value="💾 Guardar Cambios"
            >
            <a href="{{ $data['return'] }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
            >
                Volver
            </a>
        </div>
    </div>

    <form method="POST" id="form-datos" action="{{ route('familiar_alumno_guardar_datos') }}" enctype="multipart/form-data" class="mt-8">
        @csrf

        <!-- Input hidden para marcar eliminación de foto -->
        <input type="hidden" name="remove_photo" id="remove_photo" value="0">

        <!-- Foto del Alumno -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Foto del Alumno
            </h3>
            <div class="flex items-center gap-6 bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4">
                <!-- Foto actual con botón de eliminar -->
                <div class="flex-shrink-0 relative" id="foto-container">
                    <img
                        id="preview-foto"
                        src="{{ $data['alumno']->foto_url }}"
                        alt="Foto de {{ $data['alumno']->primer_nombre }}"
                        class="w-32 h-32 rounded-full object-cover border-4 border-gray-200 dark:border-gray-600"
                    >
                    @if($data['alumno']->foto)
                    <button
                        type="button"
                        id="btn-remove-foto"
                        onclick="removePhoto()"
                        class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg transition-colors"
                        title="Eliminar foto"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    @endif
                </div>

                <!-- Input para cambiar foto -->
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Cambiar Foto
                    </label>
                    <div class="relative">
                        <input
                            type="file"
                            name="foto"
                            id="foto"
                            accept=".jpg,.jpeg,.png"
                            class="peer absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                            onchange="previewImage(this)"
                        >
                        <div class="w-full max-w-md border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 bg-white dark:bg-gray-800 flex items-center justify-between peer-focus:ring-2 peer-focus:ring-purple-500 peer-focus:border-purple-500 hover:border-purple-400 transition-colors">
                            <div class="flex items-center gap-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <div>
                                    <span id="foto_filename" class="text-gray-600 dark:text-gray-400 text-sm">Seleccionar imagen...</span>
                                    <p class="text-xs text-gray-500 mt-0.5">JPG o PNG. Máximo 2MB.</p>
                                </div>
                            </div>
                            <span class="text-purple-500 text-sm font-medium">Examinar</span>
                        </div>
                    </div>
                    @error('foto')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <!-- Mensaje de foto eliminada -->
                    <p id="foto-removed-msg" class="text-orange-500 text-sm mt-2 hidden">
                        La foto será eliminada al guardar los cambios.
                    </p>
                </div>
            </div>
        </div>

        <!-- Información Personal -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Información Personal
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @include('components.forms.string-ineditable-real', [
                    'label' => 'Apellido Paterno',
                    'name' => 'apellido_paterno',
                    'error' => $errors->first('apellido_paterno') ?? false,
                    'value' => old('apellido_paterno', $data['alumno']->apellido_paterno)
                ])
                @include('components.forms.string-ineditable-real', [
                    'label' => 'Apellido Materno',
                    'name' => 'apellido_materno',
                    'error' => $errors->first('apellido_materno') ?? false,
                    'value' => old('apellido_materno', $data['alumno']->apellido_materno)
                ])
                @include('components.forms.string-ineditable-real', [
                    'label' => 'Primer Nombre',
                    'name' => 'primer_nombre',
                    'error' => $errors->first('primer_nombre') ?? false,
                    'value' => old('primer_nombre', $data['alumno']->primer_nombre)
                ])
                @include('components.forms.string-ineditable-real', [
                    'label' => 'Otros Nombres',
                    'name' => 'otros_nombres',
                    'error' => $errors->first('otros_nombres') ?? false,
                    'value' => old('otros_nombres', $data['alumno']->otros_nombres)
                ])
            </div>
        </div>

        <!-- Ubicación Geográfica -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Ubicación Geográfica
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @include('components.forms.combo_dependient', [
                    'label' => 'Departamento',
                    'name' => 'departamento',
                    'error' => $errors->first('departamento') ?? false,
                    'placeholder' => 'Seleccionar departamento...',
                    'value' => old('departamento', $data['alumno']->departamento),
                    'value_field' => 'id_departamento',
                    'text_field' => 'descripcion',
                    'options' => $data['departamentos'],
                    'enabled' => true,
                ])
                @include('components.forms.combo_dependient', [
                    'label' => 'Provincia',
                    'name' => 'provincia',
                    'error' => $errors->first('provincia') ?? false,
                    'value' => old('provincia', $data['alumno']->provincia),
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
                    'error' => $errors->first('distrito') ?? false,
                    'value' => old('distrito', $data['alumno']->distrito),
                    'placeholder' => 'Seleccionar distrito...',
                    'depends_on' => 'provincia',
                    'parent_field' => 'id_provincia',
                    'value_field' => 'id_distrito',
                    'text_field' => 'descripcion',
                    'options' => $data['distritos'],
                    'enabled' => false,
                ])
                @include('components.forms.string', [
                    'label' => 'Teléfono',
                    'name' => 'telefono',
                    'error' => $errors->first('telefono') ?? false,
                    'value' => old('telefono', $data['alumno']->telefono)
                ])
            </div>
            <div class="grid grid-cols-1 gap-6 mt-4">
                @include('components.forms.string', [
                    'label' => 'Dirección',
                    'name' => 'direccion',
                    'error' => $errors->first('direccion') ?? false,
                    'value' => old('direccion', $data['alumno']->direccion)
                ])
            </div>
        </div>

        <!-- Información Cultural y Religiosa -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                Información Cultural y Religiosa
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @include('components.forms.combo', [
                    'label' => 'Lengua Materna',
                    'name' => 'lengua_materna',
                    'error' => $errors->first('lengua_materna') ?? false,
                    'value' => old('lengua_materna', $data['alumno']->lengua_materna),
                    'options' => $data['lenguasmaternas'],
                    'options_attributes' => ['id', 'descripcion']
                ])
                @include('components.forms.combo', [
                    'label' => 'Estado Civil',
                    'name' => 'estado_civil',
                    'error' => $errors->first('estado_civil') ?? false,
                    'value' => old('estado_civil', $data['alumno']->estado_civil),
                    'options' => $data['estadosciviles'],
                    'options_attributes' => ['id', 'descripcion']
                ])
                @include('components.forms.string', [
                    'label' => 'Religión',
                    'name' => 'religion',
                    'error' => $errors->first('religion') ?? false,
                    'value' => old('religion', $data['alumno']->religion)
                ])
                @include('components.forms.string', [
                    'label' => 'Parroquia de Bautizo',
                    'name' => 'parroquia_bautizo',
                    'error' => $errors->first('parroquia_bautizo') ?? false,
                    'value' => old('parroquia_bautizo', $data['alumno']->parroquia_bautizo)
                ])
            </div>
        </div>

        <!-- Información de Transporte -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
                Información de Transporte
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @include('components.forms.string', [
                    'label' => 'Medio de Transporte',
                    'name' => 'medio_transporte',
                    'error' => $errors->first('medio_transporte') ?? false,
                    'value' => old('medio_transporte', $data['alumno']->medio_transporte)
                ])
                @include('components.forms.string', [
                    'label' => 'Tiempo de Demora',
                    'name' => 'tiempo_demora',
                    'error' => $errors->first('tiempo_demora') ?? false,
                    'value' => old('tiempo_demora', $data['alumno']->tiempo_demora)
                ])
            </div>
        </div>

        <!-- Información de Vivienda -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Información de Vivienda
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @include('components.forms.string', [
                    'label' => 'Material Vivienda',
                    'name' => 'material_vivienda',
                    'error' => $errors->first('material_vivienda') ?? false,
                    'value' => old('material_vivienda', $data['alumno']->material_vivienda)
                ])
                @include('components.forms.string', [
                    'label' => 'Energía Eléctrica',
                    'name' => 'energia_electrica',
                    'error' => $errors->first('energia_electrica') ?? false,
                    'value' => old('energia_electrica', $data['alumno']->energia_electrica)
                ])
                @include('components.forms.string', [
                    'label' => 'Agua Potable',
                    'name' => 'agua_potable',
                    'error' => $errors->first('agua_potable') ?? false,
                    'value' => old('agua_potable', $data['alumno']->agua_potable)
                ])
                @include('components.forms.string', [
                    'label' => 'Desagüe',
                    'name' => 'desague',
                    'error' => $errors->first('desague') ?? false,
                    'value' => old('desague', $data['alumno']->desague)
                ])
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-4">
                @include('components.forms.string', [
                    'label' => 'SS.HH.',
                    'name' => 'ss_hh',
                    'error' => $errors->first('ss_hh') ?? false,
                    'value' => old('ss_hh', $data['alumno']->ss_hh)
                ])
                @include('components.forms.string', [
                    'label' => 'Número de Habitantes',
                    'name' => 'num_habitantes',
                    'error' => $errors->first('num_habitantes') ?? false,
                    'value' => old('num_habitantes', $data['alumno']->num_habitantes)
                ])
                @include('components.forms.string', [
                    'label' => 'Situación de Vivienda',
                    'name' => 'situacion_vivienda',
                    'error' => $errors->first('situacion_vivienda') ?? false,
                    'value' => old('situacion_vivienda', $data['alumno']->situacion_vivienda)
                ])
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ $data['return'] }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
            <input form="form-datos" type="submit"
                class="cursor-pointer inline-flex items-center gap-2 rounded-lg border border-green-300 bg-green-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:border-green-600 dark:bg-green-600 dark:hover:bg-green-700"
                value="💾 Guardar Cambios"
            >
        </div>
    </form>

    <!-- Sección de Reubicación de Escala -->
    <div class="mt-10 pt-8 border-t border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Solicitar Reubicación de Escala
        </h3>

        <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700 rounded-lg p-4 mb-4">
            <p class="text-sm text-orange-700 dark:text-orange-300">
                <strong>Escala actual:</strong> {{ $data['alumno']->escala ?? 'A' }}
            </p>
            <p class="text-sm text-orange-600 dark:text-orange-400 mt-1">
                Para solicitar un cambio de escala, debe adjuntar su clasificación socioeconómica SISFOH actualizada.
            </p>
        </div>

        @if($data['solicitud_pendiente'])
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 class="font-semibold text-yellow-800 dark:text-yellow-200">Solicitud Pendiente</h4>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                            Ya tiene una solicitud de reubicación pendiente de revisión.
                            <br>Escala solicitada: <strong>{{ $data['solicitud_pendiente']->escala_solicitada }}</strong>
                            <br>Fecha de solicitud: {{ $data['solicitud_pendiente']->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>
        @else
            <form method="POST" action="{{ route('familiar_alumno_solicitar_reubicacion') }}" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    @include('components.forms.combo', [
                        'label' => 'Escala Solicitada',
                        'name' => 'escala_solicitada',
                        'error' => $errors->first('escala_solicitada') ?? false,
                        'value' => old('escala_solicitada'),
                        'options' => $data['escalas'],
                        'options_attributes' => ['id', 'descripcion']
                    ])
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Archivo SISFOH <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="file" name="archivo_sisfoh" id="archivo_sisfoh" accept=".pdf,.jpg,.jpeg,.png" required
                                class="peer absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                onchange="if(this.files.length > 0) { document.getElementById('archivo_sisfoh_filename').innerText = this.files[0].name; document.getElementById('archivo_sisfoh_filename').classList.remove('text-gray-500'); document.getElementById('archivo_sisfoh_filename').classList.add('text-gray-900', 'dark:text-white'); }">
                            <div class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-800 flex items-center justify-between peer-focus:ring-2 peer-focus:ring-blue-500 peer-focus:border-blue-500">
                                <span id="archivo_sisfoh_filename" class="text-gray-500 dark:text-gray-400 truncate">Seleccionar archivo...</span>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">PDF, JPG o PNG. Máximo 5MB.</p>
                        @error('archivo_sisfoh')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Justificación <span class="text-red-500">*</span>
                    </label>
                    <textarea name="justificacion" rows="3" required placeholder="Explique brevemente el motivo de su solicitud..."
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('justificacion') }}</textarea>
                    @error('justificacion')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg border border-orange-300 bg-orange-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-orange-600 dark:border-orange-600 dark:bg-orange-600 dark:hover:bg-orange-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Enviar Solicitud de Reubicación
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>

<script>
        const defaultPhotoUrl = "{{ asset('images/default-avatar.png') }}";

        function previewImage(input) {
            const preview = document.getElementById('preview-foto');
            const filename = document.getElementById('foto_filename');
            const removePhotoInput = document.getElementById('remove_photo');
            const removedMsg = document.getElementById('foto-removed-msg');
            const btnRemove = document.getElementById('btn-remove-foto');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                }

                reader.readAsDataURL(input.files[0]);

                filename.innerText = input.files[0].name;
                filename.classList.remove('text-gray-600');
                filename.classList.add('text-gray-900', 'dark:text-white');

                // Resetear el estado de eliminación si se selecciona una nueva foto
                removePhotoInput.value = '0';
                removedMsg.classList.add('hidden');

                // Mostrar botón de eliminar si estaba oculto
                if (btnRemove) {
                    btnRemove.classList.remove('hidden');
                }
            }
        }

        function removePhoto() {
            const preview = document.getElementById('preview-foto');
            const removePhotoInput = document.getElementById('remove_photo');
            const removedMsg = document.getElementById('foto-removed-msg');
            const btnRemove = document.getElementById('btn-remove-foto');
            const fileInput = document.getElementById('foto');
            const filename = document.getElementById('foto_filename');

            // Marcar para eliminación
            removePhotoInput.value = '1';

            // Mostrar imagen por defecto
            preview.src = defaultPhotoUrl;

            // Ocultar botón de eliminar
            if (btnRemove) {
                btnRemove.classList.add('hidden');
            }

            // Limpiar input de archivo
            fileInput.value = '';
            filename.innerText = 'Seleccionar imagen...';
            filename.classList.remove('text-gray-900', 'dark:text-white');
            filename.classList.add('text-gray-600');

            // Mostrar mensaje de confirmación
            removedMsg.classList.remove('hidden');
        }
</script>

@section('custom-js')
    <script src="{{ asset('js/tables.js') }}"></script>

@endsection
