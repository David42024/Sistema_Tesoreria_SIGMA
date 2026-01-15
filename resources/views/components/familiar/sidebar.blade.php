@extends('layout.sidebar')

@section('opciones')
    @php
        $alumno = session('alumno');
    @endphp

    @if($alumno)
        {{-- Componente de información del alumno --}}
        <li class="mb-4" :class="sidebarToggle ? 'lg:px-0' : 'px-4'">

            {{-- Vista completa (sidebar expandido) --}}
            <div class="group relative bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-900 rounded-xl p-4 border-2 border-blue-200 dark:border-gray-600 shadow-md hover:shadow-xl transition-all duration-300"
                :class="sidebarToggle ? 'lg:hidden' : ''">

                {{-- Decoración de fondo --}}
                <div
                    class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-400/10 to-indigo-400/10 rounded-full -mr-12 -mt-12">
                </div>

                <div class="relative">
                    {{-- Foto del alumno --}}
                    <div class="flex justify-center mb-3">
                        <div class="relative">
                            <div
                                class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-200 to-indigo-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center overflow-hidden ring-4 ring-white dark:ring-gray-800 shadow-lg transition-all duration-300 hover:scale-105">
                                @if($alumno->foto)
                                    <img src="{{ asset('storage/' . $alumno->foto) }}" alt="Foto del alumno"
                                        class="w-full h-full object-cover">
                                @else
                                    <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>
                            {{-- Badge de estado activo --}}
                            <div
                                class="absolute bottom-0 right-0 w-5 h-5 bg-green-500 rounded-full border-3 border-white dark:border-gray-800 shadow-md">
                            </div>
                        </div>
                    </div>

                    {{-- Nombre del alumno --}}
                    <div class="mb-3">
                        <h3 class="text-sm font-bold text-gray-800 dark:text-white text-center leading-tight">
                            {{ $alumno->primer_nombre }} {{ $alumno->otros_nombres }}
                        </h3>
                        <p class="text-xs text-gray-600 dark:text-gray-300 text-center font-medium">
                            {{ $alumno->apellido_paterno }} {{ $alumno->apellido_materno }}
                        </p>
                    </div>
                    <div class="space-y-2 mb-3">
                        {{-- DNI --}}
                        <div class="flex items-center justify-between bg-white/60 dark:bg-gray-800/60 rounded-lg px-3 py-1.5">
                            <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                DNI
                            </span>
                            <span class="text-xs font-semibold text-gray-800 dark:text-white">{{ $alumno->dni }}</span>
                        </div>

                        {{-- Código --}}
                        <div class="flex items-center justify-between bg-white/60 dark:bg-gray-800/60 rounded-lg px-3 py-1.5">
                            <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z"
                                        clip-rule="evenodd"></path>
                                    <path
                                        d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z">
                                    </path>
                                </svg>
                                Código
                            </span>
                            <span
                                class="text-xs font-semibold text-gray-800 dark:text-white">{{ $alumno->codigo_educando ?? 'N/A' }}</span>
                        </div>

                        {{-- Grado --}}
                        @php
                            $matriculaActiva = App\Models\Matricula::where('id_periodo_academico', '=', App\Services\Cronograma\CronogramaAcademicoService::periodoActual()->getKey())
                                ->where('id_alumno', '=', $alumno->getKey())
                                ->first();
                        @endphp
                        <div class="flex items-center justify-between bg-white/60 dark:bg-gray-800/60 rounded-lg px-3 py-1.5">
                            <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z">
                                    </path>
                                </svg>
                                Grado
                            </span>
                            <span
                                class="text-xs font-semibold text-gray-800 dark:text-white">{{ $matriculaActiva?->grado?->nombre_grado ?? 'N/A' }}</span>
                        </div>

                        {{-- Sección --}}
                        <div class="flex items-center justify-between bg-white/60 dark:bg-gray-800/60 rounded-lg px-3 py-1.5">
                            <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z">
                                    </path>
                                </svg>
                                Sección
                            </span>
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold
                                                     {{ $matriculaActiva?->nombreSeccion ? 'bg-blue-500 text-white' : 'bg-gray-300 text-gray-600 dark:bg-gray-600 dark:text-gray-300' }}">
                                {{ $matriculaActiva?->nombreSeccion ?? 'N/A' }}
                            </span>
                        </div>
                    </div>

                    {{-- Botón Ver más --}}
                    <div class="flex justify-center">
                        <a href="{{ route('familiar_dato_view') }}"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2 text-xs font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg hover:from-blue-700 hover:to-indigo-700 dark:from-blue-500 dark:to-indigo-500 dark:hover:from-blue-600 dark:hover:to-indigo-600 shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                            Ver más
                        </a>
                    </div>
                </div>
            </div>

            {{-- Vista colapsada (sidebar minimizado) - Solo icono con texto --}}
            <a href="{{ route('familiar_dato_view') }}" class="hidden menu-item group menu-item-inactive"
                :class="sidebarToggle ? 'lg:flex' : ''" title="Ver información del estudiante">
                <svg class="menu-item-icon-inactive" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M12 4C9.79086 4 8 5.79086 8 8C8 10.2091 9.79086 12 12 12C14.2091 12 16 10.2091 16 8C16 5.79086 14.2091 4 12 4Z"
                        stroke="currentColor" stroke-width="1.5" />
                    <path d="M6 18C6 15.7909 7.79086 14 10 14H14C16.2091 14 18 15.7909 18 18V20H6V18Z" stroke="currentColor"
                        stroke-width="1.5" />
                </svg>

                <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                    Estudiante
                </span>
            </a>
        </li>

        {{-- Componente de Matrículas --}}
        @can('access-resource', 'matriculas')
            @if(App\Services\Cronograma\CronogramaAcademicoService::preMatriculaHabilitada() && $matriculaActiva == null)
            {{-- Alumno NO está matriculado: mostrar ambas opciones --}}
            @include('components.para-sidebar.dropdown-button', [
                'name' => 'Matrículas',
                'items' => [
                    'Ver matrículas del Alumno',
                    'Realizar Prematrícula',
                ],
                'links' => [
                    'Ver matrículas del Alumno' => 'familiar_matricula_view',
                    'Realizar Prematrícula' => 'familiar_matricula_prematricula_create',
                ],
                'icon' => 'birrete'
            ])
            @else
            {{-- Alumno YA está matriculado: solo mostrar "Ver matrículas del Alumno" --}} 
            @include('components.para-sidebar.dropdown-button', [
                'name' => 'Matrículas',
                'items' => [
                    'Ver matrículas del Alumno',
                ],
                'links' => [
                    'Ver matrículas del Alumno' => 'familiar_matricula_view',
                ],
                'icon' => 'birrete'
            ])
            @endif
        @endcan

                {{-- Componente de Pagos --}}
                @can('access-resource', 'pagos')
                    @include('components.para-sidebar.dropdown-button', [
                        'name' => 'Pagos',
                        'items' => [
                            'Ver pagos anteriores',
                            'Ver deudas',
                        ],
                        'links' => [
                            'Ver pagos anteriores' => 'familiar_pago_view_pagos',
                            'Ver deudas' => 'familiar_pago_view_deudas',
                        ],
                        'icon' => 'monedas'
                    ])
                @endcan

                {{-- Componente de Traslado --}}
                @can('access-resource', 'traslado')
                    @include('components.para-sidebar.dropdown-button', [
                        'name' => 'Traslado',
                        'items' => [
                            'Traslado Regular',
                            'Traslado Excepcional',
                        ],
                        'links' => [
                            'Traslado Regular' => 'familiar_traslado_regular',
                            'Traslado Excepcional' => 'familiar_traslado_excepcional',
                        ],
                        'icon' => 'traslado'
                    ])
                @endcan
    @endif
@endsection

