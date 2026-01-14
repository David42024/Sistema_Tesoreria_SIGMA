<div class="p-8 m-4 bg-gray-100 dark:bg-white/[0.03] rounded-2xl">
    <!-- Header -->
    <div class="flex pb-6 justify-between items-center border-b border-gray-200 dark:border-gray-700">
        <div>
            <h2 class="text-2xl font-bold dark:text-gray-200 text-gray-800">Solicitar Prematrícula</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Año Escolar {{ $data['info_prematricula']['periodo']['año_escolar'] }}</p>
        </div>
        <div class="flex gap-3">
            <input form="form" type="submit"
                class="cursor-pointer inline-flex items-center gap-2 rounded-lg border border-green-300 bg-green-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:border-green-600 dark:bg-green-600 dark:hover:bg-green-700"
                value="Enviar Solicitud"
            >
            <a href="{{ $data['return'] }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
            >
                Cancelar
            </a>
        </div>
    </div>

    <form method="POST" id="form" action="{{ route('familiar_matricula_prematricula_store') }}" class="mt-8">
        @csrf

        <!-- Aviso de Alumno Nuevo y Período de Prematrícula -->
        <div class="mb-8 grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Aviso de Alumno Nuevo -->
            <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-orange-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div>
                        <h4 class="font-semibold text-orange-800 dark:text-orange-200 text-sm">Alumno sin matrícula previa</h4>
                        <p class="text-xs text-orange-700 dark:text-orange-300 mt-1">
                            Este alumno no tiene matrículas registradas. La solicitud será revisada por la institución.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Período de Prematrícula -->
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 flex items-center">
                <svg class="w-5 h-5 text-yellow-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <div>
                    <h4 class="font-semibold text-yellow-800 dark:text-yellow-200 text-sm">Período de Prematrícula</h4>
                    <p class="text-xs text-yellow-700 dark:text-yellow-300 mt-1">
                        <span class="font-medium">Fecha límite:</span> 
                        {{ \Carbon\Carbon::parse($data['info_prematricula']['periodo']['fecha_fin'])->format('d/m/Y') }}
                    </p>
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
                        <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 flex items-center justify-center mb-3">
                            @if($data['alumno']->foto && \Storage::disk('public')->exists($data['alumno']->foto))
                                <img src="{{ asset('storage/' . $data['alumno']->foto) }}" alt="Foto del estudiante" class="w-full h-full object-cover">
                            @else
                                @if($data['alumno']->sexo == 'F')
                                    <svg class="w-20 h-20 text-pink-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <svg class="w-20 h-20 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            @endif
                        </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400 text-center">Estudiante</span>
                    </div>
                    
                    {{-- Datos del estudiante --}}
                    <div class="lg:col-span-9">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-gray-500 dark:text-gray-400">Nombre completo</label>
                                <input type="text" readonly
                                    value="{{ $data['alumno']->primer_nombre }} {{ $data['alumno']->otros_nombres }} {{ $data['alumno']->apellido_paterno }} {{ $data['alumno']->apellido_materno }}"
                                    class="mt-1 w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 dark:text-gray-400">DNI</label>
                                <input type="text" readonly
                                    value="{{ $data['alumno']->dni ?? 'N/A' }}"
                                    class="mt-1 w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 dark:text-gray-400">Código educando</label>
                                <input type="text" readonly
                                    value="{{ $data['alumno']->codigo_educando ?? 'N/A' }}"
                                    class="mt-1 w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 dark:text-gray-400">Año de ingreso</label>
                                <input type="text" readonly
                                    value="{{ now()->year }}"
                                    class="mt-1 w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white">
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
                    'error' => $errors->first('nivel_educativo') ?? false,
                    'placeholder' => 'Seleccionar nivel educativo...',
                    'value' => old('nivel_educativo'),
                    'value_field' => 'id_nivel',
                    'text_field' => 'nombre_nivel',
                    'options' => $data['niveles'] ?? collect(),
                    'enabled' => true,
                    'disableSearch' => true
                ])

                @include('components.forms.combo_dependient', [
                    'label' => 'Grado',
                    'name' => 'id_grado',
                    'error' => $errors->first('id_grado') ?? false,
                    'placeholder' => 'Seleccionar grado...',
                    'depends_on' => 'nivel_educativo',
                    'parent_field' => 'id_nivel',
                    'value' => old('id_grado'),
                    'value_field' => 'id_grado',
                    'text_field' => 'nombre_grado',
                    'options' => $data['grados'] ?? collect(),
                    'enabled' => false,
                    'disableSearch' => true
                ])

                @include('components.forms.combo_dependient', [
                    'label' => 'Sección',
                    'name' => 'nombreSeccion',
                    'error' => $errors->first('nombreSeccion') ?? false,
                    'placeholder' => 'Seleccionar sección...',
                    'depends_on' => 'id_grado',
                    'parent_field' => 'id_grado',
                    'value' => old('nombreSeccion'),
                    'value_field' => 'nombreSeccion',
                    'text_field' => 'nombreSeccion',
                    'options' => $data['secciones'] ?? collect(),
                    'enabled' => false,
                    'disableSearch' => true
                ])
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                La sección final será asignada por la institución según disponibilidad.
            </p>
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
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Observaciones (Opcional)
                    </label>
                    <textarea name="observaciones" id="observaciones" rows="5"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5
                               bg-white dark:bg-gray-800 text-gray-800 dark:text-white
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                        placeholder="Ingrese información adicional relevante (colegio de procedencia, etc.)...">{{ old('observaciones') }}</textarea>
                    @error('observaciones')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <!-- Info Box del Alumno -->
                    <div class="h-full bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-5">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Información Financiera
                        </h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-600 dark:text-gray-400">Escala:</span>
                                <span class="text-sm font-semibold text-gray-800 dark:text-gray-200" id="escala_display">{{ $data['alumno']->escala ?? 'E' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-600 dark:text-gray-400">Deuda mensual:</span>
                                <span class="text-sm font-semibold text-gray-800 dark:text-gray-200" id="deuda_mensual">S/ 100.00</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-600 dark:text-gray-400">Cuotas pendientes:</span>
                                <span class="text-sm font-semibold text-gray-800 dark:text-gray-200" id="cuotas_pendientes">10</span>
                            </div>
                            <div class="flex justify-between items-center border-t-2 border-gray-300 dark:border-gray-600 pt-2 mt-2">
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Deuda total:</span>
                                <span class="text-base font-bold text-red-600 dark:text-red-400" id="deuda_total">S/ 1,000.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                value="Enviar Solicitud"
            >
        </div>
    </form>
</div>

<script>
    // ========== CÁLCULO DE DEUDA FINANCIERA ==========
    document.addEventListener('DOMContentLoaded', function() {
        const escala = '{{ $data["alumno"]->escala ?? "E" }}';
        const cuotasPendientes = 10;
        
        // Mapeo de escalas a montos
        const montosEscala = {
            'A': 500,
            'B': 400,
            'C': 300,
            'D': 200,
            'E': 100
        };
        
        const deudaMensual = montosEscala[escala] || 100;
        const deudaTotal = deudaMensual * cuotasPendientes;
        
        // Actualizar los elementos
        document.getElementById('deuda_mensual').textContent = 'S/ ' + deudaMensual.toFixed(2);
        document.getElementById('cuotas_pendientes').textContent = cuotasPendientes;
        document.getElementById('deuda_total').textContent = 'S/ ' + deudaTotal.toLocaleString('es-PE', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    });
</script>