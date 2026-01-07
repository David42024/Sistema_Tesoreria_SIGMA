@extends('base.administrativo.blank')

@section('titulo', 'Estado de Solicitud de Prematrícula')

@section('contenido')
<div class="p-8 m-4 bg-gray-100 dark:bg-white/[0.03] rounded-2xl">
    <!-- Header -->
    <div class="flex pb-6 justify-between items-center border-b border-gray-200 dark:border-gray-700">
        <div>
            <h2 class="text-2xl font-bold dark:text-gray-200 text-gray-800">Mis Solicitudes de Prematrícula</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Gestione sus solicitudes de prematrícula</p>
        </div>
        <a href="{{ route('pre_apoderado.nueva_solicitud') }}" 
            class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nueva Solicitud
        </a>
    </div>

    {{-- Lista de solicitudes si hay más de una --}}
    @if($solicitudes->count() > 1)
        <div class="mt-6 mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Seleccionar solicitud:</label>
            <div class="flex flex-wrap gap-2">
                @foreach($solicitudes as $sol)
                    <a href="{{ route('pre_apoderado.estado_solicitud', ['id' => $sol->id_solicitud]) }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                        {{ $solicitud->id_solicitud === $sol->id_solicitud 
                            ? 'bg-blue-600 text-white' 
                            : 'bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                        {{ $sol->nombre_completo_alumno }}
                        <span class="ml-1 text-xs opacity-75">({{ ucfirst($sol->estado) }})</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Estado actual de la solicitud seleccionada -->
    <div class="mt-6">
        <div class="flex items-center gap-2 mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                Solicitud #{{ $solicitud->id_solicitud }} - {{ $solicitud->nombre_completo_alumno }}
            </h3>
            {!! $solicitud->estado_badge !!}
        </div>

        @if($solicitud->estado === 'pendiente')
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-xl p-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-yellow-800 dark:text-yellow-200">Solicitud Pendiente</h3>
                        <p class="text-yellow-700 dark:text-yellow-300 mt-1">Su solicitud está en espera de revisión por parte de la institución.</p>
                    </div>
                </div>
            </div>
        @elseif($solicitud->estado === 'en_revision')
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-blue-800 dark:text-blue-200">En Revisión</h3>
                        <p class="text-blue-700 dark:text-blue-300 mt-1">Su solicitud está siendo revisada. Pronto recibirá una respuesta.</p>
                    </div>
                </div>
            </div>
        @elseif($solicitud->estado === 'aprobada')
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-xl p-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-green-800 dark:text-green-200">¡Solicitud Aprobada!</h3>
                        <p class="text-green-700 dark:text-green-300 mt-1">Su solicitud ha sido aprobada. Ahora puede acceder al sistema completo como apoderado.</p>
                        @if($solicitud->observaciones)
                            <p class="text-sm text-green-600 dark:text-green-400 mt-2">
                                <strong>Observaciones:</strong> {{ $solicitud->observaciones }}
                            </p>
                        @endif
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-green-200 dark:border-green-700">
                    <a href="{{ route('principal') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                        Ir al Sistema
                    </a>
                </div>
            </div>
        @elseif($solicitud->estado === 'rechazada')
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-xl p-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-red-800 dark:text-red-200">Solicitud Rechazada</h3>
                        <p class="text-red-700 dark:text-red-300 mt-1">Lamentablemente su solicitud no ha sido aprobada.</p>
                        @if($solicitud->motivo_rechazo)
                            <div class="mt-3 p-3 bg-red-100 dark:bg-red-800/30 rounded-lg">
                                <p class="text-sm text-red-800 dark:text-red-200">
                                    <strong>Motivo:</strong> {{ $solicitud->motivo_rechazo }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Información de la solicitud -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Datos del Apoderado -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Datos del Apoderado
            </h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Nombre:</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $solicitud->nombre_completo_apoderado }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">DNI:</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $solicitud->dni_apoderado }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Parentesco:</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $solicitud->parentesco }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Contacto:</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $solicitud->numero_contacto }}</dd>
                </div>
            </dl>
        </div>

        <!-- Datos del Alumno -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                Datos del Estudiante
            </h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Nombre:</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $solicitud->nombre_completo_alumno }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">DNI:</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $solicitud->dni_alumno }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Fecha de Nacimiento:</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ \Carbon\Carbon::parse($solicitud->fecha_nacimiento)->format('d/m/Y') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Grado solicitado:</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $solicitud->grado->nombre_grado ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Fecha de solicitud -->
    <div class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
        Solicitud enviada el {{ $solicitud->created_at->format('d/m/Y \a \l\a\s H:i') }}
        @if($solicitud->fecha_revision)
            <br>Revisada el {{ \Carbon\Carbon::parse($solicitud->fecha_revision)->format('d/m/Y \a \l\a\s H:i') }}
        @endif
    </div>
</div>
@endsection