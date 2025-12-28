@extends('base.administrativo.blank')

@section('titulo')
  {{ $data['titulo'] }}
@endsection

@section('contenido')
<form action="{{ route('validacion_pago_guardar_validaciones', $data['pago']->id_pago) }}" method="POST" id="form-validacion">
    @csrf
    
<div class="max-w-7xl mx-auto p-6">
    <!-- Mensajes de Error/Éxito -->
    @if ($errors->any())
        <div class="mb-6 bg-red-50 dark:bg-red-900/30 border-2 border-red-400 dark:border-red-500 text-red-800 dark:text-red-100 px-5 py-4 rounded-lg">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 flex-shrink-0 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1">
                    <h3 class="font-semibold mb-2">Se encontraron los siguientes errores:</h3>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/30 border-2 border-red-400 dark:border-red-500 text-red-800 dark:text-red-100 px-5 py-4 rounded-lg">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 flex-shrink-0 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="font-semibold">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $data['titulo'] }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    Revisa y valida los detalles del pago
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <!-- Estado de IA -->
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium
                    {{ $data['ia_activa'] ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400' }}">
                    <div class="w-2 h-2 rounded-full {{ $data['ia_activa'] ? 'bg-green-500' : 'bg-gray-400' }}"></div>
                    IA {{ $data['ia_activa'] ? 'Activada' : 'Desactivada' }}
                </span>
                
                <!-- Botón Guardar Cambios (Submit del formulario) -->
                <button type="submit" 
                        id="btn-guardar-cambios"
                        disabled
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg transition-colors font-medium opacity-50 cursor-not-allowed">
                    <span>Guardar Cambios</span>
                </button>
                
                <!-- Botón volver -->
                <a href="{{ route('validacion_pago_view') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Cancelar
                </a>
            </div>
        </div>
    </div>

    <!-- Datos del Pago -->
    <div class="rounded-lg mb-4">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Datos del Pago
            </h2>
        </div>
        
        <div class="px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Fecha de Pago
                    </label>
                    <div class="flex items-center rounded-lg border border-gray-300 dark:border-gray-600 focus-within:ring-2 focus-within:ring-indigo-500">
                        <input type="text" 
                               value="{{ $data['pago']->fecha_pago->format('d/m/Y') }}" 
                               readonly
                               class="flex-1 px-3 py-2 bg-gray-50 dark:bg-gray-900 rounded-l-lg text-sm text-gray-900 dark:text-white border-0 focus:outline-none">
                        <span class="px-3 py-2 bg-gray-50 dark:bg-gray-900 border-l border-gray-300 dark:border-gray-600 flex items-center justify-center rounded-r-lg">
                            <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Concepto de Pago
                    </label>
                    <input type="text" 
                           value="{{ $data['concepto'] }}" 
                           readonly
                           class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Monto Total
                    </label>
                    <input type="text" 
                           value="S/ {{ number_format($data['pago']->monto, 2) }}" 
                           readonly
                           class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white font-semibold">
                </div>
            </div>
            
            @if($data['pago']->observaciones)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Observaciones
                    </label>
                    <textarea readonly
                              rows="2"
                              class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white resize-none">{{ $data['pago']->observaciones }}</textarea>
                </div>
            @endif
        </div>
    </div>

    <!-- Datos del Estudiante -->
    <div class="rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Datos del Estudiante
            </h2>
        </div>
        
        <div class="px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nombre Completo
                    </label>
                    <input type="text" 
                           value="{{ $data['alumno'] }}" 
                           readonly
                           class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        DNI
                    </label>
                    <input type="text" 
                           value="{{ $data['alumno_obj']->dni ?? 'Sin DNI' }}" 
                           readonly
                           class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Dirección
                    </label>
                    <input type="text" 
                           value="{{ $data['alumno_obj']->direccion ?? 'Sin dirección' }}" 
                           readonly
                           class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white">
                </div>
            </div>
        </div>
    </div>

    <!-- Detalles de Pago -->
    @if($data['pago']->detallesPago->count() > 0)
        <div class="max-w-full mx-auto">
        @foreach($data['pago']->detallesPago as $index => $detalle)
            @php
                // Obtener el id_detalle directamente de los atributos
                $idDetalle = $detalle->attributes['id_detalle'] ?? $detalle->id_detalle;
                $estadoActual = old("validaciones.{$idDetalle}", $detalle->estado_validacion);
            @endphp
            
            <div class="border rounded-lg p-6 mb-6 bg-gray-50 dark:bg-gray-800/40 w-full">
                <!-- Input hidden para enviar al controlador - siempre presente -->
                <input type="hidden" 
                       name="validaciones[{{ $idDetalle }}]" 
                       id="input-validacion-{{ $idDetalle }}"
                       value="{{ $estadoActual }}">
                
                <div class="pb-4 mb-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Detalle de Pago #{{ $index + 1 }}
                        </h2>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
                        <!-- Información del Detalle -->
                        <div class="space-y-4 flex flex-col h-full">
                            <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-4">
                                Información del Pago
                            </h3>
                            
                            <!-- Estado de Validación -->
                            <p class="text-xs font-semibold mb-2 px-4 py-2 rounded-lg text-center shadow-sm 
                                @if($estadoActual === 'validado')
                                    bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                @elseif($estadoActual === 'rechazado')
                                    bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                @else
                                    bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                @endif">
                                {{ strtoupper($estadoActual ?? 'pendiente') }}
                            </p>
                            
                            <!-- Método de pago -->
                            <div class="flex flex-col mb-3">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                    Método de Pago
                                </label>
                                <input disabled
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm px-3 py-2.5 text-gray-800 dark:text-white/90"
                                        value="{{ ucfirst($detalle->metodo_pago ?? 'No especificado') }}">  
                            </div>
                            
                            <!-- Número de operación -->
                            <div class="flex flex-col mb-3">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                    Nro de operación / recibo
                                </label>
                                <input type="text" 
                                       value="{{ $detalle->nro_recibo ?? 'Sin número' }}" 
                                       readonly
                                       class="w-full rounded-lg border border-gray-300 dark:border-gray-700 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90 bg-white dark:bg-gray-800">
                            </div>
                            
                            <!-- Monto -->
                            <div class="flex flex-col mb-3">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                    Monto
                                </label>
                                <input type="text" 
                                       value="S/ {{ number_format($detalle->monto, 2) }}" 
                                       readonly
                                       class="w-full rounded-lg border border-gray-300 dark:border-gray-700 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90 bg-white dark:bg-gray-800">
                            </div>
                            
                            <!-- Fecha -->
                            <div class="flex flex-col mb-3">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                    Fecha de Pago
                                </label>
                                <div class="flex items-center rounded-lg border border-gray-300 dark:border-gray-700 focus-within:ring-2 focus-within:ring-indigo-500">
                                    <input type="date" 
                                           value="{{ \Carbon\Carbon::parse($detalle->fecha_pago)->format('Y-m-d') }}" 
                                           readonly
                                           class="w-full px-3 py-2.5 text-sm text-gray-800 dark:text-white/90 focus:outline-none rounded-l-lg bg-white dark:bg-gray-800">
                                    
                                    <!-- Ícono calendario -->
                                    <span class="px-3 py-2.5 bg-gray-100 dark:bg-gray-800 border-l border-gray-300 dark:border-gray-700 flex items-center justify-center rounded-r-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            
                            @if($detalle->observacion)
                                <div class="flex flex-col mb-3">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                        Observación
                                    </label>
                                    <textarea readonly
                                              rows="3"
                                              class="w-full rounded-lg border border-gray-300 dark:border-gray-700 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90 bg-white dark:bg-gray-800 resize-none">{{ $detalle->observacion }}</textarea>
                                </div>
                            @endif
                            
                            <!-- Validación con IA - Solo para pagos con voucher/constancia -->
                            @if($data['ia_activa'] && $detalle->voucher_path && $detalle->estado_validacion === 'pendiente')
                                <div class="pt-4 border-t border-gray-200 dark:border-gray-600">
                                    <button onclick="validarConIA({{ $idDetalle }}, {{ $detalle->id_pago }})"
                                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                            id="btn-validar-ia-{{ $idDetalle }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                        </svg>
                                        <span class="btn-text-ia">Validar con IA</span>
                                    </button>
                                </div>
                            @endif
                            
                            @if($detalle->validado_por_ia && $detalle->voucher_path && $detalle->estado_validacion !== 'pendiente')
                                <div class="pt-4 border-t border-gray-200 dark:border-gray-600">
                                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                                        <div class="flex items-center gap-2 mb-3">
                                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                            </svg>
                                            <h4 class="text-sm font-semibold text-purple-900 dark:text-purple-200">Análisis con IA</h4>
                                        </div>
                                        
                                        <div class="space-y-3">
                                            <!-- Confianza -->
                                            <div>
                                                <label class="block text-xs font-medium text-purple-700 dark:text-purple-300 mb-1">Confianza</label>
                                                <div class="flex items-center gap-2">
                                                    <div class="flex-1 bg-purple-200 dark:bg-purple-800 rounded-full h-2">
                                                        <div class="bg-purple-600 dark:bg-purple-400 h-2 rounded-full" style="width: {{ $detalle->porcentaje_confianza }}%"></div>
                                                    </div>
                                                    <span class="text-sm font-semibold text-purple-900 dark:text-purple-200">
                                                        {{ round($detalle->porcentaje_confianza, 1) }}%
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <!-- Razón del Análisis -->
                                            @if($detalle->razon_ia)
                                                <div>
                                                    <label class="block text-xs font-medium text-purple-700 dark:text-purple-300 mb-1">
                                                        {{ $detalle->estado_validacion === 'rechazado' ? 'Razón del Rechazo' : 'Razón de la Validación' }}
                                                    </label>
                                                    <div class="bg-white dark:bg-gray-800 rounded-md p-3 border border-purple-200 dark:border-purple-700">
                                                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $detalle->razon_ia }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            <!-- Botón Ver Texto Extraído -->
                                            @if($detalle->voucher_texto)
                                                <div>
                                                    <button type="button" 
                                                            onclick="toggleTextoExtraido({{ $idDetalle }})"
                                                            class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium text-purple-700 dark:text-purple-300 bg-white dark:bg-gray-800 border border-purple-200 dark:border-purple-700 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                                                        <span>Ver Texto Extraído (OCR)</span>
                                                        <svg id="icon-texto-{{ $idDetalle }}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                        </svg>
                                                    </button>
                                                    
                                                    <div id="texto-extraido-{{ $idDetalle }}" class="hidden mt-2 bg-white dark:bg-gray-800 rounded-md p-3 border border-purple-200 dark:border-purple-700 max-h-48 overflow-y-auto">
                                                        <p class="text-xs text-gray-700 dark:text-gray-300 whitespace-pre-wrap font-mono">{{ $detalle->voucher_texto }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Constancia de Pago -->
                        <div class="space-y-4 flex flex-col h-full">
                            <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-4">
                                Constancia de Pago
                            </h3>
                            
                            @if($detalle->voucher_path)
                                @php
                                    $extension = pathinfo($detalle->voucher_path, PATHINFO_EXTENSION);
                                    $isPdf = strtolower($extension) === 'pdf';
                                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png']);
                                @endphp
                                
                                <div class="border-2 border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden bg-gray-50 dark:bg-gray-900">
                                    @if($isPdf)
                                        <div class="relative">
                                            <iframe src="{{ asset('storage/' . $detalle->voucher_path) }}" 
                                                    class="w-full h-[500px] border-0"
                                                    frameborder="0">
                                            </iframe>
                                            
                                            <div class="absolute top-4 right-4">
                                                <a href="{{ asset('storage/' . $detalle->voucher_path) }}" 
                                                   target="_blank"
                                                   class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg shadow-lg hover:bg-red-700 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                                    </svg>
                                                    Ver PDF Completo
                                                </a>
                                            </div>
                                        </div>
                                    @elseif($isImage)
                                        <div class="relative group">
                                            <img src="{{ asset('storage/' . $detalle->voucher_path) }}" 
                                                 alt="Constancia de pago"
                                                 class="w-full h-auto max-h-[500px] object-contain">
                                            
                                            <div class="absolute top-4 right-4">
                                                <a href="{{ asset('storage/' . $detalle->voucher_path) }}" 
                                                   target="_blank"
                                                   class="inline-flex items-center gap-2 px-4 py-2 bg-white text-gray-900 rounded-lg shadow-lg hover:bg-gray-100 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                                    </svg>
                                                    Ampliar Imagen
                                                </a>
                                            </div>
                                        </div>
                                    @else
                                        <div class="p-8 text-center">
                                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Formato no soportado para preview</p>
                                            <a href="{{ asset('storage/' . $detalle->voucher_path) }}" 
                                               target="_blank"
                                               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                Descargar Archivo
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center bg-gray-50 dark:bg-gray-900">
                                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Sin constancia de pago</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">No se requiere voucher para este método de pago</p>
                                </div>
                            @endif
                            
                            @if($estadoActual !== 'validado' && $estadoActual !== 'rechazado')
                                <div class="grid grid-cols-2 gap-4 pt-4">
                                    <button onclick="validarDetalle({{ $idDetalle }}, 'validado')"
                                            class="inline-flex items-center justify-center gap-2 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium"
                                            id="btn-validar-{{ $idDetalle }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="btn-text-validar">Validar Pago</span>
                                    </button>
                                    
                                    <button onclick="validarDetalle({{ $idDetalle }}, 'rechazado')"
                                            class="inline-flex items-center justify-center gap-2 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium"
                                            id="btn-rechazar-{{ $idDetalle }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="btn-text-rechazar">Rechazar Pago</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        </div>
    @else
        <div class="rounded-lg">
            <div class="p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-gray-500 dark:text-gray-400">No hay detalles de pago registrados</p>
            </div>
        </div>
    @endif
</div>
</form>
@endsection

@section('custom-js')
    <script>
        function mostrarNotificacion(tipo, mensaje) {
            const icono = tipo === 'validado' 
                ? '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                : '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
            
            const colorClasses = tipo === 'validado'
                ? 'bg-green-50 dark:bg-green-900/70 border-green-400 dark:border-green-500 text-green-800 dark:text-green-100'
                : 'bg-red-50 dark:bg-red-900/70 border-red-400 dark:border-red-500 text-red-800 dark:text-red-100';
            
            const iconColor = tipo === 'validado' ? 'text-green-600 dark:text-green-300' : 'text-red-600 dark:text-red-300';
            
            let contenedor = document.getElementById('notificaciones-container');
            if (!contenedor) {
                contenedor = document.createElement('div');
                contenedor.id = 'notificaciones-container';
                contenedor.className = 'fixed top-20 right-6 z-50 flex flex-col gap-3';
                document.body.appendChild(contenedor);
            }
            
            const notificacion = document.createElement('div');
            notificacion.className = `flex items-center gap-3 px-5 py-4 rounded-lg border-2 shadow-xl ${colorClasses} transform transition-all duration-300 translate-x-full min-w-[300px]`;
            notificacion.innerHTML = `
                <div class="${iconColor}">
                    ${icono}
                </div>
                <span class="font-semibold flex-1">${mensaje}</span>
                <button onclick="this.parentElement.remove()" class="ml-2 ${iconColor} hover:opacity-75 transition-opacity">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            `;
            
            contenedor.appendChild(notificacion);
            
            setTimeout(() => {
                notificacion.classList.remove('translate-x-full');
                notificacion.classList.add('translate-x-0');
            }, 10);

            setTimeout(() => {
                notificacion.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    notificacion.remove();
                    if (contenedor.children.length === 0) {
                        contenedor.remove();
                    }
                }, 300);
            }, 3000);
        }

        function validarDetalle(idDetalle, accion) {
            // Actualizar el input hidden que ya existe
            const input = document.getElementById(`input-validacion-${idDetalle}`);
            if (input) {
                input.value = accion;
            }
            
            const btnValidar = document.getElementById(`btn-validar-${idDetalle}`);
            const btnRechazar = document.getElementById(`btn-rechazar-${idDetalle}`);
            
            if (!btnValidar || !btnRechazar) {
                console.error('No se encontraron los botones');
                return;
            }

            const detalleContainer = btnValidar.closest('.border.rounded-lg.p-6');
            
            if (!detalleContainer) {
                console.error('No se encontró el contenedor del detalle');
                return;
            }
            
            const estadoElement = detalleContainer.querySelector('p.text-xs.font-semibold');
            
            if (estadoElement) {
                estadoElement.className = 'text-xs font-semibold mb-2 px-4 py-2 rounded-lg text-center shadow-sm';
                
                if (accion === 'validado') {
                    estadoElement.classList.add('bg-green-100', 'text-green-800', 'dark:bg-green-900/30', 'dark:text-green-400');
                    estadoElement.textContent = 'VALIDADO';
                    mostrarNotificacion('validado', '✓ Pago Validado');
                } else {
                    estadoElement.classList.add('bg-red-100', 'text-red-800', 'dark:bg-red-900/30', 'dark:text-red-400');
                    estadoElement.textContent = 'RECHAZADO';
                    mostrarNotificacion('rechazado', '✗ Pago Rechazado');
                }
            }

            if (accion === 'validado') {
                btnValidar.disabled = true;
                btnValidar.classList.add('opacity-50', 'cursor-not-allowed');
                btnValidar.classList.remove('hover:bg-green-700');
                
                btnRechazar.disabled = false;
                btnRechazar.classList.remove('opacity-50', 'cursor-not-allowed');
                btnRechazar.classList.add('hover:bg-red-700');
            } else {
                btnRechazar.disabled = true;
                btnRechazar.classList.add('opacity-50', 'cursor-not-allowed');
                btnRechazar.classList.remove('hover:bg-red-700');
                
                btnValidar.disabled = false;
                btnValidar.classList.remove('opacity-50', 'cursor-not-allowed');
                btnValidar.classList.add('hover:bg-green-700');
            }
            
            // Verificar si todos los detalles están validados/rechazados
            verificarYHabilitarGuardar();
        }

        function verificarYHabilitarGuardar() {
            // Obtener todos los inputs hidden de validaciones
            const form = document.getElementById('form-validacion');
            const inputs = form.querySelectorAll('input[name^="validaciones["]');
            let todosValidados = true;
            
            inputs.forEach(input => {
                if (input.value === 'pendiente') {
                    todosValidados = false;
                }
            });
            
            const btnGuardar = document.getElementById('btn-guardar-cambios');
            if (btnGuardar) {
                btnGuardar.disabled = !todosValidados;
                
                if (todosValidados) {
                    btnGuardar.classList.remove('opacity-50', 'cursor-not-allowed');
                    btnGuardar.classList.add('hover:bg-gray-700');
                } else {
                    btnGuardar.classList.add('opacity-50', 'cursor-not-allowed');
                    btnGuardar.classList.remove('hover:bg-gray-700');
                }
            }
        }

        function validarConIA(idDetalle, idPago) {
            const btn = document.getElementById(`btn-validar-ia-${idDetalle}`);
            const btnText = btn.querySelector('.btn-text-ia');
            const originalText = btnText.textContent;

            btn.disabled = true;
            btnText.textContent = 'Procesando...';
            
            const currentPath = window.location.pathname;
            const pathParts = currentPath.split('/');
            const validacionIndex = pathParts.indexOf('validacion_pago');
            const basePath = pathParts.slice(0, validacionIndex + 1).join('/');
            
            fetch(`${basePath}/procesar-ia/${idPago}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ 
                    id_detalle: idDetalle 
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Ocultar el botón de validar con IA
                    btn.style.display = 'none';
                    
                    // Crear el div de resultados de IA con botones de acción
                    const resultadosDiv = crearDivResultadosIA(idDetalle, idPago, data);
                    
                    // Insertar el div después del área de observaciones
                    const contenedorDetalle = btn.closest('.space-y-4');
                    const separador = contenedorDetalle.querySelector('.border-t.border-gray-200');
                    if (separador && separador.parentElement) {
                        separador.parentElement.insertBefore(resultadosDiv, separador);
                    } else {
                        btn.parentElement.parentElement.insertBefore(resultadosDiv, btn.parentElement);
                    }
                } else {
                    alert('❌ Error al procesar con IA: ' + (data.message || 'Error desconocido'));
                    btn.disabled = false;
                    btnText.textContent = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btn.disabled = false;
                btnText.textContent = originalText;
            });
        }

        // Función para mostrar/ocultar texto extraído
        function toggleTextoExtraido(idDetalle) {
            const textoDiv = document.getElementById(`texto-extraido-${idDetalle}`);
            const icon = document.getElementById(`icon-texto-${idDetalle}`);
            
            if (textoDiv.classList.contains('hidden')) {
                textoDiv.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                textoDiv.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }

        // Función para crear el div de resultados de IA dinámicamente
        function crearDivResultadosIA(idDetalle, idPago, data) {
            const div = document.createElement('div');
            div.className = 'pt-4 border-t border-gray-200 dark:border-gray-600';
            div.id = `resultados-ia-${idDetalle}`;
            
            const porcentaje = data.porcentaje || 0;
            const recomendacion = data.recomendacion || 'pendiente';
            const razon = data.razon || 'Sin razón especificada';
            const textoExtraido = data.texto_extraido || '';
            
            const tituloRazon = recomendacion === 'rechazado' ? 'Razón del Rechazo' : 'Razón de la Validación';
            const iconoRecomendacion = recomendacion === 'validado' ? '✅' : '❌';
            const textoRecomendacion = recomendacion === 'validado' ? 'VALIDAR' : 'RECHAZAR';
            
            div.innerHTML = `
                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        <h4 class="text-sm font-semibold text-purple-900 dark:text-purple-200">Análisis con IA</h4>
                    </div>
                    
                    <div class="space-y-3">
                        <!-- Recomendación de la IA -->
                        <div class="bg-gradient-to-r ${recomendacion === 'validado' ? 'from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20' : 'from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20'} rounded-lg p-3 border-2 ${recomendacion === 'validado' ? 'border-green-300 dark:border-green-700' : 'border-red-300 dark:border-red-700'}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-xs font-medium ${recomendacion === 'validado' ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300'} block mb-1">
                                        Recomendación de la IA
                                    </span>
                                    <p class="text-base font-bold ${recomendacion === 'validado' ? 'text-green-900 dark:text-green-100' : 'text-red-900 dark:text-red-100'}">
                                        ${textoRecomendacion}
                                    </p>
                                </div>
                                <span class="text-xl ml-2">${iconoRecomendacion}</span>
                            </div>
                        </div>
                        
                        <!-- Confianza -->
                        <div>
                            <label class="block text-xs font-medium text-purple-700 dark:text-purple-300 mb-1">Confianza</label>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-purple-200 dark:bg-purple-800 rounded-full h-2">
                                    <div class="bg-purple-600 dark:bg-purple-400 h-2 rounded-full" style="width: ${porcentaje}%"></div>
                                </div>
                                <span class="text-sm font-semibold text-purple-900 dark:text-purple-200">
                                    ${porcentaje}%
                                </span>
                            </div>
                        </div>
                        
                        <!-- Razón del Análisis -->
                        <div>
                            <label class="block text-xs font-medium text-purple-700 dark:text-purple-300 mb-1">
                                ${tituloRazon}
                            </label>
                            <div class="bg-white dark:bg-gray-800 rounded-md p-3 border border-purple-200 dark:border-purple-700">
                                <p class="text-sm text-gray-700 dark:text-gray-300">${razon}</p>
                            </div>
                        </div>
                        
                        <!-- Botones de Acción -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2" id="botones-accion-ia-${idDetalle}">
                            <button type="button" 
                                    onclick="aceptarRecomendacionIA(${idDetalle}, '${recomendacion}', ${porcentaje})"
                                    class="flex items-center justify-center gap-2 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium text-sm shadow-md hover:shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Aceptar Recomendación</span>
                            </button>
                            
                            <button type="button" 
                                    onclick="rechazarRecomendacionIA(${idDetalle})"
                                    class="flex items-center justify-center gap-2 px-4 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium text-sm shadow-md hover:shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                <span>Validar Manualmente</span>
                            </button>
                            
                            <button type="button" 
                                    onclick="volverAnalizarIA(${idDetalle}, ${idPago})"
                                    class="sm:col-span-2 flex items-center justify-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium text-sm shadow-md hover:shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <span>Volver a Analizar con IA</span>
                            </button>
                        </div>
                        
                        <!-- Botón Ver Texto Extraído -->
                        ${textoExtraido ? `
                        <div>
                            <button type="button" 
                                    onclick="toggleTextoExtraido(${idDetalle})"
                                    class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium text-purple-700 dark:text-purple-300 bg-white dark:bg-gray-800 border border-purple-200 dark:border-purple-700 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                                <span>Ver Texto Extraído (OCR)</span>
                                <svg id="icon-texto-${idDetalle}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            
                            <div id="texto-extraido-${idDetalle}" class="hidden mt-2 bg-white dark:bg-gray-800 rounded-md p-3 border border-purple-200 dark:border-purple-700 max-h-48 overflow-y-auto">
                                <p class="text-xs text-gray-700 dark:text-gray-300 whitespace-pre-wrap font-mono">${textoExtraido}</p>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                </div>
            `;
            
            return div;
        }

        // Función para aceptar la recomendación de la IA
        function aceptarRecomendacionIA(idDetalle, recomendacion, porcentaje) {
            // Aplicar la recomendación
            validarDetalle(idDetalle, recomendacion);
            
            // Actualizar badge con porcentaje
            const badge = document.getElementById(`badge-${idDetalle}`);
            if (badge) {
                badge.textContent = `${recomendacion.toUpperCase()} (IA: ${porcentaje}%)`;
                badge.className = recomendacion === 'validado' 
                    ? 'inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                    : 'inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
            }
            
            // Ocultar botones de acción
            const botonesAccion = document.getElementById(`botones-accion-ia-${idDetalle}`);
            if (botonesAccion) {
                botonesAccion.innerHTML = `
                    <div class="sm:col-span-2 bg-green-100 dark:bg-green-900/30 rounded-lg p-3 text-center">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">
                            ✅ Recomendación aplicada. Recuerda guardar los cambios.
                        </p>
                    </div>
                `;
            }
        }

        // Función para rechazar la recomendación (validar manualmente)
        function rechazarRecomendacionIA(idDetalle) {
            // Remover el div de resultados de IA
            const resultadosDiv = document.getElementById(`resultados-ia-${idDetalle}`);
            if (resultadosDiv) {
                resultadosDiv.remove();
            }
            
            // Mostrar nuevamente el botón de "Validar con IA" y restaurar su texto
            const btnValidarIA = document.getElementById(`btn-validar-ia-${idDetalle}`);
            if (btnValidarIA) {
                btnValidarIA.style.display = '';
                btnValidarIA.disabled = false;
                
                // Restaurar el texto original del botón
                const btnText = btnValidarIA.querySelector('.btn-text-ia');
                if (btnText) {
                    btnText.textContent = 'Validar con IA';
                }
            }
            
            // Mostrar notificación temporal
            const contenedorDetalle = btnValidarIA ? btnValidarIA.closest('.space-y-4') : null;
            if (contenedorDetalle) {
                const notificacion = document.createElement('div');
                notificacion.className = 'bg-blue-100 dark:bg-blue-900/30 rounded-lg p-3 mb-4 animate-pulse';
                notificacion.innerHTML = `
                    <p class="text-sm font-medium text-blue-800 dark:text-blue-200 text-center">
                        ℹ️ Recomendación ignorada. Puedes validar manualmente o volver a usar la IA.
                    </p>
                `;
                
                const separador = contenedorDetalle.querySelector('.border-t.border-gray-200');
                if (separador) {
                    separador.parentElement.insertBefore(notificacion, separador);
                } else {
                    contenedorDetalle.insertBefore(notificacion, contenedorDetalle.firstChild);
                }
                
                // Remover notificación después de 3 segundos
                setTimeout(() => {
                    notificacion.remove();
                }, 3000);
            }
        }

        // Función para volver a analizar con IA
        function volverAnalizarIA(idDetalle, idPago) {
            // Remover el div de resultados actual
            const resultadosDiv = document.getElementById(`resultados-ia-${idDetalle}`);
            if (resultadosDiv) {
                resultadosDiv.remove();
            }
            
            // Mostrar nuevamente el botón de "Validar con IA", restaurar texto y ejecutarlo
            const btnValidarIA = document.getElementById(`btn-validar-ia-${idDetalle}`);
            if (btnValidarIA) {
                btnValidarIA.style.display = '';
                btnValidarIA.disabled = false;
                
                // Restaurar el texto original del botón
                const btnText = btnValidarIA.querySelector('.btn-text-ia');
                if (btnText) {
                    btnText.textContent = 'Validar con IA';
                }
                
                // Ejecutar validación con IA directamente
                validarConIA(idDetalle, idPago);
            }
        }

        // Inicializar estados de botones según old() después de un error de validación
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($data['pago']->detallesPago as $detalle)
                @php
                    $idDet = $detalle->attributes['id_detalle'] ?? $detalle->id_detalle;
                    $oldValue = old("validaciones.{$idDet}");
                @endphp
                @if($oldValue)
                    const btnValidar{{ $idDet }} = document.getElementById('btn-validar-{{ $idDet }}');
                    const btnRechazar{{ $idDet }} = document.getElementById('btn-rechazar-{{ $idDet }}');
                    
                    if (btnValidar{{ $idDet }} && btnRechazar{{ $idDet }}) {
                        @if($oldValue === 'validado')
                            btnValidar{{ $idDet }}.disabled = true;
                            btnValidar{{ $idDet }}.classList.add('opacity-50', 'cursor-not-allowed');
                            btnValidar{{ $idDet }}.classList.remove('hover:bg-green-700');
                            
                            btnRechazar{{ $idDet }}.disabled = false;
                            btnRechazar{{ $idDet }}.classList.remove('opacity-50', 'cursor-not-allowed');
                            btnRechazar{{ $idDet }}.classList.add('hover:bg-red-700');
                        @elseif($oldValue === 'rechazado')
                            btnRechazar{{ $idDet }}.disabled = true;
                            btnRechazar{{ $idDet }}.classList.add('opacity-50', 'cursor-not-allowed');
                            btnRechazar{{ $idDet }}.classList.remove('hover:bg-red-700');
                            
                            btnValidar{{ $idDet }}.disabled = false;
                            btnValidar{{ $idDet }}.classList.remove('opacity-50', 'cursor-not-allowed');
                            btnValidar{{ $idDet }}.classList.add('hover:bg-green-700');
                        @endif
                    }
                @endif
            @endforeach
        });
    </script>
@endsection