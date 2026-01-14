@extends('base.administrativo.blank')

@section('titulo')
    Editar una Matricula
@endsection

@section('extracss')
@endsection

@section('contenido')
    <div class="p-8 m-4 bg-gray-100 dark:bg-white/[0.03] rounded-2xl">
        <!-- Header -->
        <div class="flex pb-6 justify-between items-center border-b border-gray-200 dark:border-gray-700">
            <div>
                <h2 class="text-2xl font-bold dark:text-gray-200 text-gray-800">Editar Matrícula</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">ID: {{ $data['id'] }}</p>
            </div>
            <div class="flex gap-3">
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

        <form method="POST" id="form" action="" class="mt-8">
            @method('PATCH')
            @csrf

            <!-- Información del Estudiante -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Información del Estudiante
                </h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        @include('components.forms.combo', [
                            'label' => 'Alumno',
                            'name' => 'alumno',
                            'error' => $errors->first(Str::snake('Alumno')) ?? false,
                            'value' => old(Str::snake('Alumno')) ?? $data['default'][Str::snake('Alumno')],
                            'options' => $data['alumnos'],
                            'options_attributes' => ['id', 'nombres']
                        ])
                    </div>
                    <div>
                        @include('components.forms.combo', [
                            'label' => 'Año Escolar',
                            'name' => 'id_periodo_academico',
                            'error' => $errors->first('id_periodo_academico') ?? false,
                            'value' => old('id_periodo_academico') ?? $data['default']['id_periodo_academico'],
                            'options' => $data['periodosAcademicos'],
                            'options_attributes' => ['id', 'descripcion']
                        ])
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
                        'value' => old(Str::snake('Nivel Educativo')) ?? $data['default'][Str::snake('Nivel Educativo')],
                        'value_field' => 'id_nivel',
                        'text_field' => 'nombre_nivel',
                        'options' => $data['niveles'],
                        'enabled' => true,
                    ])

                    @include('components.forms.combo_dependient', [
                        'label' => 'Grado',
                        'name' => 'grado',
                        'error' => $errors->first(Str::snake('Grado')) ?? false,
                        'placeholder' => 'Seleccionar grado...',
                        'depends_on' => 'nivel_educativo',
                        'parent_field' => 'id_nivel',
                        'value' => old(Str::snake('Grado')) ?? $data['default'][Str::snake('Grado')],
                        'value_field' => 'id_grado',
                        'text_field' => 'nombre_grado',
                        'options' => $data['grados'],
                        'enabled' => false,
                    ])

                    @include('components.forms.combo_dependient', [
                        'label' => 'Sección',
                        'name' => 'seccion',
                        'error' => $errors->first(Str::snake('Seccion')) ?? false,
                        'value' => old(Str::snake('Seccion')) ?? $data['default'][Str::snake('Seccion')],
                        'placeholder' => 'Seleccionar sección...',
                        'depends_on' => 'grado',
                        'parent_field' => 'id_grado',
                        'value_field' => ['id_grado', 'nombreSeccion'],
                        'text_field' => 'nombreSeccion',
                        'options' => $data['secciones'],
                        'enabled' => false,
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
                            'value' => old(Str::snake('Observaciones')) ?? $data['default'][Str::snake('Observaciones')]
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

            <!-- Campos ocultos -->
            <input type="hidden" name="escala" id="escalaInput" value="">
            <input type="hidden" name="fecha_matricula" id="fecha_matriculaInput" value="{{ $data['default'][Str::snake('fecha_matricula')] }}">

            <!-- Botones de acción -->
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ $data['return'] }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancelar
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
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const gradoSelect = document.getElementById('grado');
        const seccionSelect = document.getElementById('seccion');
        // Cuando cambie el grado
        gradoSelect.addEventListener('change', function () {
            if (gradoSelect.value) {
                seccionSelect.disabled = false;
            } else {
                seccionSelect.disabled = true;
                seccionSelect.value = ''; // Opcional: limpia selección
            }
        });
    });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const alumnoInput = document.querySelector('input[name="alumno"]');
        const infoBox = document.getElementById('infoBox');
        
        // Función reutilizable para cargar datos
        function cargarInfoAlumno(alumnoId) {
            if (!alumnoId) {
                infoBox.innerHTML = `
                    <div class="text-center">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p>Selecciona un alumno para ver detalles financieros</p>
                    </div>
                `;
                return;
            }
            
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
        
        if (alumnoInput && infoBox) {
            // Al cambiar el input
            alumnoInput.addEventListener('change', function () {
                cargarInfoAlumno(alumnoInput.value);
            });
            
            // Al cargar la página si ya hay un valor
            if (alumnoInput.value) {
                cargarInfoAlumno(alumnoInput.value);
            }
        }
    });
    </script>
@endsection
