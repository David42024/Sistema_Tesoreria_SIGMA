@if(count($alumnos) > 0)
<div class="container-fluid py-4">
    <div class="flex flex-nowrap gap-6 overflow-x-auto pb-4 scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 dark:scrollbar-thumb-gray-600 dark:scrollbar-track-gray-800 snap-x snap-mandatory px-4 md:px-0 justify-center" style="-webkit-overflow-scrolling: touch;">
        @foreach($alumnos as $index => $alumno)
            @php
                $matriculaActiva = \App\Models\Matricula::where('id_alumno', $alumno['id_alumno'])
                    ->where('estado', true)
                    ->orderBy('año_escolar', 'desc')
                    ->first();
                $gradientClass = match($index % 6) {
                    0 => 'bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-800 dark:to-indigo-900',
                    1 => 'bg-gradient-to-br from-amber-50 to-orange-100 dark:from-amber-800 dark:to-orange-900',
                    2 => 'bg-gradient-to-br from-pink-50 to-purple-100 dark:from-pink-800 dark:to-purple-900',
                    3 => 'bg-gradient-to-br from-cyan-50 to-blue-100 dark:from-cyan-800 dark:to-blue-900',
                    4 => 'bg-gradient-to-br from-rose-50 to-pink-100 dark:from-rose-800 dark:to-pink-900',
                    5 => 'bg-gradient-to-br from-purple-50 to-indigo-100 dark:from-purple-800 dark:to-indigo-900',
                    default => 'bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700'
                };
                $foto = isset($alumno['foto']) && $alumno['foto']
                    ? asset('storage/' . $alumno['foto'])
                    : asset('storage/fotos/alumnos/default.jpg');
            @endphp
            <div class="flex-shrink-0 w-72 rounded-3xl overflow-hidden transition-all duration-300 ease-in-out cursor-pointer shadow-lg hover:shadow-2xl hover:-translate-y-2 hover:scale-[1.02] border border-gray-200 dark:border-gray-700 {{ $gradientClass }}" onclick="seleccionarAlumno({{ $alumno['id_alumno'] }})">
                <div class="p-6 text-center dark:bg-gray-800 flex flex-col justify-between flex-1 h-full">
                    {{-- Foto --}}
                    <div class="mb-3 flex justify-center">
                        <img src="{{ $foto }}" alt="Foto" class="w-32 h-32 rounded-full object-cover border-4 border-white dark:border-gray-700 shadow-lg">
                    </div>

                    {{-- Nombre --}}
                    <h5 class="text-gray-800 dark:text-gray-100 font-bold text-lg leading-tight min-h-[2.6rem] flex items-center justify-center mb-3">
                        {{ $alumno['primer_nombre'] }} {{ $alumno['apellido_paterno'] }} {{ $alumno['apellido_materno'] }}
                    </h5>

                    {{-- Info box --}}
                    <div class="bg-white/95 dark:bg-gray-700/95 backdrop-blur-sm rounded-2xl p-4 mb-4 shadow-md border border-gray-100 dark:border-gray-600">
                        <div class="info-item flex items-center mb-1.5 text-gray-600 dark:text-gray-300 text-sm last:mb-0">
                            <i class="fas fa-id-card text-blue-500 w-5 text-base mr-2 flex-shrink-0"></i>
                            <span class="flex-1"><strong class="text-gray-800 dark:text-gray-200">DNI:</strong> {{ $alumno['dni'] }}</span>
                        </div>
                        <div class="info-item flex items-center mb-1.5 text-gray-600 dark:text-gray-300 text-sm last:mb-0">
                            <i class="fas fa-barcode text-blue-500 w-5 text-base mr-2 flex-shrink-0"></i>
                            <span class="flex-1"><strong class="text-gray-800 dark:text-gray-200">Código:</strong> {{ $alumno['codigo_educando'] }}</span>
                        </div>
                        <div class="info-item flex items-center mb-1.5 text-gray-600 dark:text-gray-300 text-sm last:mb-0">
                            <i class="fas fa-graduation-cap text-blue-500 w-5 text-base mr-2 flex-shrink-0"></i>
                            <span class="flex-1"><strong class="text-gray-800 dark:text-gray-200">Grado:</strong> {{ $matriculaActiva?->grado?->nombre_grado ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item flex items-center mb-1.5 text-gray-600 dark:text-gray-300 text-sm last:mb-0">
                            <i class="fas fa-users text-blue-500 w-5 text-base mr-2 flex-shrink-0"></i>
                            <span class="flex-1"><strong class="text-gray-800 dark:text-gray-200">Sección:</strong> {{ $matriculaActiva?->nombreSeccion ?? 'N/A' }}</span>
                        </div>
                    </div>

                    {{-- Botón --}}
                    <button type="button" class="bg-gradient-to-r from-blue-600 to-indigo-600 dark:bg-gray-700/95 hover:from-blue-700 hover:to-indigo-700 dark:from-transparent dark:to-transparent
                    dark:hover:bg-gray-600/95 text-white py-2.5 px-6 rounded-full font-semibold transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 mt-auto mx-auto flex items-center justify-center w-full text-sm" onclick="event.stopPropagation(); seleccionarAlumno({{ $alumno['id_alumno'] }})">
                        <i class="fas fa-arrow-right mr-2"></i>Seleccionar
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
function seleccionarAlumno(idAlumno) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("principal") }}';

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);

    const alumnoInput = document.createElement('input');
    alumnoInput.type = 'hidden';
    alumnoInput.name = 'idalumno';
    alumnoInput.value = idAlumno;
    form.appendChild(alumnoInput);

    document.body.appendChild(form);
    form.submit();
}
</script>
@else
{{-- Tarjeta cuando no hay alumnos vinculados --}}
<div class="container-fluid py-4">
    <div class="max-w-md mx-auto mt-12 rounded-3xl overflow-hidden bg-gradient-to-br from-blue-600 to-indigo-600 dark:from-blue-700 dark:to-indigo-800 shadow-2xl p-12 text-center border border-blue-200 dark:border-blue-800">
        <div class="text-white text-6xl mb-6">
            <i class="fas fa-user-slash"></i>
        </div>
        <h3 class="text-white text-2xl font-bold mb-4">No hay alumnos vinculados</h3>
        <p class="text-white/90 text-base leading-relaxed">
            Actualmente no tienes ningún alumno asociado a tu cuenta.<br>
            Por favor, contacta con el administrador del sistema para vincular alumnos.
        </p>
    </div>
</div>
@endif
