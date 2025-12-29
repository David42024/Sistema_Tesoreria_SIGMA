@extends('base.administrativo.blank')

@section('titulo')
  {{ $data['titulo'] }}
@endsection

@section('just-after-html')
  <div class="delete-modal hidden">
    @include('layout.modals.modal-01', [
      'caution_message' => '¿Estás seguro?',
      'action' => 'Estás eliminando el Pago',
      'columns' => [
        'Concepto de Pago',
        'Estudiante',
        'Fecha de Pago',
        'Monto',
        'Observaciones'
      ],
      'rows' => [
        'concepto_pago',
        'estudiante',
        'fecha_pago',
        'monto',
        'observaciones'
      ],
      'last_warning_message' => 'Borrar esto afectará a todo lo que esté vinculado a este Pago',
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
      'message' => 'El pago ha sido registrado exitosamente.',
      'route' => 'layout.alerts.success' 
    ])
  @endif

  @if(isset($data['edited']))
    @include('layout.alerts.animated.timed-alert',[
      'message' => 'El pago ha sido editado exitosamente.',
      'route' => 'layout.alerts.orange-success' 
    ])
  @endif

  @if(isset($data['abort']))
    @include('layout.alerts.animated.timed-alert',[
      'message' => 'La acción sobre el pago ha sido cancelada.',
      'route' => 'layout.alerts.info' 
    ])
  @endif

  @if(isset($data['deleted']))
    @include('layout.alerts.animated.timed-alert',[
      'message' => 'El pago ha sido eliminado exitosamente.',
      'route' => 'layout.alerts.red-success' 
    ])
  @endif

  @include('layout.tables.table-01', $data)
@endsection

@section('custom-js')
  <script src="{{ asset('js/tables.js') }}"></script>
  <script src="{{ asset('js/delete-button-modal.js') }}"></script>
@endsection