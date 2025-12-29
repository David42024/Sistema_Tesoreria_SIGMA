@extends('base.administrativo.blank')

@section('titulo')
    Docentes - {{ $data['departamento']->nombre }}
@endsection

@section('just-after-html')
    <div class="delete-modal hidden">
        @include('layout.modals.modal-01', [
            'caution_message' => '¿Estás seguro?',
            'action' => 'Estás quitando al docente del departamento',
            'columns' => [
                'DNI',
                'Nombre',
                'Cargo',
            ],
            'rows' => [
                'dni',
                'nombre',
                'cargo',
            ],
            'last_warning_message' => 'El docente será removido de este departamento.',
            'confirm_button' => 'Sí, quitar',
            'cancel_button' => 'Cancelar',
            'is_form' => true,
            'data_input_name' => 'id_personal',
            'form_action' => route('departamento_academico_quitar_docente')
        ])
    </div>
@endsection

@section('contenido')
    @if(session('success'))
        @include('layout.alerts.animated.timed-alert',[
            'message' => session('success'),
            'route' => 'layout.alerts.success' 
        ])
    @endif

    @if(session('removed'))
        @include('layout.alerts.animated.timed-alert',[
            'message' => 'Docente removido exitosamente del departamento: ' . session('removed'),
            'route' => 'layout.alerts.red-success' 
        ])
    @endif

    @if(isset($data['added']))
        @include('layout.alerts.animated.timed-alert',[
            'message' => 'Docente agregado exitosamente al departamento: ' . $data['departamento']->nombre,
            'route' => 'layout.alerts.success' 
        ])
    @endif

    @include('layout.tables.table-01', $data)
@endsection

@section('custom-js')
    <script src="{{ asset('js/tables.js') }}"></script>
    <script src="{{ asset('js/delete-button-modal.js') }}"></script>
@endsection