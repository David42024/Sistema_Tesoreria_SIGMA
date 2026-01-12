@extends('layout.sidebar')

@section('opciones')
    @php
        $alumno = session('alumno');
    @endphp

    @if($alumno)
        {{-- Componente de información del alumno --}}
        <li class="mb-4 px-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                {{-- Foto del alumno --}}
                <div class="flex justify-center mb-3">
                    <div class="w-24 h-24 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                        @if($alumno->foto)
                            <img src="{{ asset('storage/' . $alumno->foto) }}" alt="Foto del alumno" class="w-full h-full object-cover">
                        @else
                            <span class="text-gray-400 dark:text-gray-500 text-xs text-center px-2">Foto del<br>alumno</span>
                        @endif
                    </div>
                </div>

                {{-- Nombre del alumno --}}
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 text-center mb-2">
                    {{ $alumno->apellido_paterno }} {{ $alumno->apellido_materno }}<br>
                    {{ $alumno->primer_nombre }} {{ $alumno->otros_nombres }}
                </p>

                {{-- DNI --}}
                <p class="text-xs text-gray-600 dark:text-gray-400 text-center mb-1">
                    <span class="font-medium">DNI:</span> {{ $alumno->dni }}
                </p>

                {{-- Código Educando --}}
                <p class="text-xs text-gray-600 dark:text-gray-400 text-center mb-3">
                    <span class="font-medium">Código:</span> {{ $alumno->codigo_educando ?? 'N/A' }}
                </p>

                {{-- Grado --}}
                @php
                    $matriculaActiva = $alumno->matriculas()->where('estado', true)->orderBy('año_escolar', 'desc')->first();
                @endphp
                <p class="text-xs text-gray-600 dark:text-gray-400 text-center mb-3">
                    <span class="font-medium">Grado:</span> {{ $matriculaActiva?->grado?->nombre_grado ?? 'No Matriculado' }}
                </p>

                {{-- Sección --}}
                <p class="text-xs text-gray-600 dark:text-gray-400 text-center mb-3">
                    <span class="font-medium">Sección:</span> {{ $matriculaActiva?->nombreSeccion ?? 'No Matriculado' }}
                </p>

                {{-- Botón Ver más --}}
                <div class="flex justify-center">
                    <a href="{{ route('familiar_dato_view') }}"
                       class="inline-block px-4 py-2 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-colors">
                        Ver más
                    </a>
                </div>
            </div>
        </li>

        {{-- Componente de Matrículas --}}
        @can('access-resource', 'matriculas')
            @include('components.para-sidebar.dropdown-button', [
                'name' => 'Matrículas',
                'items' => [
                    'Ver matrículas del Alumno',
                    'Realizar Matrícula',
                ],
                'links' => [
                    'Ver matrículas del Alumno' => 'familiar_matricula_view',
                    'Realizar Matrícula' => 'familiar_matricula_prematricula_create',
                ],
                'icon' => 'birrete'
            ])
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
    @endif
@endsection

