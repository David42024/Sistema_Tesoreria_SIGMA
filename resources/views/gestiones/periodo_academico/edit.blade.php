@extends('base.administrativo.blank')

@section('titulo')
    Editar Período Académico
@endsection

@section('contenido')
    <div class="p-8 m-4 bg-gray-100 dark:bg-white/[0.03] rounded-2xl">
        <!-- Header -->
        <div class="flex pb-6 justify-between items-center border-b border-gray-200 dark:border-gray-700">
            <div>
                <h2 class="text-2xl font-bold dark:text-gray-200 text-gray-800">Editar Período Académico</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">ID: {{ $data['periodo']->getKey() }}</p>
            </div>
            <div class="flex gap-3">
                @if(!$data['es_actual'])
                <form action="{{ route('periodo_academico_establecerActual', ['id' => $data['periodo']->getKey()]) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Establecer como Actual
                    </button>
                </form>
                @endif
                <input form="form" type="submit"
                    class="cursor-pointer inline-flex items-center gap-2 rounded-lg border border-green-300 bg-green-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:border-green-600 dark:bg-green-600 dark:hover:bg-green-700"
                    value="Guardar Cambios"
                >
                <a href="{{ $data['return'] }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    Cancelar
                </a>
            </div>
        </div>

        <form method="POST" id="form" action="" class="mt-8">
            @method('PATCH')
            @csrf

            <!-- Estado actual -->
            @if($data['es_actual'])
            <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-green-900 dark:text-green-200">Este es el período académico actual</p>
                        <p class="text-xs text-green-700 dark:text-green-300 mt-1">Todas las matrículas nuevas se asociarán a este período.</p>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Información del Período
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @include('components.forms.string', [
                        'label' => 'Nombre del Período',
                        'name' => 'nombre',
                        'error' => $errors->first('nombre') ?? false,
                        'value' => old('nombre', $data['periodo']->nombre),
                        'required' => true
                    ])

                    @include('components.forms.select', [
                        'label' => 'Estado del Período',
                        'name' => 'estado',
                        'error' => $errors->first('estado') ?? false,
                        'value' => old('estado', $data['periodo']->id_estado_periodo_academico),
                        'required' => true,
                        'options' => $data['estados_periodo']->pluck('nombre'),
                        'option_values' => $data['estados_periodo']->map(function($estado){ return $estado->getKey(); })->toArray()
                    ])
                </div>
            </div>

            <!--  Configuración del cronograma académico (Mirrored from Create) -->
            <div x-data="cronogramaForm" class="mt-8 bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Cronograma Académico
                    </h3>
                    <div class="flex gap-2">
                        <button type="button" @click="agregarTodas" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-purple-700 bg-purple-100 rounded-lg hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 dark:bg-purple-900/30 dark:text-purple-300 dark:hover:bg-purple-900/50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Agregar todas las etapas
                        </button>
                        <button type="button" @click="agregarEtapa" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-900/30 dark:text-blue-300 dark:hover:bg-blue-900/50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Agregar Etapa
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3 rounded-l-lg">Tipo de Etapa</th>
                                <th class="px-4 py-3">Fecha Inicio</th>
                                <th class="px-4 py-3">Fecha Fin</th>
                                <th class="px-4 py-3">Estado</th>
                                <th class="px-4 py-3 rounded-r-lg text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-if="etapas.length === 0">
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        No hay etapas configuradas. Haz clic en "Agregar Etapa" para comenzar.
                                    </td>
                                </tr>
                            </template>
                            <template x-for="(etapa, index) in etapas" :key="index">
                                <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-3">
                                        <select x-model="etapa.tipo" required class="block p-2 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="" disabled>Seleccionar etapa...</option>
                                            <template x-for="tipo in tipos" :key="tipo.id_tipo_etapa_pa">
                                                <option :value="tipo.id_tipo_etapa_pa" x-text="tipo.nombre" :disabled="isTipoUsed(tipo.id_tipo_etapa_pa, index)" :selected="etapa.tipo == tipo.id_tipo_etapa_pa"></option>
                                            </template>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="datetime-local" x-model="etapa.fecha_inicio" required class="block p-2 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="datetime-local" x-model="etapa.fecha_fin" required class="block p-2 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </td>
                                    <td class="px-4 py-3">
                                        <select x-model="etapa.estado" required class="block p-2 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <template x-for="estado in estados" :key="estado.id_estado_etapa_pa">
                                                <option :value="estado.id_estado_etapa_pa" x-text="estado.nombre" :selected="etapa.estado == estado.id_estado_etapa_pa"></option>
                                            </template>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="button" @click="eliminarEtapa(index)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                
                <!-- Hidden input to send data -->
                <input type="hidden" name="cronograma" :value="JSON.stringify(etapas)">
            </div>

            <!-- Botones de acción -->
            <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ $data['return'] }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancelar
                </a>
                @if(!$data['es_actual'])
                <form action="{{ route('periodo_academico_establecerActual', ['id' => $data['periodo']->getKey()]) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Establecer como Actual
                    </button>
                </form>
                @endif
                <input form="form" type="submit"
                    class="cursor-pointer inline-flex items-center gap-2 rounded-lg border border-green-300 bg-green-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:border-green-600 dark:bg-green-600 dark:hover:bg-green-700"
                    value="💾 Guardar Cambios"
                >
            </div>
        </form>
    </div>
@endsection

@section('custom-js')
    <script src="{{ asset('js/tables.js') }}"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cronogramaForm', () => ({
                etapas: @if(old('cronograma')) JSON.parse(@json(old('cronograma'))) @else @json($data['cronograma_actual']) @endif,
                tipos: @json($data['tipos_etapa']),
                estados: @json($data['estados_etapa']),

                init() {},

                agregarEtapa() {
                    const defaultEstado = this.estados.find(e => e.nombre === 'ACTIVO') || this.estados[0];
                    if (this.etapas.length < this.tipos.length) {
                        this.etapas.push({
                            tipo: '',
                            estado: defaultEstado ? defaultEstado.id_estado_etapa_pa : '',
                            fecha_inicio: '',
                            fecha_fin: ''
                        });
                    }
                },

                eliminarEtapa(index) {
                    this.etapas.splice(index, 1);
                },

                agregarTodas() {
                    const usados = this.etapas.map(e => parseInt(e.tipo)).filter(t => !isNaN(t));
                    const defaultEstado = this.estados.find(e => e.nombre === 'ACTIVO') || this.estados[0];

                    this.tipos.forEach(tipo => {
                        if (!usados.includes(tipo.id_tipo_etapa_pa)) {
                            this.etapas.push({
                                tipo: tipo.id_tipo_etapa_pa,
                                estado: defaultEstado ? defaultEstado.id_estado_etapa_pa : '',
                                fecha_inicio: '',
                                fecha_fin: ''
                            });
                        }
                    });
                },

                isTipoUsed(idTipo, currentIndex) {
                    return this.etapas.some((etapa, idx) => 
                        idx !== currentIndex && parseInt(etapa.tipo) === idTipo
                    );
                }
            }));
        });
    </script>
@endsection