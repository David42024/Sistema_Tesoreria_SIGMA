@extends('base.administrativo.blank')

@section('titulo')
    Editar usuario
@endsection

@section('contenido')
    <div class="p-8 m-4 bg-gray-100 dark:bg-white/[0.03] rounded-2xl">
        <!-- Header -->
        <div class="flex pb-6 justify-between items-center border-b border-gray-200 dark:border-gray-700">
            <div>
                <h2 class="text-2xl font-bold dark:text-gray-200 text-gray-800">Editar Usuario</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Modifica la información del usuario</p>
            </div>
            <div class="flex gap-3">
                <input form="form" type="submit"
                    class="cursor-pointer inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700"
                    value="Guardar Cambios"
                >

                <a href="{{ $data['return'] }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    Cancelar
                </a>
            </div>
        </div>

        <form method="POST" id="form" action="{{ route('usuario_editEntry', $data['id']) }}" class="mt-8">
            @method('PATCH')
            @csrf

            <!-- Información de Acceso -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                    Información de Acceso
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @include('components.forms.string', [
                        'label' => 'Nombre de Usuario',
                        'name' => 'username',
                        'error' => $errors->first('username') ?? false,
                        'value' => old('username', $data['default']['username'])
                    ])

                    @include('components.forms.combo', [
                        'label' => 'Tipo de Usuario',
                        'name' => 'tipo',
                        'options' => $data['tipos'],
                        'options_attributes' => ['id', 'descripcion'],
                        'error' => $errors->first('tipo') ?? false,
                        'value' => old('tipo', $data['default']['tipo'])
                    ])
                </div>

                <div class="grid grid-cols-1 gap-6 mt-6">
                    @include('components.forms.combo', [
                        'label' => 'Estado',
                        'name' => 'estado',
                        'options' => [
                            ['id' => '1', 'descripcion' => 'Activo'],
                            ['id' => '0', 'descripcion' => 'Inactivo']
                        ],
                        'options_attributes' => ['id', 'descripcion'],
                        'error' => $errors->first('estado') ?? false,
                        'value' => old('estado', $data['default']['estado'] ? '1' : '0')
                    ])
                </div>
            </div>

            <!-- Cambio de Contraseña -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Cambiar Contraseña
                </h3>
                <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700 dark:text-blue-400">
                                Si desea cambiar la contraseña del usuario, utilice el botón "Cambiar Contraseña" disponible en la lista de usuarios.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
