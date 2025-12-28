@extends('base.administrativo.blank')

@section('title')
    Ingresar Pago
@endsection

@section('contenido')

    <div class="p-8 m-4 dark:bg-white/[0.03] rounded-2xl">
        <div class="flex pb-4 justify-between items-center">
            <h2 class="text-lg dark:text-gray-200 text-gray-800">
                @if(isset($data['modo_completar']) && $data['modo_completar'])
                    Estás completando el Pago
                @else
                    Estás ingresando un Pago
                @endif
            </h2>

            <div class="flex gap-4">
                <input form="form" type="submit" id="btnIngresar"
                    class="cursor-pointer inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-gray-200 px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-300 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
                    value="@if(isset($data['modo_completar']) && $data['modo_completar']) Completar Pago @else Ingresar @endif">

                <a href="{{ $data['return'] }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-gray-200 px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-300 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                    Cancelar
                </a>
            </div>
        </div>

        <form method="POST" id="form" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"
            action="@if(isset($data['modo_completar']) && $data['modo_completar']) {{ route('pago_completarPago', $data['id_pago']) }} @else {{ route('pago_createNewEntry') }} @endif"
            enctype="multipart/form-data">
            @method('PUT')
            @csrf

            {{-- Campo hidden para enviar el tipo de pago al controlador --}}
            <input type="hidden" name="tipo_pago" id="tipo_pago_hidden" value="">

            @if(!isset($data['modo_completar']) || !$data['modo_completar'])

                <!-- ========== BÚSQUEDA UNIFICADA ========== -->
                <div class="col-span-1 sm:col-span-2 lg:col-span-3 mb-6">
                    <div
                        class="bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 rounded-xl p-6 border-2 border-indigo-200 dark:border-indigo-800">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="bg-indigo-600 dark:bg-indigo-500 rounded-lg p-2">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Buscar por Código</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Ingrese el código de estudiante o código de
                                    orden de pago</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                            <div class="lg:col-span-3 flex flex-col">
                                <input type="text" id="codigo_busqueda_unificado" name="codigo_busqueda_unificado"
                                    value="{{ old('codigo_busqueda_unificado', '') }}"
                                    class="w-full rounded-lg border-2 border-indigo-300 dark:border-indigo-600 px-4 py-3 text-sm text-gray-800 dark:text-white/90 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                    placeholder="Ejemplo: 123456 (estudiante) o OP-2025-0032 (orden)">
                                <p id="helper_busqueda" class="text-xs mt-1 text-gray-500 dark:text-gray-400">
                                    💡 Tip: Puede buscar por código de estudiante (6 dígitos) o por código de orden de pago (ej:
                                    OP-2025-0032)
                                </p>
                            </div>

                            <div class="flex items-start">
                                <button type="button" id="btnBuscarUnificado"
                                    class="w-full flex items-center justify-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-md transition duration-200 transform hover:scale-105">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tipo de Pago (Solo aparece después de buscar) -->
                <div id="tipo_pago_container" class="col-span-1 sm:col-span-2 lg:col-span-3 hidden mb-4">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-2 block">
                        Tipo de Pago
                    </label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="tipo_pago_selector" value="deuda_individual"
                                class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Pago de Deuda Individual</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer" id="label_orden_completa">
                            <input type="radio" name="tipo_pago_selector" value="orden_completa"
                                class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Pago de Orden Completa</span>
                        </label>
                    </div>
                </div>
                <!-- Tipo de Pago (Solo en modo normal) -->
                <div class="col-span-1 sm:col-span-2 lg:col-span-3 flex flex-col mb-4 hidden">
                    <label for="tipo_pago_selector" class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                        Tipo de Pago (OLD)
                    </label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="tipo_pago_selector_old" value="deuda_individual"
                                class="w-4 h-4 text-indigo-600 focus:ring-indigo-500" checked>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Pago de Deuda Individual</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="tipo_pago_selector_old" value="orden_completa"
                                class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Pago de Orden Completa</span>
                        </label>
                    </div>
                </div>
            @endif

            @if(!isset($data['modo_completar']) || !$data['modo_completar'])

                        <!-- ========== SECCIÓN PARA ORDEN COMPLETA ========== -->
                        <div id="seccion_orden_completa" class="col-span-1 sm:col-span-2 lg:col-span-3 hidden">
                            <div
                                class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-2 border-blue-300 dark:border-blue-700 rounded-xl p-6 shadow-lg">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="bg-blue-600 dark:bg-blue-500 rounded-lg p-3">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                            Pago de Orden Completa
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400" id="mensaje_orden_header">
                                            Cargando información de la orden...
                                        </p>
                                    </div>
                                </div>

                                <!-- Código de Orden (OCULTO - ya no es necesario buscar de nuevo) -->
                                <div class="hidden mb-6">
                                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                                        <div class="lg:col-span-3 flex flex-col">
                                            <label for="codigo_orden"
                                                class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                Código de Orden de Pago
                                            </label>
                                            <input type="text" id="codigo_orden" name="codigo_orden"
                                                class="w-full rounded-lg border-2 border-gray-300 dark:border-gray-600 px-4 py-3 text-sm text-gray-800 dark:text-white/90 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                                placeholder="Ejemplo: OP-2025-0032">
                                        </div>

                                        <div class="flex items-end">
                                            <button type="button" id="btnBuscarOrden"
                                                class="w-full flex items-center justify-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-md transition duration-200 transform hover:scale-105">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                </svg>
                                                Buscar
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Info de la Orden (oculta inicialmente) -->
                                <div id="info_orden_container" class="hidden space-y-4">
                                    <!-- Información Básica de la Orden -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                                        <!-- Fecha de Pago -->
                                        <div class="flex flex-col">
                                            <label for="fecha_pago_orden"
                                                class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                Fecha de Pago
                                            </label>

                                            <div
                                                class="flex items-center rounded-lg border border-gray-300 dark:border-gray-700 focus-within:ring-2 focus-within:ring-indigo-500">
                                                <input type="date" id="fecha_pago_orden" name="fecha_pago"
                                                    value="{{ now()->format('Y-m-d') }}" readonly
                                                    class="w-full px-3 py-2.5 text-sm text-gray-800 dark:text-white/90 focus:outline-none rounded-l-lg bg-gray-50 dark:bg-gray-800">

                                                <span
                                                    class="px-3 py-2.5 bg-gray-100 dark:bg-gray-800 border-l border-gray-300 dark:border-gray-700 flex items-center justify-center rounded-r-lg">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-h-[20px]"></div>
                                        </div>

                                        <!-- Estudiante -->
                                        <div class="flex flex-col">
                                            <label class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                Estudiante
                                            </label>
                                            <input type="text" id="info_estudiante_orden" readonly
                                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90">
                                            <div class="min-h-[20px]"></div>
                                        </div>

                                        <!-- Código Educando -->
                                        <div class="flex flex-col">
                                            <label class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                Código Educando
                                            </label>
                                            <input type="text" id="info_codigo_estudiante_orden" readonly
                                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90">
                                            <div class="min-h-[20px]"></div>
                                        </div>
                                    </div>

                                    <!-- Segunda fila: Monto Total, Monto Pagado, Monto Pendiente -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                                        <!-- Monto Total -->
                                        <div class="flex flex-col">
                                            <label class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                Monto Total
                                            </label>
                                            <input type="text" id="info_monto_total_orden" readonly
                                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-blue-50 dark:bg-blue-900/30 px-3 py-2.5 text-sm font-bold text-blue-700 dark:text-blue-300">
                                            <div class="min-h-[20px]"></div>
                                        </div>

                                        <!-- Monto Pagado -->
                                        <div class="flex flex-col">
                                            <label class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                Monto Pagado
                                            </label>
                                            <input type="text" id="info_monto_pagado_orden" readonly
                                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-green-50 dark:bg-green-900/30 px-3 py-2.5 text-sm font-bold text-green-700 dark:text-green-300">
                                            <div class="min-h-[20px]"></div>
                                        </div>

                                        <!-- Monto Pendiente -->
                                        <div class="flex flex-col">
                                            <label class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                Monto Pendiente
                                            </label>
                                            <input type="text" id="info_monto_pendiente_orden" readonly
                                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-red-50 dark:bg-red-900/30 px-3 py-2.5 text-sm font-bold text-red-700 dark:text-red-300">
                                            <div class="min-h-[20px]"></div>
                                        </div>
                                    </div>

                                    <!-- Tabla de Deudas -->
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5 border border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center gap-2 mb-4">
                                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            <h4 class="text-base font-bold text-gray-800 dark:text-gray-200">Deudas Incluidas en la
                                                Orden</h4>
                                        </div>
                                        <div class="overflow-x-auto">
                                            <table class="w-full text-sm">
                                                <thead>
                                                    <tr
                                                        class="bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-700 dark:to-gray-700/50 border-b-2 border-gray-300 dark:border-gray-600">
                                                        <th
                                                            class="px-4 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider w-16">
                                                            N°</th>
                                                        <th
                                                            class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                                            Concepto</th>
                                                        <th
                                                            class="px-4 py-3 text-right text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                                            Monto</th>
                                                        <th
                                                            class="px-4 py-3 text-right text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                                            Pagado</th>
                                                        <th
                                                            class="px-4 py-3 text-right text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                                            Pendiente</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tabla_deudas_orden" class="divide-y divide-gray-200 dark:divide-gray-600">
                                                    <tr>
                                                        <td colspan="5" class="px-4 py-8 text-center">
                                                            <div class="flex flex-col items-center gap-2">
                                                                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                </svg>
                                                                <span class="text-gray-500 dark:text-gray-400">Busque una orden para ver
                                                                    las deudas</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Datos del Pago -->
                                    <div class="mb-6">
                                        <h4 class="text-base font-bold text-gray-800 dark:text-gray-200 mb-4">Datos del Pago</h4>

                                        <!-- Detalles de Pago (2 partes máximo en una fila) -->
                                        <div id="detalles_pago_orden_container" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                            <!-- Detalle de Pago 1 -->
                                            <div
                                                class="detalle-pago-orden border rounded-lg p-4 bg-gray-50 dark:bg-gray-800/40 border-gray-300 dark:border-gray-700">
                                                <h5 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                                    Detalle de Pago #1
                                                </h5>

                                                <!-- Estado -->
                                                <p id="estado_pago_orden_1"
                                                    class="text-xs font-semibold mb-3 px-4 py-2 rounded-lg text-center shadow-sm bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200">
                                                    PENDIENTE
                                                </p>

                                                <div class="space-y-3">
                                                    <!-- Método de Pago -->
                                                    <div class="flex flex-col">
                                                        <label for="metodo_pago_orden_1"
                                                            class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                            Método de Pago
                                                        </label>
                                                        <select name="metodo_pago[]" id="metodo_pago_orden_1"
                                                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-800 dark:text-white/90 px-3 py-2.5 focus:ring-2 focus:ring-indigo-500">
                                                            <option value="">Seleccione...</option>
                                                            <option value="tarjeta">Tarjeta</option>
                                                            <option value="yape">Yape / Plin</option>
                                                            <option value="transferencia">Transferencia</option>
                                                            <option value="paypal">PayPal</option>
                                                        </select>
                                                        <div class="min-h-[20px]">
                                                            @error('metodo_pago.0')
                                                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Número de Operación -->
                                                    <div class="flex flex-col">
                                                        <label for="detalle_recibo_orden_1"
                                                            class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                            Nro de operación / recibo
                                                        </label>
                                                        <input type="text" name="detalle_recibo[]" id="detalle_recibo_orden_1" disabled
                                                            placeholder="Seleccione un método de pago"
                                                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90 focus:ring-2 focus:ring-indigo-500 disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed">
                                                        <div class="min-h-[20px]">
                                                            @error('detalle_recibo.0')
                                                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Monto -->
                                                    <div class="flex flex-col">
                                                        <label for="detalle_monto_orden_1"
                                                            class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                            Monto
                                                        </label>
                                                        <input type="text" name="detalle_monto[]" id="detalle_monto_orden_1"
                                                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90 focus:ring-2 focus:ring-indigo-500">
                                                        <div class="min-h-[20px]">
                                                            @error('detalle_monto.0')
                                                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Fecha -->
                                                    <div class="flex flex-col">
                                                        <label for="detalle_fecha_orden_1"
                                                            class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                            Fecha de Pago
                                                        </label>

                                                        <div
                                                            class="flex items-center rounded-lg border border-gray-300 dark:border-gray-700 focus-within:ring-2 focus-within:ring-indigo-500">
                                                            <input type="date" name="detalle_fecha[]" id="detalle_fecha_orden_1"
                                                                value="{{ old('detalle_fecha.0', '') }}"
                                                                class="w-full px-3 py-2.5 text-sm text-gray-800 dark:text-white/90 focus:outline-none rounded-l-lg bg-white dark:bg-gray-800">

                                                            <span
                                                                class="px-3 py-2.5 bg-gray-100 dark:bg-gray-800 border-l border-gray-300 dark:border-gray-700 flex items-center justify-center rounded-r-lg">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                </svg>
                                                            </span>
                                                        </div>
                                                        <div class="min-h-[20px]">
                                                            @error('detalle_fecha.0')
                                                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Voucher -->
                                                    <div id="voucher_container_orden_1" class="flex flex-col hidden">
                                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                            Voucher <span class="text-gray-500 text-xs">(JPG, PNG, PDF)</span>
                                                        </label>
                                                        <input type="file" name="voucher_path[]" id="voucher_path_orden_1"
                                                            class="hidden" accept=".jpg,.jpeg,.png,.pdf">
                                                        <button type="button" id="btnUploadVoucher_orden_1"
                                                            class="w-full flex items-center justify-center gap-2 px-3 py-2.5 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg hover:border-indigo-500 dark:hover:border-indigo-400 transition">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="h-4 w-4 text-gray-500 dark:text-gray-400" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                            </svg>
                                                            <span id="voucher_label_orden_1"
                                                                class="text-xs text-gray-600 dark:text-gray-400">Seleccionar</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Detalle de Pago 2 -->
                                            <div
                                                class="detalle-pago-orden border rounded-lg p-4 bg-gray-50 dark:bg-gray-800/40 border-gray-300 dark:border-gray-700">
                                                <h5 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                                    Detalle de Pago #2
                                                </h5>

                                                <!-- Estado -->
                                                <p id="estado_pago_orden_2"
                                                    class="text-xs font-semibold mb-3 px-4 py-2 rounded-lg text-center shadow-sm bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200">
                                                    PENDIENTE
                                                </p>

                                                <div class="space-y-3">
                                                    <!-- Método de Pago -->
                                                    <div class="flex flex-col">
                                                        <label for="metodo_pago_orden_2"
                                                            class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                            Método de Pago
                                                        </label>
                                                        <select name="metodo_pago[]" id="metodo_pago_orden_2"
                                                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-800 dark:text-white/90 px-3 py-2.5 focus:ring-2 focus:ring-indigo-500">
                                                            <option value="">Seleccione...</option>
                                                            <option value="tarjeta">Tarjeta</option>
                                                            <option value="yape">Yape / Plin</option>
                                                            <option value="transferencia">Transferencia</option>
                                                            <option value="paypal">PayPal</option>
                                                        </select>
                                                        <div class="min-h-[20px]">
                                                            @error('metodo_pago.1')
                                                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Número de Operación -->
                                                    <div class="flex flex-col">
                                                        <label for="detalle_recibo_orden_2"
                                                            class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                            Nro de operación / recibo
                                                        </label>
                                                        <input type="text" name="detalle_recibo[]" id="detalle_recibo_orden_2" disabled
                                                            placeholder="Seleccione un método de pago"
                                                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90 focus:ring-2 focus:ring-indigo-500 disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed">
                                                        <div class="min-h-[20px]">
                                                            @error('detalle_recibo.1')
                                                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Monto -->
                                                    <div class="flex flex-col">
                                                        <label for="detalle_monto_orden_2"
                                                            class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                            Monto
                                                        </label>
                                                        <input type="text" name="detalle_monto[]" id="detalle_monto_orden_2"
                                                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90 focus:ring-2 focus:ring-indigo-500">
                                                        <div class="min-h-[20px]">
                                                            @error('detalle_monto.1')
                                                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Fecha -->
                                                    <div class="flex flex-col">
                                                        <label for="detalle_fecha_orden_2"
                                                            class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                            Fecha de Pago
                                                        </label>

                                                        <div
                                                            class="flex items-center rounded-lg border border-gray-300 dark:border-gray-700 focus-within:ring-2 focus-within:ring-indigo-500">
                                                            <input type="date" name="detalle_fecha[]" id="detalle_fecha_orden_2"
                                                                value="{{ old('detalle_fecha.1', '') }}"
                                                                class="w-full px-3 py-2.5 text-sm text-gray-800 dark:text-white/90 focus:outline-none rounded-l-lg bg-white dark:bg-gray-800">

                                                            <span
                                                                class="px-3 py-2.5 bg-gray-100 dark:bg-gray-800 border-l border-gray-300 dark:border-gray-700 flex items-center justify-center rounded-r-lg">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                </svg>
                                                            </span>
                                                        </div>
                                                        <div class="min-h-[20px]">
                                                            @error('detalle_fecha.1')
                                                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Voucher -->
                                                    <div id="voucher_container_orden_2" class="flex flex-col hidden">
                                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                            Voucher <span class="text-gray-500 text-xs">(JPG, PNG, PDF)</span>
                                                        </label>
                                                        <input type="file" name="voucher_path[]" id="voucher_path_orden_2"
                                                            class="hidden" accept=".jpg,.jpeg,.png,.pdf">
                                                        <button type="button" id="btnUploadVoucher_orden_2"
                                                            class="w-full flex items-center justify-center gap-2 px-3 py-2.5 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg hover:border-indigo-500 dark:hover:border-indigo-400 transition">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="h-4 w-4 text-gray-500 dark:text-gray-400" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                            </svg>
                                                            <span id="voucher_label_orden_2"
                                                                class="text-xs text-gray-600 dark:text-gray-400">Seleccionar</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Input hidden para id_orden -->
                                <input type="hidden" id="id_orden_hidden" name="id_orden" value="">
                            </div>
                        </div>

                        <!-- ========== SECCIÓN PARA DEUDA INDIVIDUAL ========== -->
                        <div id="seccion_deuda_individual" class="col-span-1 sm:col-span-2 lg:col-span-3 hidden">
                            <div
                                class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-2 border-blue-300 dark:border-blue-700 rounded-xl p-6 shadow-lg">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="bg-blue-600 dark:bg-blue-500 rounded-lg p-3">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                            Pagar Deuda Individual
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Complete los detalles del pago de la deuda seleccionada
                                        </p>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    {{-- Primera fila: Fecha, Código, Nombre --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">

                                        @if(!isset($data['modo_completar']) || !$data['modo_completar'])
                                            <!-- Fecha de Pago -->
                                            <div class="flex flex-col">
                                                <label for="fecha_de_pago"
                                                    class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                    Fecha de Pago
                                                </label>

                                                <div
                                                    class="flex items-center rounded-lg border border-gray-300 dark:border-gray-700 focus-within:ring-2 focus-within:ring-indigo-500">
                                                    <input type="date" id="fecha_de_pago" name="fecha_de_pago" readonly
                                                        value="{{ old('fecha_de_pago') ?? now()->format('Y-m-d') }}"
                                                        class="w-full px-3 py-2.5 text-sm text-gray-800 dark:text-white/90 focus:outline-none rounded-l-lg">

                                                    <span
                                                        class="px-3 py-2.5 bg-gray-100 dark:bg-gray-800 border-l border-gray-300 dark:border-gray-700 flex items-center justify-center rounded-r-lg">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    </span>
                                                </div>

                                                <div class="min-h-[20px]">
                                                    @error('fecha_de_pago')
                                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Nombre Completo -->
                                            <div class="flex flex-col">
                                                <label for="nombre_alumno"
                                                    class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                    Estudiante
                                                </label>
                                                <input type="text" id="nombre_alumno" name="nombre_alumno"
                                                    value="{{ old('nombre_alumno') }}" readonly
                                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90">
                                                <div class="min-h-[20px]"></div>
                                            </div>

                                            <!-- Código de Educando -->
                                            <div class="flex flex-col">
                                                <label for="codigo_alumno"
                                                    class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">Código
                                                    educando</label>
                                                <input type="text" id="codigo_alumno" name="codigo_alumno"
                                                    value="{{ old('codigo_alumno') }}" readonly
                                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90">
                                                <div class="min-h-[20px]">
                                                    @error('codigo_alumno')
                                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                        @else
                                            <!-- Fecha de Pago (Modo Completar) -->
                                            <div class="flex flex-col">
                                                <label for="fecha_de_pago"
                                                    class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                    Fecha de Pago
                                                </label>

                                                <div
                                                    class="flex items-center rounded-lg border border-gray-300 dark:border-gray-700 focus-within:ring-2 focus-within:ring-indigo-500">
                                                    <input type="date" id="fecha_de_pago" name="fecha_de_pago" readonly
                                                        value="{{ old('fecha_de_pago') ?? now()->format('Y-m-d') }}"
                                                        class="w-full px-3 py-2.5 text-sm text-gray-800 dark:text-white/90 focus:outline-none rounded-l-lg bg-gray-100 dark:bg-gray-800">

                                                    <span
                                                        class="px-3 py-2.5 bg-gray-100 dark:bg-gray-800 border-l border-gray-300 dark:border-gray-700 flex items-center justify-center rounded-r-lg">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    </span>
                                                </div>

                                                <div class="min-h-[20px]">
                                                    @error('fecha_de_pago')
                                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Código de Educando (Modo Completar) -->
                                            <div class="flex flex-col">
                                                <label for="codigo_alumno"
                                                    class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                    Código educando
                                                </label>
                                                <div class="flex gap-2">
                                                    <input type="text" id="codigo_alumno" name="codigo_alumno"
                                                        value="{{ $data['alumno']->codigo_educando }}" readonly
                                                        class="flex-1 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90">
                                                    <button type="button" id="btnBuscarOtro"
                                                        class="inline-flex items-center gap-2 rounded-lg border border-indigo-600 bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 dark:border-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                        </svg>
                                                        Buscar Otro
                                                    </button>
                                                </div>
                                                <div class="min-h-[20px]"></div>
                                            </div>

                                            <!-- Nombre Completo del Alumno (Modo Completar) -->
                                            <div class="flex flex-col">
                                                <label for="nombre_alumno"
                                                    class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                    Nombre Completo
                                                </label>
                                                <input type="text" id="nombre_alumno" name="nombre_alumno"
                                                    value="{{ trim($data['alumno']->primer_nombre . ' ' . ($data['alumno']->otros_nombres ?? '') . ' ' . $data['alumno']->apellido_paterno . ' ' . $data['alumno']->apellido_materno) }}"
                                                    readonly
                                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90">
                                                <div class="min-h-[20px]"></div>
                                            </div>
                                        @endif

                                        {{-- Deuda --}}
                                    </div>

                                    {{-- Segunda fila: Deuda, Monto Total, Monto Pagado, Monto Pendiente (4 columnas) --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                                        {{-- Deuda --}}
                                        <div class="flex flex-col">
                                            <label for="select_deuda" class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                Deuda
                                            </label>
                                            <select id="select_deuda" name="id_deuda"
                                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-800 dark:text-white/90 px-3 py-2.5 focus:ring-2 focus:ring-indigo-500"
                                                @if(isset($data['modo_completar']) && $data['modo_completar']) readonly disabled @endif>
                                                @if(isset($data['modo_completar']) && $data['modo_completar'])
                                                    <option value="{{ $data['deuda']->id_deuda }}" selected>
                                                        {{ $data['concepto']->descripcion }} - {{ $data['deuda']->periodo }} (S/
                                                        {{ number_format($data['deuda']->monto_total, 2) }})
                                                    </option>
                                                @else
                                                    <option value="">Seleccione...</option>
                                                    @foreach($options ?? [] as $key => $option)
                                                        <option value="{{ $option_values[$key] ?? $key }}" {{ old('id_deuda') == ($option_values[$key] ?? $key) ? 'selected' : '' }}>
                                                            {{ $option }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @if(isset($data['modo_completar']) && $data['modo_completar'])
                                                <input type="hidden" name="id_deuda" value="{{ $data['deuda']->id_deuda }}">
                                            @endif
                                            <input type="hidden" id="id_deuda_hidden" name="id_deuda_old"
                                                value="{{ old('id_deuda', $data['deuda']->id_deuda ?? '') }}">
                                            <input type="hidden" id="detalles_existentes" name="detalles_existentes"
                                                value="{{ old('detalles_existentes', isset($data['detalle_existente']) ? 1 : 0) }}">
                                            <div class="min-h-[20px]">
                                                @error('id_deuda')
                                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Monto total a pagar --}}
                                        <div class="flex flex-col">
                                            <label for="monto_total_a_pagar"
                                                class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                Monto Total
                                            </label>
                                            <input type="text" id="monto_total_a_pagar" name="monto_total_a_pagar"
                                                value="{{ old('monto_total_a_pagar', isset($data['deuda']) ? number_format($data['deuda']->monto_total, 2, '.', '') : '') }}"
                                                readonly
                                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-blue-50 dark:bg-blue-900/30 px-3 py-2.5 text-sm font-bold text-blue-700 dark:text-blue-300">
                                            <div class="min-h-[20px]">
                                                @error('monto_total_a_pagar')
                                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Monto pagado --}}
                                        <div class="flex flex-col">
                                            <label for="monto_pagado" class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                Monto Pagado
                                            </label>
                                            <input type="text" id="monto_pagado" name="monto_pagado"
                                                value="{{ old('monto_pagado', isset($data['detalle_existente']) ? number_format($data['detalle_existente']->monto, 2, '.', '') : '00.00') }}"
                                                readonly
                                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-green-50 dark:bg-green-900/30 px-3 py-2.5 text-sm font-bold text-green-700 dark:text-green-300">
                                            <div class="min-h-[20px]">
                                                @error('monto_pagado')
                                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Monto Pendiente (calculado) --}}
                                        <div class="flex flex-col">
                                            <label for="monto_pendiente"
                                                class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                Monto Pendiente
                                            </label>
                                            <input type="text" id="monto_pendiente" readonly
                                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-red-50 dark:bg-red-900/30 px-3 py-2.5 text-sm font-bold text-red-700 dark:text-red-300">
                                            <div class="min-h-[20px]"></div>
                                        </div>
                                    </div>

                                    {{-- Tercera fila: Observaciones (ancho completo fuera del grid) --}}
                                </div>

                                <div class="mb-4">
                                    <label for="observaciones"
                                        class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1 block">Observaciones</label>
                                    <textarea id="observaciones" name="observaciones" rows="2"
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 px-3 py-2.5 text-sm text-gray-800 dark:text-white/90 focus:ring-2 focus:ring-indigo-500">{{ old('observaciones') }}</textarea>
                                    <div class="min-h-[20px]">
                                        @error('observaciones')
                                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Datos del Pago --}}
                                <div class="mb-4">
                                    <h4 class="text-base font-bold text-gray-800 dark:text-gray-200 mb-4">Datos del Pago</h4>

                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                                        {{-- Detalle de Pago #1 --}}
                                        <div
                                            class="detalle-pago border rounded-lg p-4 bg-gray-50 dark:bg-gray-800/40 border-gray-300 dark:border-gray-700 shadow-sm">
                                            <h5 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                                Detalle de Pago #1
                                            </h5>

                                            <!-- Estado -->
                                            <p id="estado_pago_1" class="text-xs font-semibold mb-3 px-4 py-2 rounded-lg text-center shadow-sm 
                                                @if(isset($data['modo_completar']) && $data['modo_completar'])
                                                    @if($data['detalle_existente']->estado_validacion === 'validado')
                                                        bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200
                                                    @elseif($data['detalle_existente']->estado_validacion === 'rechazado')
                                                        bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200
                                                    @else
                                                        bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200
                                                    @endif
                                                @else
                                                    bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200
                                                @endif">
                                                @if(isset($data['modo_completar']) && $data['modo_completar'])
                                                    @if($data['detalle_existente']->estado_validacion === 'validado')
                                                        VALIDADO
                                                    @elseif($data['detalle_existente']->estado_validacion === 'rechazado')
                                                        RECHAZADO
                                                    @else
                                                        PENDIENTE
                                                    @endif
                                                @else
                                                    PENDIENTE
                                                @endif
                                            </p>

                                            <div class="space-y-3">
                                                <!-- Método de pago -->
                                                <div class="flex flex-col">
                                                    <label for="metodo_pago_1"
                                                        class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                        Método de Pago
                                                    </label>
                                                    <select name="metodo_pago[0]" id="metodo_pago_1" class="w-full rounded-lg border
                                                            {{ $errors->has('metodo_pago.0') ? 'border-red-500' : 'border-gray-300 dark:border-gray-700' }}
                                                            bg-white dark:bg-gray-800 text-sm px-3 py-2.5 
                                                            text-gray-800 dark:text-white/90 focus:ring-2 focus:ring-indigo-500"
                                                        @if(isset($data['modo_completar']) && $data['modo_completar']) disabled @endif>
                                                        @if(isset($data['modo_completar']) && $data['modo_completar'])
                                                            <option value="{{ $data['detalle_existente']->metodo_pago }}" selected>
                                                                {{ ucfirst($data['detalle_existente']->metodo_pago) }}
                                                            </option>
                                                        @else
                                                            <option value="">Seleccione...</option>
                                                            <option value="tarjeta" {{ old('metodo_pago.0') == 'tarjeta' ? 'selected' : '' }}>
                                                                Tarjeta
                                                            </option>
                                                            <option value="yape" {{ old('metodo_pago.0') == 'yape' ? 'selected' : '' }}>Yape /
                                                                Plin
                                                            </option>
                                                            <option value="transferencia" {{ old('metodo_pago.0') == 'transferencia' ? 'selected' : '' }}>
                                                                Transferencia</option>
                                                            <option value="paypal" {{ old('metodo_pago.0') == 'paypal' ? 'selected' : '' }}>
                                                                PayPal
                                                            </option>
                                                        @endif
                                                    </select>
                                                    @if(isset($data['modo_completar']) && $data['modo_completar'])
                                                        <input type="hidden" name="metodo_pago[0]"
                                                            value="{{ $data['detalle_existente']->metodo_pago }}">
                                                    @endif
                                                    <div class="min-h-[20px]">
                                                        @error('metodo_pago.0')
                                                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!-- Número de operación -->
                                                <div class="flex flex-col">
                                                    <label for="detalle_recibo_1"
                                                        class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                        Nro de operación / recibo
                                                    </label>
                                                    <input type="text" name="detalle_recibo[0]" id="detalle_recibo_1"
                                                        value="{{ old('detalle_recibo.0', isset($data['detalle_existente']) ? $data['detalle_existente']->nro_recibo : '') }}"
                                                        placeholder="Seleccione un método de pago"
                                                        class="w-full rounded-lg border
                                                            {{ $errors->has('detalle_recibo.0') ? 'border-red-500' : 'border-gray-300 dark:border-gray-700' }}
                                                            px-3 py-2.5 text-sm text-gray-800 dark:text-white/90 focus:ring-2 focus:ring-indigo-500 disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed"
                                                        @if(isset($data['modo_completar']) && $data['modo_completar']) readonly @else
                                                        disabled @endif>
                                                    <div class="min-h-[20px]">
                                                        @error('detalle_recibo.0')
                                                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!-- Monto -->
                                                <div class="flex flex-col">
                                                    <label for="detalle_monto_1"
                                                        class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                        Monto
                                                    </label>
                                                    <input type="text" name="detalle_monto[0]" id="detalle_monto_1"
                                                        value="{{ old('detalle_monto.0', isset($data['detalle_existente']) ? number_format((float) $data['detalle_existente']->monto, 2, '.', '') : '') }}"
                                                        class="w-full rounded-lg border
                                                            {{ $errors->has('detalle_monto.0') ? 'border-red-500' : 'border-gray-300 dark:border-gray-700' }}
                                                            px-3 py-2.5 text-sm text-gray-800 dark:text-white/90 focus:ring-2 focus:ring-indigo-500"
                                                        @if(isset($data['modo_completar']) && $data['modo_completar']) readonly @endif>
                                                    <div class="min-h-[20px]">
                                                        @error('detalle_monto.0')
                                                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!-- Fecha -->
                                                <div class="flex flex-col">
                                                    <label for="detalle_fecha_1"
                                                        class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                        Fecha de Pago
                                                    </label>

                                                    <div class="flex items-center rounded-lg border 
                                                        {{ $errors->has('detalle_fecha.0') ? 'border-red-500' : 'border-gray-300 dark:border-gray-700' }}
                                                        focus-within:ring-2 focus-within:ring-indigo-500">

                                                        <input type="text" name="detalle_fecha[0]" id="detalle_fecha_1"
                                                            value="{{ old('detalle_fecha.0', isset($data['detalle_existente']) ? $data['detalle_existente']->fecha_pago->format('d/m/Y') : '') }}"
                                                            placeholder="DD/MM/YYYY"
                                                            pattern="\d{2}/\d{2}/\d{4}"
                                                            class="w-full px-3 py-2.5 text-sm text-gray-800 dark:text-white/90 
                                                                focus:outline-none rounded-l-lg bg-white dark:bg-gray-800"
                                                            @if(isset($data['modo_completar']) && $data['modo_completar']) readonly
                                                            @endif>

                                                        <span class="px-3 py-2.5 bg-gray-100 dark:bg-gray-800 border-l border-gray-300 dark:border-gray-700 
                                                                    flex items-center justify-center rounded-r-lg">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                        </span>
                                                    </div>

                                                    <div class="min-h-[20px]">
                                                        @error('detalle_fecha.0')
                                                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!-- Voucher (solo para transferencia/yape) -->
                                                @if(!isset($data['modo_completar']) || !$data['modo_completar'])
                                                    <div class="flex flex-col hidden" id="voucher_group_1">
                                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                            Voucher <span class="text-gray-500 text-xs">(JPG, PNG, PDF)</span>
                                                        </label>
                                                        <input type="file" name="voucher_path[0]" id="voucher_path_1" style="display: none;"
                                                            accept=".jpg,.jpeg,.png,.pdf">
                                                        <button type="button" id="btnUploadVoucher_1"
                                                            class="w-full flex items-center justify-center gap-2 px-3 py-2.5 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg hover:border-indigo-500 dark:hover:border-indigo-400 transition">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="h-4 w-4 text-gray-500 dark:text-gray-400" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                            </svg>
                                                            <span id="voucher_label_1"
                                                                class="text-xs text-gray-600 dark:text-gray-400">Seleccionar</span>
                                                        </button>
                                                        <div class="min-h-[20px]">
                                                            @error('voucher_path.0')
                                                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                @elseif(isset($data['detalle_existente']) && $data['detalle_existente']->voucher_path)
                                                    <div class="flex flex-col">
                                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                            Constancia de Pago
                                                        </label>
                                                        <p class="text-xs text-gray-600 dark:text-gray-400">✓ Archivo cargado</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Detalle de Pago #2 --}}
                                        <div
                                            class="detalle-pago border rounded-lg p-4 bg-gray-50 dark:bg-gray-800/40 border-gray-300 dark:border-gray-700 shadow-sm">
                                            <h5 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                                Detalle de Pago #2
                                            </h5>

                                            <!-- Estado -->
                                            <p id="estado_pago_2"
                                                class="text-xs font-semibold mb-3 px-4 py-2 rounded-lg text-center shadow-sm bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200">
                                                PENDIENTE
                                            </p>

                                            <div class="space-y-3">
                                                <!-- Método de pago -->
                                                <div class="flex flex-col">
                                                    <label for="metodo_pago_2"
                                                        class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                        Método de Pago
                                                    </label>
                                                    <select name="metodo_pago[1]" id="metodo_pago_2" class="w-full rounded-lg border 
                                                            {{ $errors->has('metodo_pago.1') ? 'border-red-500' : 'border-gray-300 dark:border-gray-700' }}
                                                            bg-white dark:bg-gray-800 text-sm px-3 py-2.5 
                                                            text-gray-800 dark:text-white/90 focus:ring-2 focus:ring-indigo-500">
                                                        <option value="">Seleccione...</option>
                                                        <option value="tarjeta" {{ old('metodo_pago.1') == 'tarjeta' ? 'selected' : '' }}>
                                                            Tarjeta
                                                        </option>
                                                        <option value="yape" {{ old('metodo_pago.1') == 'yape' ? 'selected' : '' }}>Yape /
                                                            Plin
                                                        </option>
                                                        <option value="transferencia" {{ old('metodo_pago.1') == 'transferencia' ? 'selected' : '' }}>
                                                            Transferencia</option>
                                                        <option value="paypal" {{ old('metodo_pago.1') == 'paypal' ? 'selected' : '' }}>
                                                            PayPal
                                                        </option>
                                                    </select>
                                                    <div class="min-h-[20px]">
                                                        @error('metodo_pago.1')
                                                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!-- Número de operación -->
                                                <div class="flex flex-col">
                                                    <label for="detalle_recibo_2"
                                                        class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                        Nro de operación / recibo
                                                    </label>
                                                    <input type="text" name="detalle_recibo[1]" id="detalle_recibo_2"
                                                        value="{{ old('detalle_recibo.1') }}" @if(isset($data['modo_completar']) && $data['modo_completar'] && !old('metodo_pago.1')) readonly @else disabled @endif
                                                        placeholder="Seleccione un método de pago"
                                                        class="w-full rounded-lg border 
                                                                            {{ $errors->has('detalle_recibo.1') ? 'border-red-500' : 'border-gray-300 dark:border-gray-700' }}
                                                            px-3 py-2.5 text-sm text-gray-800 dark:text-white/90 focus:ring-2 focus:ring-indigo-500 disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed
                                                            @if(isset($data['modo_completar']) && $data['modo_completar'] && !old('metodo_pago.1')) bg-gray-100 dark:bg-gray-800 cursor-not-allowed @endif">
                                                    <div class="min-h-[20px]">
                                                        @error('detalle_recibo.1')
                                                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!-- Monto -->
                                                <div class="flex flex-col">
                                                    <label for="detalle_monto_2"
                                                        class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                        Monto
                                                    </label>
                                                    <input type="text" name="detalle_monto[1]" id="detalle_monto_2"
                                                        value="{{ old('detalle_monto.1') ? number_format((float) old('detalle_monto.1'), 2, '.', '') : '' }}"
                                                        @if(isset($data['modo_completar']) && $data['modo_completar'])
                                                            placeholder="Restante: S/ {{ number_format($data['deuda']->monto_total - $data['detalle_existente']->monto, 2, '.', '') }}"
                                                        @endif
                                                        class="w-full rounded-lg border 
                                                            {{ $errors->has('detalle_monto.1') ? 'border-red-500' : 'border-gray-300 dark:border-gray-700' }}
                                                            px-3 py-2.5 text-sm text-gray-800 dark:text-white/90 focus:ring-2 focus:ring-indigo-500">
                                                    <div class="min-h-[20px]">
                                                        @error('detalle_monto.1')
                                                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!-- Fecha -->
                                                <div class="flex flex-col">
                                                    <label for="detalle_fecha_2"
                                                        class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                        Fecha de Pago
                                                    </label>

                                                    <div class="flex items-center rounded-lg border 
                                                        {{ $errors->has('detalle_fecha.1') ? 'border-red-500' : 'border-gray-300 dark:border-gray-700' }}
                                                        focus-within:ring-2 focus-within:ring-indigo-500">

                                                        <input type="text" name="detalle_fecha[1]" id="detalle_fecha_2"
                                                            value="{{ old('detalle_fecha.1') }}"
                                                            placeholder="DD/MM/YYYY"
                                                            pattern="\d{2}/\d{2}/\d{4}"
                                                            class="w-full px-3 py-2.5 text-sm text-gray-800 dark:text-white/90 
                                                                focus:outline-none rounded-l-lg bg-white dark:bg-gray-800">

                                                        <span class="px-3 py-2.5 bg-gray-100 dark:bg-gray-800 border-l border-gray-300 dark:border-gray-700 
                                                                    flex items-center justify-center rounded-r-lg">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                        </span>
                                                    </div>

                                                    <div class="min-h-[20px]">
                                                        @error('detalle_fecha.1')
                                                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                                        @enderror
                                                    </div>

                                                    <div class="min-h-[20px]">
                                                        @error('detalle_fecha.1')
                                                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!-- Voucher (solo para transferencia/yape) -->
                                                <div class="flex flex-col hidden" id="voucher_group_2">
                                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                                        Voucher <span class="text-gray-500 text-xs">(JPG, PNG, PDF)</span>
                                                    </label>
                                                    <input type="file" name="voucher_path[1]" id="voucher_path_2" style="display: none;"
                                                        accept=".jpg,.jpeg,.png,.pdf">
                                                    <button type="button" id="btnUploadVoucher_2"
                                                        class="w-full flex items-center justify-center gap-2 px-3 py-2.5 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg hover:border-indigo-500 dark:hover:border-indigo-400 transition">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-4 w-4 text-gray-500 dark:text-gray-400" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                        </svg>
                                                        <span id="voucher_label_2"
                                                            class="text-xs text-gray-600 dark:text-gray-400">Seleccionar</span>
                                                    </button>
                                                    <div class="min-h-[20px]">
                                                        @error('voucher_path.1')
                                                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div> <!-- Fin de seccion_deuda_individual -->

            @endif

    </form>
    </div>

@endsection

@section('custom-js')
    <script>
        window.Laravel = window.Laravel || {};
        window.Laravel.routes = {
            buscarOrden: "{{ route('pago_buscar_orden', ['codigo_orden' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', ''),
            registrarPagoOrden: "{{ route('pago_registrar_pago_orden') }}"
        };
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const oldTipo = @json(old('tipo_pago'));
            const oldIdDeuda = @json(old('id_deuda'));
            const oldCodigoBusqueda = @json(old('codigo_busqueda_unificado'));

            if (oldTipo) {
                const tipoContainer = document.getElementById('tipo_pago_container');
                if (tipoContainer) tipoContainer.classList.remove('hidden');

                const radio = document.querySelector(`input[name="tipo_pago_selector"][value="${oldTipo}"]`);
                if (radio) radio.checked = true;

                const tipoHidden = document.getElementById('tipo_pago_hidden');
                if (tipoHidden) tipoHidden.value = oldTipo;

                setTimeout(function() {
                    if (radio) radio.dispatchEvent(new Event('change'));
                }, 250);
            }
        });
    </script>
    <script src="{{ asset('js/busqueda-unificada-pago.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/pago-create-1.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/pago-create-2.js') }}?v={{ time() }}"></script>
@endsection