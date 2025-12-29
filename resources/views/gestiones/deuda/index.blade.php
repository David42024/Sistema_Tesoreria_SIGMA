@extends('base.administrativo.blank')

@section('titulo')
  {{ $data['titulo'] }}
@endsection

@section('just-after-html')
  <div class="delete-modal hidden">
    @include('layout.modals.modal-01', [
      'caution_message' => '¿Estás seguro?',
      'action' => 'Estás eliminando la deuda',
      'columns' => [
        'Periodo',
        'Alumno',
        'Concepto',
        'Monto Total',
        'Observaciones',
      ],
      'rows' => [
        'periodo',
        'id_alumno',
        'id_concepto',
        'monto_total',
        'observaciones'
      ],
      'last_warning_message' => 'Borrar esta deuda afectará los registros vinculados a esta.',
      'confirm_button' => 'Sí, bórralo',
      'cancel_button' => 'Cancelar',
      'is_form' => true,
      'data_input_name' => 'id'
    ])
  </div>
@endsection

@section('contenido')
  @if(isset($data['created']))
    @include('layout.alerts.animated.timed-alert',[
      'message' => 'La deuda ha sido registrada exitosamente.',
      'route' => 'layout.alerts.success' 
    ])
  @endif

  @if(isset($data['edited']))
    @include('layout.alerts.animated.timed-alert',[
      'message' => 'La deuda ha sido editada exitosamente.',
      'route' => 'layout.alerts.orange-success' 
    ])
  @endif

  @if(isset($data['abort']))
    @include('layout.alerts.animated.timed-alert',[
      'message' => 'La acción sobre la deuda ha sido cancelada.',
      'route' => 'layout.alerts.info' 
    ])
  @endif

  @if(isset($data['deleted']))
    @include('layout.alerts.animated.timed-alert',[
      'message' => 'La deuda ha sido eliminada exitosamente.',
      'route' => 'layout.alerts.red-success' 
    ])
  @endif

  @include('layout.tables.table-01', $data)
@endsection

@section('custom-js')
  <script src="{{ asset('js/tables.js') }}"></script>
  <script src="{{ asset('js/delete-button-modal.js') }}"></script>
@endsection
