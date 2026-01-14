@extends('base.administrativo.blank')

@section('titulo')
  Solicitudes de Prematrícula
@endsection

@section('contenido')
  @if(session('success'))
    @include('layout.alerts.animated.timed-alert',[
      'message' => session('success'),
      'route' => 'layout.alerts.success' 
    ])
  @endif

  @if(session('error'))
    @include('layout.alerts.animated.timed-alert',[
      'message' => session('error'),
      'route' => 'layout.alerts.error' 
    ])
  @endif

  @if(session('info'))
    @include('layout.alerts.animated.timed-alert',[
      'message' => session('info'),
      'route' => 'layout.alerts.info' 
    ])
  @endif

  <style>
    /* Estilos para los badges de estado */
    table td p:is(:contains("Pendiente")) {
      display: inline-block;
      padding: 0.25rem 0.75rem;
      font-size: 0.75rem;
      font-weight: 600;
      border-radius: 9999px;
      background-color: rgb(254 249 195);
      color: rgb(161 98 7);
    }
    
    table td p:is(:contains("En Revisión")) {
      display: inline-block;
      padding: 0.25rem 0.75rem;
      font-size: 0.75rem;
      font-weight: 600;
      border-radius: 9999px;
      background-color: rgb(219 234 254);
      color: rgb(30 64 175);
    }
    
    table td p:is(:contains("Aprobado")) {
      display: inline-block;
      padding: 0.25rem 0.75rem;
      font-size: 0.75rem;
      font-weight: 600;
      border-radius: 9999px;
      background-color: rgb(220 252 231);
      color: rgb(22 101 52);
    }
    
    table td p:is(:contains("Rechazado")) {
      display: inline-block;
      padding: 0.25rem 0.75rem;
      font-size: 0.75rem;
      font-weight: 600;
      border-radius: 9999px;
      background-color: rgb(254 226 226);
      color: rgb(153 27 27);
    }
  </style>

  {!! $data['contenido'] !!}
@endsection

@section('custom-js')
  <script src="{{ asset('js/tables.js') }}"></script>
  <script src="{{ asset('js/download-button.js') }}"></script>
  
  <script>
    // Aplicar estilos a las celdas de estado con JavaScript
    document.addEventListener('DOMContentLoaded', function() {
      const rows = document.querySelectorAll('table tbody tr');
      
      rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        // La columna de estado es la octava (índice 7): ID, Alumno, DNI, Nivel, Grado, Sección, Apoderado, Estado
        if (cells.length > 7) {
          const estadoCell = cells[7].querySelector('p');
          if (estadoCell) {
            const texto = estadoCell.textContent.trim();
            
            // Limpiar clases existentes
            estadoCell.className = 'text-sm font-semibold inline-flex items-center px-2.5 py-0.5 rounded-full border-2 border-dotted';
            
            // Aplicar clases según el estado con fondo semi-transparente y borde punteado
            if (texto === 'Pendiente') {
              estadoCell.classList.add('bg-yellow-500/20', 'text-yellow-700', 'border-yellow-500', 'dark:bg-yellow-500/30', 'dark:text-yellow-300', 'dark:border-yellow-400');
            } else if (texto === 'En Revisión') {
              estadoCell.classList.add('bg-blue-500/20', 'text-blue-700', 'border-blue-500', 'dark:bg-blue-500/30', 'dark:text-blue-300', 'dark:border-blue-400');
            } else if (texto === 'Aprobado') {
              estadoCell.classList.add('bg-green-500/20', 'text-green-700', 'border-green-500', 'dark:bg-green-500/30', 'dark:text-green-300', 'dark:border-green-400');
            } else if (texto === 'Rechazado') {
              estadoCell.classList.add('bg-red-500/20', 'text-red-700', 'border-red-500', 'dark:bg-red-500/30', 'dark:text-red-300', 'dark:border-red-400');
            } else {
              estadoCell.classList.add('bg-gray-500/20', 'text-gray-700', 'border-gray-500', 'dark:bg-gray-500/30', 'dark:text-gray-300', 'dark:border-gray-400');
            }
          }
        }
      });
    });
  </script>
@endsection
