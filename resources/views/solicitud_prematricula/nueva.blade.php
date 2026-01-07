@extends('base.administrativo.blank')

@section('titulo', 'Nueva Solicitud de Prematrícula')

@section('contenido')
<div class="p-4 md:p-8 m-4 bg-gray-100 dark:bg-white/[0.03] rounded-2xl">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row pb-6 justify-between items-start sm:items-center gap-4 border-b border-gray-200 dark:border-gray-700">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Nueva Solicitud de Prematrícula</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Registre un nuevo estudiante vinculado a su cuenta</p>
        </div>
        <a href="{{ route('pre_apoderado.estado_solicitud') }}" 
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver al listado
        </a>
    </div>

    <!-- Errores -->
    @if($errors->any())
        <div class="mt-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-xl">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium">Hay algunos problemas con los datos ingresados. Por favor, verifique el formulario.</span>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('pre_apoderado.guardar_nueva_solicitud') }}" enctype="multipart/form-data" class="mt-6 space-y-8">
        @csrf

        <!-- Sección: Datos del Apoderado -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 bg-gradient-to-r from-blue-50 to-transparent dark:from-blue-900/20 border-b border-gray-100 dark:border-gray-700">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Datos del Apoderado</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Información del responsable legal</p>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <span class="block text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Nombre Completo</span>
                        <p class="text-gray-800 dark:text-gray-200 font-medium">{{ $solicitudExistente->nombre_completo_apoderado }}</p>
                    </div>
                    <div class="space-y-1">
                        <span class="block text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">DNI</span>
                        <p class="text-gray-800 dark:text-gray-200 font-medium font-mono">{{ $solicitudExistente->dni_apoderado }}</p>
                    </div>
                    <div class="space-y-1">
                        <span class="block text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Teléfono</span>
                        <p class="text-gray-800 dark:text-gray-200 font-medium">{{ $solicitudExistente->numero_contacto }}</p>
                    </div>
                </div>
                
                <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Parentesco con el nuevo estudiante <span class="text-red-500">*</span>
                    </label>
                    <select name="parentesco" required
                        class="w-full sm:w-64 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('parentesco') border-red-500 ring-red-500 @enderror">
                        <option value="">Seleccionar...</option>
                        @foreach($parentescos as $parentesco)
                            <option value="{{ $parentesco['id'] }}" {{ old('parentesco') == $parentesco['id'] ? 'selected' : '' }}>
                                {{ $parentesco['descripcion'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('parentesco')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Sección: Datos del Estudiante -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 bg-gradient-to-r from-green-50 to-transparent dark:from-green-900/20 border-b border-gray-100 dark:border-gray-700">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/50 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Datos del Estudiante</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Información del nuevo alumno a registrar</p>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- DNI -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            DNI del Estudiante <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="dni_alumno" value="{{ old('dni_alumno') }}" 
                            maxlength="8" pattern="[0-9]{8}" required placeholder="12345678"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 dark:bg-gray-700 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('dni_alumno') border-red-500 @enderror">
                        @error('dni_alumno')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sexo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Sexo <span class="text-red-500">*</span>
                        </label>
                        <select name="sexo" required
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('sexo') border-red-500 @enderror">
                            <option value="">Seleccionar...</option>
                            @foreach($sexos as $sexo)
                                <option value="{{ $sexo['id'] }}" {{ old('sexo') == $sexo['id'] ? 'selected' : '' }}>
                                    {{ $sexo['descripcion'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('sexo')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Apellido Paterno -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Apellido Paterno <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="apellido_paterno_alumno" value="{{ old('apellido_paterno_alumno') }}" required
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('apellido_paterno_alumno') border-red-500 @enderror">
                        @error('apellido_paterno_alumno')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Apellido Materno -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Apellido Materno
                        </label>
                        <input type="text" name="apellido_materno_alumno" value="{{ old('apellido_materno_alumno') }}"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                    </div>

                    <!-- Primer Nombre -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Primer Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="primer_nombre_alumno" value="{{ old('primer_nombre_alumno') }}" required
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('primer_nombre_alumno') border-red-500 @enderror">
                        @error('primer_nombre_alumno')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Otros Nombres -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Otros Nombres
                        </label>
                        <input type="text" name="otros_nombres_alumno" value="{{ old('otros_nombres_alumno') }}"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                    </div>

                    <!-- Fecha de Nacimiento -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Fecha de Nacimiento <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" required
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('fecha_nacimiento') border-red-500 @enderror">
                        @error('fecha_nacimiento')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Grado -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Grado al que postula <span class="text-red-500">*</span>
                        </label>
                        <select name="id_grado" required
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('id_grado') border-red-500 @enderror">
                            <option value="">Seleccionar grado...</option>
                            @foreach($grados as $grado)
                                <option value="{{ $grado->id_grado }}" {{ old('id_grado') == $grado->id_grado ? 'selected' : '' }}>
                                    {{ $grado->nombre_grado }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_grado')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Colegio de Procedencia -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Colegio de Procedencia
                        </label>
                        <input type="text" name="colegio_procedencia" value="{{ old('colegio_procedencia') }}" placeholder="Nombre del colegio anterior"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 dark:bg-gray-700 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Teléfono del Estudiante
                        </label>
                        <input type="tel" name="telefono_alumno" value="{{ old('telefono_alumno') }}" placeholder="987654321"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 dark:bg-gray-700 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                    </div>

                    <!-- Dirección -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Dirección del Estudiante
                        </label>
                        <input type="text" name="direccion_alumno" value="{{ old('direccion_alumno') }}" placeholder="Av. Ejemplo 123, Distrito"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 dark:bg-gray-700 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección: Documentos -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 bg-gradient-to-r from-purple-50 to-transparent dark:from-purple-900/20 border-b border-gray-100 dark:border-gray-700">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/50 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Documentación</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Adjunte los requisitos (opcional en esta etapa)</p>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Foto -->
                    <div class="group">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Foto del Estudiante
                        </label>
                        <div class="relative">
                            <input type="file" name="foto_alumno" accept=".jpg,.jpeg,.png"
                                class="w-full border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 text-sm dark:bg-gray-700 dark:text-gray-300 
                                file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-purple-50 file:text-purple-700 
                                dark:file:bg-purple-900/30 dark:file:text-purple-400
                                hover:border-purple-400 dark:hover:border-purple-500 transition-colors cursor-pointer">
                        </div>
                        <p class="text-xs text-gray-400 mt-2">JPG o PNG. Máximo 2MB</p>
                    </div>

                    <!-- Partida -->
                    <div class="group">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Partida de Nacimiento
                        </label>
                        <div class="relative">
                            <input type="file" name="partida_nacimiento" accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 text-sm dark:bg-gray-700 dark:text-gray-300 
                                file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-purple-50 file:text-purple-700 
                                dark:file:bg-purple-900/30 dark:file:text-purple-400
                                hover:border-purple-400 dark:hover:border-purple-500 transition-colors cursor-pointer">
                        </div>
                        <p class="text-xs text-gray-400 mt-2">PDF, JPG o PNG. Máximo 5MB</p>
                    </div>

                    <!-- Certificado -->
                    <div class="group">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Certificado de Estudios
                        </label>
                        <div class="relative">
                            <input type="file" name="certificado_estudios" accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 text-sm dark:bg-gray-700 dark:text-gray-300 
                                file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-purple-50 file:text-purple-700 
                                dark:file:bg-purple-900/30 dark:file:text-purple-400
                                hover:border-purple-400 dark:hover:border-purple-500 transition-colors cursor-pointer">
                        </div>
                        <p class="text-xs text-gray-400 mt-2">PDF, JPG o PNG. Máximo 5MB</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-4">
            <a href="{{ route('pre_apoderado.estado_solicitud') }}" 
                class="w-full sm:w-auto px-6 py-2.5 text-center text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 font-medium transition-colors">
                Cancelar
            </a>
            <button type="submit" 
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                Enviar Solicitud
            </button>
        </div>
    </form>
</div>
@endsection