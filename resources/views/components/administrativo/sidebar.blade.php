@extends('layout.sidebar')

@section('opciones')

    {{-- === OPCIONES PARA PRE-APODERADO === --}}
    @if(Auth::user()->tipo === 'PreApoderado')
        <li>
            <a href="{{ route('pre_apoderado.estado_solicitud') }}"
                class="group relative flex items-center gap-2.5 rounded-lg px-4 py-2 font-medium text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800
                {{ request()->routeIs('pre_apoderado.estado_solicitud') ? 'bg-gray-100 dark:bg-gray-800' : '' }}">
                <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6 2C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8.41421C20 7.88378 19.7893 7.37507 19.4142 7L15 2.58579C14.6249 2.21071 14.1162 2 13.5858 2H6ZM13 4H6V20H18V9H14C13.4477 9 13 8.55228 13 8V4ZM15 5.41421L17.5858 8H15V5.41421ZM8 12C8 11.4477 8.44772 11 9 11H15C15.5523 11 16 11.4477 16 12C16 12.5523 15.5523 13 15 13H9C8.44772 13 8 12.5523 8 12ZM9 15C8.44772 15 8 15.4477 8 16C8 16.5523 8.44772 17 9 17H15C15.5523 17 16 16.5523 16 16C16 15.4477 15.5523 15 15 15H9Z" fill=""/>
                </svg>
                Estado de Solicitud
            </a>
        </li>
    @endif

    {{-- Gestión Tutor (Solo para Familiar) --}}
    @can('access-resource', 'cambiar_password')
        @include('components.para-sidebar.dropdown-button', [
            'name' => 'Gestión Tutor',
            'items' => [
                'Cambiar Contraseña',
            ],
            'links' => [
                'Cambiar Contraseña' => 'familiar_cambiar_password_view'
            ],
            'icon' => 'persona'
        ])
    @endcan

    {{-- Gestión Académica --}}
    @can('access-resource', 'academica')
        @include('components.para-sidebar.dropdown-button', [
            'name' => 'Gestión Académica',
            'items' => [
                'Niveles Educativos',
                'Grados',
                'Cursos',
                'Secciones',
                'Cátedras',
            ],
            'links' => [
                'Niveles Educativos' => 'nivel_educativo_view',
                'Cursos' => 'curso_view',
                'Grados' => 'grado_view',
                'Secciones' => 'seccion_view',
                'Cátedras' => 'catedra_view'
            ],
            'icon' => 'birrete'
        ])
    @endcan

    {{-- Gestión de Alumnos --}}
    @can('access-resource', 'alumnos')
        @include('components.para-sidebar.dropdown-button', [
            'name' => 'Gestión de Alumnos',
            'items' => [
                'Alumnos',
                'Matrículas',
                'Solicitudes de Prematrícula',
                'Familiares',
                'Asignar Familiar-Alumno',
                'Generar Solicitud Traslado',

            ],
            'links' => [
                'Alumnos' => 'alumno_view',
                'Matrículas' => 'matricula_view',
                'Solicitudes de Prematrícula' => 'solicitudes_prematricula.index',
                'Familiares' => 'familiar_view',
                'Asignar Familiar-Alumno' => 'composicion_familiar_view',
                'Generar Solicitud Traslado' => 'traslado_view',
            ],
            'icon' => 'persona'
        ])
    @endcan

    {{-- Gestión de Personal --}}
    @can('access-resource', 'personal')
        @include('components.para-sidebar.dropdown-button', [
            'name' => 'Gestión de Personal',
            'items' => [
                'Docentes',
                'Departamentos Académicos',
            ],
            'links' => [
                'Departamentos Académicos' => 'departamento_academico_view',
                'Docentes' => 'docente_view'
            ],
            'icon' => 'persona-corbata'
        ])
    @endcan

    {{-- Gestión Administrativa --}}
    @can('access-resource', 'administrativa')
        @include('components.para-sidebar.dropdown-button', [
            'name' => 'Gestión Administrativa',
            'items' => [
                'Usuarios',
                'Administrativos',
                'Validación de Pago',
                'Historial de Acciones',
            ],
            'links' => [
                'Usuarios' => 'usuario_view',
                'Administrativos' => 'administrativo_view',
                'Historial de Acciones' => 'historial_de_acciones_view',
                'Validación de Pago' => 'validacion_pago_view',
            ],
            'icon' => 'maletin'
        ])
    @endcan

    {{-- Gestión Financiera --}}
    @can('access-resource', 'financiera')
        @include('components.para-sidebar.dropdown-button', [
            'name' => 'Gestión Financiera',
            'items' => [
                'Conceptos de pago',
                'Pagos',
                'Deudas',
                'Orden de Pago'
            ],
            'links' => [
                'Conceptos de pago' => 'concepto_de_pago_view',
                'Pagos' => 'pago_view',
                'Deudas' => 'deuda_view',
                'Orden de Pago' => 'orden_pago_view'
            ],
            'icon' => 'monedas'
        ])
    @endcan

    {{-- Reportes y Estadísticas --}}
    @can('access-resource', 'reportes')
        @include('components.para-sidebar.dropdown-button', [
            'name' => 'Reportes y Estadísticas',
            'items' => [
                'Reportes académicos',
                'Reportes financieros',
                'Estadísticas'
            ],
            'links' => [],
            'icon' => 'reporte'
        ])
    @endcan

    {{-- Configuración --}}
    @can('access-resource', 'configuracion')
        @include('components.para-sidebar.dropdown-button', [
            'name' => 'Configuración',
            'items' => [
                'Periodo Académico'
            ],
            'links' => [
                'Periodo Académico' => 'periodo_academico_view'
            ],
            'icon' => 'configuracion'
        ])
    @endcan

@endsection
