<div class="p-8 m-4 bg-gray-100 dark:bg-white/[0.03] rounded-2xl">
    <!-- Header -->
    <div class="flex pb-6 justify-between items-center border-b border-gray-200 dark:border-gray-700">
        <div>
            <h2 class="text-2xl font-bold dark:text-gray-200 text-gray-800">Estado de Solicitud de Prematrícula</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Alumno: {{ $data['alumno']->primer_nombre }} {{ $data['alumno']->apellido_paterno }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ $data['return'] }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
            >
                Volver
            </a>
        </div>
    </div>

    <!-- Estado de la Solicitud -->
    <div class="mt-8">
        @php
            $estadoConfig = [
                'pendiente' => [
                    'color' => 'yellow',
                    'bg' => 'bg-yellow-50 dark:bg-yellow-900/20',
                    'border' => 'border-yellow-200 dark:border-yellow-700',
                    'icon_color' => 'text-yellow-500',
                    'text' => 'Solicitud Pendiente de Revisión',
                    'descripcion' => 'Su solicitud de prematrícula ha sido recibida y está en espera de ser revisada por el personal administrativo del colegio. Le notificaremos cuando sea aprobada o si requiere modificaciones.'
                ],
                'aprobado' => [
                    'color' => 'green',
                    'bg' => 'bg-green-50 dark:bg-green-900/20',
                    'border' => 'border-green-200 dark:border-green-700',
                    'icon_color' => 'text-green-500',
                    'text' => 'Solicitud Aprobada',
                    'descripcion' => '¡Felicidades! Su solicitud de prematrícula ha sido aprobada. Pronto recibirá más información sobre los siguientes pasos.'
                ],
                'rechazado' => [
                    'color' => 'red',
                    'bg' => 'bg-red-50 dark:bg-red-900/20',
                    'border' => 'border-red-200 dark:border-red-700',
                    'icon_color' => 'text-red-500',
                    'text' => 'Solicitud Rechazada',
                    'descripcion' => 'Lamentablemente su solicitud no pudo ser aprobada. Por favor, contacte con la administración para más información.'
                ],
                'en_revision' => [
                    'color' => 'blue',
                    'bg' => 'bg-blue-50 dark:bg-blue-900/20',
                    'border' => 'border-blue-200 dark:border-blue-700',
                    'icon_color' => 'text-blue-500',
                    'text' => 'Solicitud en Revisión',
                    'descripcion' => 'Su solicitud está siendo revisada activamente por el personal administrativo.'
                ],
            ];
            
            $estado = $data['solicitud']->estado;
            $config = $estadoConfig[$estado] ?? $estadoConfig['pendiente'];
        @endphp

        <!-- Card del Estado -->
        <div class="{{ $config['bg'] }} {{ $config['border'] }} border rounded-lg p-6 mb-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    @if($estado === 'pendiente')
                        <svg class="w-10 h-10 {{ $config['icon_color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @elseif($estado === 'aprobado')
                        <svg class="w-10 h-10 {{ $config['icon_color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @elseif($estado === 'rechazado')
                        <svg class="w-10 h-10 {{ $config['icon_color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @else
                        <svg class="w-10 h-10 {{ $config['icon_color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    @endif
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $config['text'] }}</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $config['descripcion'] }}</p>
                    
                    @if($estado === 'rechazado' && $data['solicitud']->motivo_rechazo)
                        <div class="mt-3 p-3 bg-white dark:bg-gray-800 rounded border border-red-300 dark:border-red-600">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Motivo del rechazo:</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $data['solicitud']->motivo_rechazo }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Detalles de la Solicitud -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Detalles de la Solicitud</h3>
            </div>
            
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Información del Alumno -->
                <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Información del Alumno</h4>
                    <dl class="space-y-2">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">DNI:</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $data['solicitud']->dni_alumno }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">Nombres:</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $data['solicitud']->primer_nombre_alumno }} {{ $data['solicitud']->otros_nombres_alumno }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">Apellidos:</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $data['solicitud']->apellido_paterno_alumno }} {{ $data['solicitud']->apellido_materno_alumno }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">Grado solicitado:</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                @php
                                    $grado = \App\Models\Grado::find($data['solicitud']->id_grado);
                                @endphp
                                {{ $grado->nombre_grado ?? 'N/A' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Información del Apoderado -->
                <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Información del Apoderado</h4>
                    <dl class="space-y-2">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">DNI:</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $data['solicitud']->dni_apoderado }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">Nombres:</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $data['solicitud']->primer_nombre_apoderado }} {{ $data['solicitud']->otros_nombres_apoderado }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">Apellidos:</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $data['solicitud']->apellido_paterno_apoderado }} {{ $data['solicitud']->apellido_materno_apoderado }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">Teléfono:</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $data['solicitud']->numero_contacto }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Fechas -->
                <div class="md:col-span-2">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Fechas</h4>
                    <dl class="grid grid-cols-2 gap-4">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">Fecha de solicitud:</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ \Carbon\Carbon::parse($data['solicitud']->created_at)->format('d/m/Y H:i') }}
                            </dd>
                        </div>
                        @if($data['solicitud']->fecha_revision)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600 dark:text-gray-400">Fecha de revisión:</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ \Carbon\Carbon::parse($data['solicitud']->fecha_revision)->format('d/m/Y H:i') }}
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>

                @if($data['solicitud']->observaciones)
                    <div class="md:col-span-2">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Observaciones</h4>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $data['solicitud']->observaciones }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
