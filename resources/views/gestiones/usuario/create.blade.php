@extends('base.administrativo.blank')

@section('titulo')
    Crear un usuario
@endsection

@section('contenido')
    <div class="p-8 m-4 bg-gray-100 dark:bg-white/[0.03] rounded-2xl">
        <!-- Header -->
        <div class="flex pb-6 justify-between items-center border-b border-gray-200 dark:border-gray-700">
            <div>
                <h2 class="text-2xl font-bold dark:text-gray-200 text-gray-800">Nuevo Usuario</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Registra un nuevo usuario en el sistema</p>
            </div>
            <div class="flex gap-3">
                <input form="form" type="submit"
                    class="cursor-pointer inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700"
                    value="Crear Usuario"
                >

                <a href="{{ $data['return'] }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    Cancelar
                </a>
            </div>
        </div>

        <form method="POST" id="form" action="{{ route('usuario_createNewEntry') }}" enctype="multipart/form-data" class="mt-8">
            @method('PUT')
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Columna de Foto -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-4">Fotografía del Usuario</h3>

                        <div class="flex flex-col items-center">
                            <!-- Drop Zone -->
                            <div id="drop-zone" class="relative w-60 h-72 mb-4 bg-white dark:bg-gray-900 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg transition-colors hover:border-blue-400 dark:hover:border-blue-500 cursor-pointer">
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

                <!-- Columna de Datos del Usuario -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-6">Datos de Identificación</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @include('components.forms.string', [
                                'label' => 'Nombre de Usuario',
                                'name' => 'username',
                                'error' => $errors->first('username') ?? false,
                                'value' => old('username')
                            ])

                            @include('components.forms.combo', [
                                'label' => 'Tipo de Usuario',
                                'name' => 'tipo',
                                'options' => $data['tipos'],
                                'options_attributes' => ['id', 'descripcion'],
                                'error' => $errors->first('tipo') ?? false,
                                'value' => old('tipo')
                            ])
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            @include('components.forms.password', [
                                'label' => 'Contraseña',
                                'name' => 'password',
                                'error' => $errors->first('password') ?? false
                            ])

                            @include('components.forms.password', [
                                'label' => 'Confirmar Contraseña',
                                'name' => 'password_confirmation',
                                'error' => $errors->first('password_confirmation') ?? false
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                // Validar tamaño (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('El archivo es muy grande. El tamaño máximo es 2MB.');
                    event.target.value = '';
                    return;
                }

                // Validar tipo
                if (!file.type.match('image.*')) {
                    alert('Por favor selecciona una imagen válida.');
                    event.target.value = '';
                    return;
                }

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
            const input = document.getElementById('foto');
            const preview = document.getElementById('preview-foto');
            const placeholder = document.getElementById('camera-placeholder');
            const removeBtn = document.getElementById('remove-photo-btn');

            input.value = '';
            preview.src = '';
            preview.classList.add('hidden');
            placeholder.classList.remove('hidden');
            removeBtn.classList.add('hidden');
        }

        // Drag and drop functionality
        const dropZone = document.getElementById('drop-zone');
        const dragOverlay = document.getElementById('drag-overlay');
        const fileInput = document.getElementById('foto');

        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dragOverlay.classList.remove('hidden');
        });

        dropZone.addEventListener('dragleave', () => {
            dragOverlay.classList.add('hidden');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dragOverlay.classList.add('hidden');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                previewImage({ target: fileInput });
            }
        });
    </script>
@endsection
