@extends('base.administrativo.blank')

@section('titulo')
    Cambiar contraseña
@endsection

@section('contenido')
    <div class="p-8 m-4 bg-gray-100 dark:bg-white/[0.03] rounded-2xl">
        <form method="post" action="{{ route('usuario_update_password', $data['id']) }}">
                        @method('PATCH')
                        @csrf
        <!-- Header -->
        <div class="flex pb-6 justify-between items-center border-b border-gray-200 dark:border-gray-700">
            <div>
                <h2 class="text-2xl font-bold dark:text-gray-200 text-gray-800">Cambiar Contraseña</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Usuario: <span class="font-semibold">{{ $data['username'] }}</span></p>
            </div>
            <div class="flex gap-3">
                <button type="submit"
                    class="cursor-pointer inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700"
                >
                    Guardar
                </button>

                <a href="{{ $data['return'] }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    Cancelar
                </a>
            </div>
        </div>

            <!-- Información de Seguridad -->
            <div class="mb-8">
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700 dark:text-yellow-400">
                                <strong>Importante:</strong> Al cambiar la contraseña, el usuario deberá iniciar sesión nuevamente con la nueva contraseña.
                            </p>
                        </div>
                    </div>
                </div>

                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Nueva Contraseña
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @include('components.forms.password', [
                        'label' => 'Nueva Contraseña',
                        'name' => 'password',
                        'error' => $errors->first('password') ?? false
                    ])

                    @include('components.forms.password', [
                        'label' => 'Confirmar Nueva Contraseña',
                        'name' => 'password_confirmation',
                        'error' => $errors->first('password_confirmation') ?? false
                    ])
                </div>

                <div class="mt-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <p class="mb-2"><strong>Requisitos de la contraseña:</strong></p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Mínimo 6 caracteres</li>
                            <li>Las contraseñas deben coincidir</li>
                        </ul>
                    </div>
                </div>
            </div>

        </form>
    </div>
@endsection
