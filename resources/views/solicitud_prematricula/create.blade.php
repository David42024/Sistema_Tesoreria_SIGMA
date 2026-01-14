<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Prematrícula - Colegio Sigma</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/sigma_logo.png') }}" alt="Logo" class="h-10">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">SIGMA</h1>
                    <p class="text-xs text-gray-500">Sistema de Gestión Académica</p>
                </div>
            </div>
            <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:underline">
                ← Volver al inicio
            </a>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-8">
        <!-- Título -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Solicitud de Prematrícula</h2>
            <p class="text-gray-600 mt-2">Complete el formulario para solicitar una prematrícula para un nuevo estudiante</p>
        </div>

        <!-- Errores generales -->
        @if($errors->has('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                {{ $errors->first('error') }}
            </div>
        @endif

        <!-- Formulario -->
        <form method="POST" action="{{ route('solicitud_prematricula.store') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- ========== SECCIÓN 1: DATOS DEL APODERADO ========== -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Datos del Apoderado</h3>
                        <p class="text-sm text-gray-500">Información de la persona responsable del estudiante</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- DNI Apoderado -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            DNI <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="dni_apoderado" value="{{ old('dni_apoderado') }}" 
                            maxlength="8" pattern="[0-9]{8}" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('dni_apoderado') border-red-500 @enderror"
                            placeholder="12345678">
                        @error('dni_apoderado')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Parentesco -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Parentesco <span class="text-red-500">*</span>
                        </label>
                        <select name="parentesco" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('parentesco') border-red-500 @enderror">
                            <option value="">Seleccionar...</option>
                            @foreach($parentescos as $parentesco)
                                <option value="{{ $parentesco['id'] }}" {{ old('parentesco') == $parentesco['id'] ? 'selected' : '' }}>
                                    {{ $parentesco['descripcion'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('parentesco')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Apellido Paterno -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Apellido Paterno <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="apellido_paterno_apoderado" value="{{ old('apellido_paterno_apoderado') }}" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('apellido_paterno_apoderado') border-red-500 @enderror">
                        @error('apellido_paterno_apoderado')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Apellido Materno -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Apellido Materno
                        </label>
                        <input type="text" name="apellido_materno_apoderado" value="{{ old('apellido_materno_apoderado') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Primer Nombre -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Primer Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="primer_nombre_apoderado" value="{{ old('primer_nombre_apoderado') }}" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('primer_nombre_apoderado') border-red-500 @enderror">
                        @error('primer_nombre_apoderado')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Otros Nombres -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Otros Nombres
                        </label>
                        <input type="text" name="otros_nombres_apoderado" value="{{ old('otros_nombres_apoderado') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Número de Contacto -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Número de Contacto <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="numero_contacto" value="{{ old('numero_contacto') }}" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('numero_contacto') border-red-500 @enderror"
                            placeholder="987654321">
                        @error('numero_contacto')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Correo Electrónico -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Correo Electrónico
                        </label>
                        <input type="email" name="correo_electronico" value="{{ old('correo_electronico') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="ejemplo@correo.com">
                    </div>

                    <!-- Dirección -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Dirección
                        </label>
                        <input type="text" name="direccion_apoderado" value="{{ old('direccion_apoderado') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Av. Ejemplo 123, Distrito">
                    </div>
                </div>
            </div>

            <!-- ========== SECCIÓN 2: DATOS DEL ALUMNO ========== -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Datos del Estudiante</h3>
                        <p class="text-sm text-gray-500">Información del nuevo estudiante a matricular</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- DNI Alumno -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            DNI del Estudiante <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="dni_alumno" value="{{ old('dni_alumno') }}" 
                            maxlength="8" pattern="[0-9]{8}" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('dni_alumno') border-red-500 @enderror"
                            placeholder="12345678">
                        @error('dni_alumno')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sexo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Sexo <span class="text-red-500">*</span>
                        </label>
                        <select name="sexo" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('sexo') border-red-500 @enderror">
                            <option value="">Seleccionar...</option>
                            @foreach($sexos as $sexo)
                                <option value="{{ $sexo['id'] }}" {{ old('sexo') == $sexo['id'] ? 'selected' : '' }}>
                                    {{ $sexo['descripcion'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('sexo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Apellido Paterno -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Apellido Paterno <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="apellido_paterno_alumno" value="{{ old('apellido_paterno_alumno') }}" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('apellido_paterno_alumno') border-red-500 @enderror">
                        @error('apellido_paterno_alumno')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Apellido Materno -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Apellido Materno
                        </label>
                        <input type="text" name="apellido_materno_alumno" value="{{ old('apellido_materno_alumno') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Primer Nombre -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Primer Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="primer_nombre_alumno" value="{{ old('primer_nombre_alumno') }}" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('primer_nombre_alumno') border-red-500 @enderror">
                        @error('primer_nombre_alumno')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Otros Nombres -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Otros Nombres
                        </label>
                        <input type="text" name="otros_nombres_alumno" value="{{ old('otros_nombres_alumno') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Fecha de Nacimiento -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha de Nacimiento <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('fecha_nacimiento') border-red-500 @enderror">
                        @error('fecha_nacimiento')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Grado -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Grado al que postula <span class="text-red-500">*</span>
                        </label>
                        <select name="id_grado" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('id_grado') border-red-500 @enderror">
                            <option value="">Seleccionar grado...</option>
                            @foreach($grados as $grado)
                                <option value="{{ $grado->id_grado }}" {{ old('id_grado') == $grado->id_grado ? 'selected' : '' }}>
                                    {{ $grado->nombre_grado }} - {{ $grado->nivelEducativo->nombre_nivel ?? '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_grado')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Escala de Pago -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Escala de Pago <span class="text-red-500">*</span>
                        </label>
                        <select name="escala" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('escala') border-red-500 @enderror">
                            <option value="">Seleccionar escala...</option>
                            <option value="A" {{ old('escala') == 'A' ? 'selected' : '' }}>A</option>
                            <option value="B" {{ old('escala') == 'B' ? 'selected' : '' }}>B</option>
                            <option value="C" {{ old('escala') == 'C' ? 'selected' : '' }}>C</option>
                            <option value="D" {{ old('escala') == 'D' ? 'selected' : '' }}>D</option>
                            <option value="E" {{ old('escala') == 'E' ? 'selected' : '' }}>E</option>
                        </select>
                        @error('escala')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Teléfono del Alumno -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Teléfono del Estudiante
                        </label>
                        <input type="tel" name="telefono_alumno" value="{{ old('telefono_alumno') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Colegio de Procedencia -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Colegio de Procedencia
                        </label>
                        <input type="text" name="colegio_procedencia" value="{{ old('colegio_procedencia') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Nombre del colegio anterior">
                    </div>

                    <!-- Dirección del Alumno -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Dirección del Estudiante
                        </label>
                        <input type="text" name="direccion_alumno" value="{{ old('direccion_alumno') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- ========== SECCIÓN 3: DOCUMENTOS ========== -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Documentos</h3>
                        <p class="text-sm text-gray-500">Adjunte los documentos requeridos (opcional, puede entregarlos después)</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Foto del Alumno -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Foto del Estudiante
                        </label>
                        <input type="file" name="foto_alumno" accept=".jpg,.jpeg,.png"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-xs text-gray-500 mt-1">JPG o PNG. Máx. 2MB</p>
                    </div>

                    <!-- Partida de Nacimiento -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Partida de Nacimiento
                        </label>
                        <input type="file" name="partida_nacimiento" accept=".pdf,.jpg,.jpeg,.png"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-xs text-gray-500 mt-1">PDF, JPG o PNG. Máx. 5MB</p>
                    </div>

                    <!-- Certificado de Estudios -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Certificado de Estudios
                        </label>
                        <input type="file" name="certificado_estudios" accept=".pdf,.jpg,.jpeg,.png"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-xs text-gray-500 mt-1">PDF, JPG o PNG. Máx. 5MB</p>
                    </div>
                </div>
            </div>

            <!-- ========== AVISO IMPORTANTE ========== -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div>
                        <h4 class="font-semibold text-yellow-800">Importante</h4>
                        <p class="text-sm text-yellow-700 mt-1">
                            Al enviar esta solicitud, se le generará un <strong>usuario y contraseña</strong> para hacer seguimiento del estado de su solicitud. 
                            Ambos serán el <strong>DNI del apoderado</strong>. Guarde esta información.
                        </p>
                    </div>
                </div>
            </div>

            <!-- ========== BOTONES ========== -->
            <div class="flex justify-between items-center">
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-800">
                    ← Cancelar
                </a>
                <button type="submit" 
                    class="px-8 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-200 transition-all">
                    Enviar Solicitud
                </button>
            </div>
        </form>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12 py-6">
        <div class="max-w-4xl mx-auto px-4 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} Colegio Sigma. Todos los derechos reservados.
        </div>
    </footer>
</body>
</html>