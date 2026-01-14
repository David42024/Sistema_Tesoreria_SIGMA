@extends('base.administrativo.blank')

@section('titulo')
    {{ $data['titulo'] }}
@endsection

@section('contenido')
<div class="p-8 m-4 bg-gray-100 dark:bg-white/[0.03] rounded-2xl">
    <!-- Header con título y botón cancelar -->
    <div class="flex pb-6 justify-between items-center border-b border-gray-200 dark:border-gray-700 mb-6">
        <div>
            <h2 class="text-2xl font-bold dark:text-gray-200 text-gray-800">{{ $data['titulo'] }}</h2>
            <div class="mt-2">
                @php
                    $estadoBadge = match($data['solicitud']->estado) {
                        'pendiente' => '<span class="px-3 py-1.5 text-sm font-semibold rounded-full bg-yellow-500/20 text-yellow-700 border-2 border-dotted border-yellow-500 dark:bg-yellow-500/30 dark:text-yellow-300 dark:border-yellow-400">Pendiente</span>',
                        'en_revision' => '<span class="px-3 py-1.5 text-sm font-semibold rounded-full bg-blue-500/20 text-blue-700 border-2 border-dotted border-blue-500 dark:bg-blue-500/30 dark:text-blue-300 dark:border-blue-400">En Revisión</span>',
                        'aprobado' => '<span class="px-3 py-1.5 text-sm font-semibold rounded-full bg-green-500/20 text-green-700 border-2 border-dotted border-green-500 dark:bg-green-500/30 dark:text-green-300 dark:border-green-400">Aprobado</span>',
                        'rechazado' => '<span class="px-3 py-1.5 text-sm font-semibold rounded-full bg-red-500/20 text-red-700 border-2 border-dotted border-red-500 dark:bg-red-500/30 dark:text-red-300 dark:border-red-400">Rechazado</span>',
                        default => '<span class="px-3 py-1.5 text-sm font-semibold rounded-full bg-gray-500/20 text-gray-700 border-2 border-dotted border-gray-500 dark:bg-gray-500/30 dark:text-gray-300 dark:border-gray-400">Desconocido</span>',
                    };
                @endphp
                {!! $estadoBadge !!}
            </div>
        </div>
        <a href="{{ route('solicitudes_prematricula.index') }}" 
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            Cancelar
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg dark:bg-green-900 dark:border-green-700 dark:text-green-300">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Sección: Datos del Apoderado -->
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">Datos del Apoderado</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- DNI -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">DNI</label>
                <input type="text" readonly value="{{ $data['solicitud']->dni_apoderado }}" 
                       class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-800 dark:text-white font-medium cursor-not-allowed">
            </div>

            <!-- Nombre Completo -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nombre Completo</label>
                <input type="text" readonly value="{{ trim($data['solicitud']->primer_nombre_apoderado . ' ' . ($data['solicitud']->otros_nombres_apoderado ?? '') . ' ' . $data['solicitud']->apellido_paterno_apoderado . ' ' . $data['solicitud']->apellido_materno_apoderado) }}" 
                       class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-800 dark:text-white font-medium cursor-not-allowed">
            </div>

            <!-- Parentesco -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Parentesco</label>
                <input type="text" readonly value="{{ $data['solicitud']->parentesco }}" 
                       class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-800 dark:text-white font-medium cursor-not-allowed">
            </div>

            <!-- Teléfono -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Teléfono</label>
                <input type="text" readonly value="{{ $data['solicitud']->numero_contacto }}" 
                       class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-800 dark:text-white font-medium cursor-not-allowed">
            </div>

            <!-- Email -->
            @if($data['solicitud']->correo_electronico)
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                <input type="text" readonly value="{{ $data['solicitud']->correo_electronico }}" 
                       class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-800 dark:text-white font-medium cursor-not-allowed">
            </div>
            @endif
        </div>
    </div>

    <!-- Sección: Datos del Alumno -->
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">Datos del Alumno</h2>
        </div>

        <!-- Contenedor con foto y datos -->
        <div class="flex flex-col md:flex-row gap-6 mb-6">
            <!-- Foto del estudiante -->
            <div class="flex-shrink-0 flex flex-col items-center">
                <div class="relative w-48 rounded-xl border-4 border-gray-200 dark:border-gray-600 overflow-hidden bg-gray-100 dark:bg-gray-700 shadow-lg aspect-square">
                    @if($data['solicitud']->foto_alumno && Storage::disk('public')->exists($data['solicitud']->foto_alumno))
                        <img src="{{ asset('storage/' . $data['solicitud']->foto_alumno) }}" 
                             alt="Foto del estudiante"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-24 h-24 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    @endif
                </div>
                <p class="text-center text-xs text-gray-500 dark:text-gray-400 mt-3 font-medium">Estudiante</p>
            </div>

            <!-- Datos del alumno -->
            <div class="flex-1">
                <!-- Primera línea: DNI, Nombre Completo, Escala -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <!-- DNI -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">DNI</label>
                        <input type="text" readonly value="{{ $data['solicitud']->dni_alumno }}" 
                               class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-800 dark:text-white font-medium cursor-not-allowed text-sm">
                    </div>

                    <!-- Nombre Completo -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Nombre Completo</label>
                        <input type="text" readonly value="{{ trim($data['solicitud']->primer_nombre_alumno . ' ' . ($data['solicitud']->otros_nombres_alumno ?? '') . ' ' . $data['solicitud']->apellido_paterno_alumno . ' ' . $data['solicitud']->apellido_materno_alumno) }}" 
                               class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-800 dark:text-white font-medium cursor-not-allowed text-sm">
                    </div>

                    <!-- Escala de Pago -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Escala</label>
                        <input type="text" readonly value="{{ $data['solicitud']->escala ?? 'N/A' }}" 
                               class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-800 dark:text-white font-medium cursor-not-allowed text-sm">
                    </div>
                </div>

                <!-- Segunda línea: Fecha de Nacimiento, Sexo -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <!-- Fecha de Nacimiento -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Fecha de Nacimiento</label>
                        <input type="text" readonly value="{{ $data['solicitud']->fecha_nacimiento->format('d/m/Y') }}" 
                               class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-800 dark:text-white font-medium cursor-not-allowed text-sm">
                    </div>

                    <!-- Sexo -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Sexo</label>
                        <input type="text" readonly value="{{ $data['solicitud']->sexo == 'M' ? 'Masculino' : 'Femenino' }}" 
                               class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-800 dark:text-white font-medium cursor-not-allowed text-sm">
                    </div>

                    <!-- Colegio de Procedencia -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Colegio de Procedencia</label>
                        <input type="text" readonly value="{{ $data['solicitud']->colegio_procedencia ?? 'N/A' }}" 
                               class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-800 dark:text-white font-medium cursor-not-allowed text-sm">
                    </div>
                </div>

                <!-- Tercera línea: Dirección -->
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Dirección</label>
                    <input type="text" readonly value="{{ $data['solicitud']->direccion_alumno ?? 'N/A' }}" 
                           class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-800 dark:text-white font-medium cursor-not-allowed text-sm">
                </div>

                <!-- Cuarta línea: Nivel Educativo, Grado, Sección -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Nivel Educativo -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Nivel Educativo</label>
                        <input type="text" readonly value="{{ strtoupper(str_replace('Educación ', '', $data['solicitud']->grado->nivelEducativo->descripcion ?? 'N/A')) }}" 
                               class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-800 dark:text-white font-medium cursor-not-allowed text-sm">
                    </div>

                    <!-- Grado -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Grado</label>
                        <input type="text" readonly value="{{ $data['solicitud']->grado->nombre_grado ?? 'N/A' }}" 
                               class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-800 dark:text-white font-medium cursor-not-allowed text-sm">
                    </div>

                    <!-- Sección -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wide">Sección Solicitada</label>
                        
                        @if($data['solicitud']->estado === 'pendiente' || $data['solicitud']->estado === 'en_revision')
                            <select name="nombreSeccion" id="seccion_select" 
                                class="w-full px-3 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-800 dark:text-white font-medium text-sm">
                                <option value="">Seleccione una sección...</option>
                                @foreach($data['secciones'] as $seccion)
                                    <option value="{{ $seccion->nombreSeccion }}" 
                                        data-vacantes="{{ $seccion->vacantes }}"
                                        data-matriculados="{{ $seccion->matriculados }}"
                                        data-capacidad="{{ $seccion->capacidad_maxima }}"
                                        title="{{ $seccion->matriculados }}/{{ $seccion->capacidad_maxima }} matriculados"
                                        @if($seccion->nombreSeccion == $data['solicitud']->nombreSeccion) selected @endif>
                                        {{ $seccion->nombreSeccion }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" readonly value="{{ $data['solicitud']->nombreSeccion ?? 'Sin asignar' }}" 
                                   class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-800 dark:text-white font-medium cursor-not-allowed text-sm">
                        @endif
                        
                        @if($errors->any())
                            <div class="mt-2">
                                @foreach($errors->all() as $error)
                                    <p class="text-sm text-red-600 dark:text-red-400 font-medium">
                                        {{ $error }}
                                    </p>
                                @endforeach
                            </div>
                        @endif
                        
                        @if(!$data['periodo_actual'])
                            <div class="mt-2">
                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-yellow-500/20 text-yellow-700 border border-yellow-500 dark:bg-yellow-500/30 dark:text-yellow-300">
                                    ⚠ No hay periodo académico activo
                                </span>
                            </div>
                        @elseif(isset($data['seccion_solicitada_info']) && $data['seccion_solicitada_info'])
                            <div class="mt-2" id="info-vacantes">
                                @if($data['seccion_solicitada_info']['tiene_espacio'])
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-green-500/20 text-green-700 border border-green-500 dark:bg-green-500/30 dark:text-green-300">
                                        ✓ {{ $data['seccion_solicitada_info']['vacantes'] }} vacantes disponibles
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-red-500/20 text-red-700 border border-red-500 dark:bg-red-500/30 dark:text-red-300">
                                        ⚠ Llena ({{ $data['seccion_solicitada_info']['matriculados'] }}/{{ $data['seccion_solicitada_info']['capacidad'] }})
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de acción -->
    @if($data['solicitud']->estado === 'pendiente' || $data['solicitud']->estado === 'en_revision')
    <form method="POST" action="{{ route('solicitudes_prematricula.aprobar', $data['solicitud']->id_solicitud) }}" id="formAprobar">
        @csrf
        <input type="hidden" name="id_periodo_academico" value="{{ $data['periodo_actual']->id_periodo_academico ?? '' }}">
        <input type="hidden" name="nombreSeccion" id="nombreSeccion_hidden">
        
        <div class="flex justify-end gap-3">
            <button type="submit"
                class="inline-flex items-center gap-2 rounded-lg bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 text-sm font-medium shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Confirmar
            </button>
            <button type="button" onclick="toggleRechazo()"
                class="inline-flex items-center gap-2 rounded-lg bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 text-sm font-medium shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Rechazar
            </button>
        </div>
    </form>

    <!-- Formulario de rechazo (oculto por defecto) -->
    <div id="formRechazo" class="hidden mt-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4">
        <form method="POST" action="{{ route('solicitudes_prematricula.rechazar', $data['solicitud']->id_solicitud) }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Motivo del Rechazo <span class="text-red-500">*</span>
                </label>
                <select name="motivo_rechazo" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">Seleccione un motivo...</option>
                    <option value="Documentación incompleta">Documentación incompleta</option>
                    <option value="Datos incorrectos o inconsistentes">Datos incorrectos o inconsistentes</option>
                    <option value="No cumple con requisitos de edad">No cumple con requisitos de edad</option>
                    <option value="Sección solicitada sin vacantes">Sección solicitada sin vacantes</option>
                    <option value="Periodo de prematrícula cerrado">Periodo de prematrícula cerrado</option>
                    <option value="Otro motivo">Otro motivo</option>
                </select>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="toggleRechazo()" 
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    Cancelar
                </button>
                <button type="submit" 
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                    Confirmar Rechazo
                </button>
            </div>
        </form>
    </div>
    @endif
</div>

<script>
    // Actualizar información de vacantes al seleccionar una sección
    document.getElementById('seccion_select')?.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const infoVacantes = document.getElementById('info-vacantes');
        const hiddenInput = document.getElementById('nombreSeccion_hidden');
        
        // Actualizar el valor del campo oculto
        if (hiddenInput) {
            hiddenInput.value = this.value;
        }
        
        if (selected.value && infoVacantes) {
            const vacantes = selected.dataset.vacantes;
            const matriculados = selected.dataset.matriculados;
            const capacidad = selected.dataset.capacidad;
            
            if (parseInt(vacantes) > 0) {
                infoVacantes.innerHTML = `<span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-green-500/20 text-green-700 border border-green-500 dark:bg-green-500/30 dark:text-green-300">
                    ✓ ${vacantes} vacantes disponibles (${matriculados}/${capacidad} matriculados)
                </span>`;
            } else {
                infoVacantes.innerHTML = `<span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-red-500/20 text-red-700 border border-red-500 dark:bg-red-500/30 dark:text-red-300">
                    ⚠ Llena (${matriculados}/${capacidad})
                </span>`;
            }
        }
    });

    // Inicializar el valor del campo oculto
    const seccionSelect = document.getElementById('seccion_select');
    const hiddenInput = document.getElementById('nombreSeccion_hidden');
    if (seccionSelect && hiddenInput) {
        hiddenInput.value = seccionSelect.value;
    }

    // Toggle formulario de rechazo
    function toggleRechazo() {
        const formRechazo = document.getElementById('formRechazo');
        formRechazo.classList.toggle('hidden');
    }
</script>
@endsection