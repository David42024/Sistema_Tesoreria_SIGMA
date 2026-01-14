@if(count($alumnos) > 0)
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 px-4 flex items-start justify-center pt-12">
    
    <div class="w-full max-w-6xl">
        {{-- Título Principal --}}
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 dark:text-white mb-3">
                Selecciona un Estudiante
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-300">
                Elige al estudiante para ver su información académica y gestionar sus trámites
            </p>
        </div>

        {{-- Carrusel de Alumnos --}}
        <div class="relative">
            
            @if(count($alumnos) > 3)
                {{-- Botón Anterior --}}
                <button type="button" id="prevBtn" 
                    class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-20 z-10 bg-white dark:bg-gray-800 text-gray-800 dark:text-white 
                           rounded-full p-3 shadow-2xl hover:shadow-3xl transition-all duration-300 
                           hover:scale-110 border-2 border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-400
                           disabled:opacity-30 disabled:cursor-not-allowed">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>

                {{-- Botón Siguiente --}}
                <button type="button" id="nextBtn"
                    class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-20 z-10 bg-white dark:bg-gray-800 text-gray-800 dark:text-white 
                           rounded-full p-3 shadow-2xl hover:shadow-3xl transition-all duration-300 
                           hover:scale-110 border-2 border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-400
                           disabled:opacity-30 disabled:cursor-not-allowed">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            @endif

            {{-- Contenedor del Carrusel --}}
            <div class="overflow-visible py-4">
                <div id="carouselTrack" class="flex transition-transform duration-700 ease-out gap-6 {{ count($alumnos) < 3 ? 'justify-center' : '' }}" style="transform: translateX(0)">
                    
                    @foreach($alumnos as $index => $alumno)
                        @php
                            $matriculaActiva = \App\Models\Matricula::where('id_alumno', $alumno['id_alumno'])
                                ->where('estado', true)
                                ->orderBy('id_periodo_academico', 'desc')
                                ->first();

                            $tieneFoto = isset($alumno['foto']) && $alumno['foto'] && file_exists(storage_path('app/public/' . $alumno['foto']));
                            $foto = $tieneFoto ? asset('storage/' . $alumno['foto']) : null;
                            $sexo = $alumno['sexo'] ?? 'M';
                            $colorAvatar = $sexo === 'F' ? 'pink' : 'blue';
                        @endphp

                        <div class="flex-shrink-0 carousel-item transition-all duration-700" style="width: calc((100% - 3rem) / 3);" data-index="{{ $index }}">
                            <div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl 
                                        transition-all duration-300 cursor-pointer overflow-hidden h-full
                                        border-2 border-transparent hover:border-blue-500 dark:hover:border-blue-400
                                        transform hover:-translate-y-2"
                                 onclick="seleccionarAlumno({{ $alumno['id_alumno'] }})">

                                {{-- Decoración de fondo --}}
                                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-500/10 to-indigo-500/10 dark:from-blue-400/5 dark:to-indigo-400/5 rounded-full -mr-16 -mt-16 transition-transform duration-300 group-hover:scale-150"></div>

                                <div class="relative p-6">

                                    {{-- Foto del Alumno --}}
                                    <div class="mb-4 flex justify-center">
                                        <div class="relative">
                                            @if($foto)
                                                <img src="{{ $foto }}"
                                                     class="w-28 h-28 rounded-full object-cover border-4 border-{{ $colorAvatar }}-100 dark:border-{{ $colorAvatar }}-900 shadow-xl
                                                            transition-transform duration-300 group-hover:scale-110"
                                                     alt="Foto alumno">
                                            @else
                                                <div class="w-28 h-28 rounded-full border-4 border-{{ $colorAvatar }}-100 dark:border-{{ $colorAvatar }}-900 shadow-xl
                                                            transition-transform duration-300 group-hover:scale-110
                                                            bg-gradient-to-br from-{{ $colorAvatar }}-400 to-{{ $colorAvatar }}-600
                                                            flex items-center justify-center">
                                                    <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Nombre --}}
                                    <h3 class="text-center text-lg font-bold text-gray-800 dark:text-white mb-1 line-clamp-2">
                                        {{ $alumno['primer_nombre'] }} {{ $alumno['apellido_paterno'] }}
                                    </h3>
                                    <p class="text-center text-sm text-gray-500 dark:text-gray-400 mb-4">
                                        {{ $alumno['apellido_materno'] }}
                                    </p>

                                    {{-- Información del Estudiante --}}
                                    <div class="space-y-2 mb-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-gray-600 dark:text-gray-400 font-medium">DNI:</span>
                                            <span class="text-gray-800 dark:text-gray-200 font-semibold">{{ $alumno['dni'] }}</span>
                                        </div>
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-gray-600 dark:text-gray-400 font-medium">Código:</span>
                                            <span class="text-gray-800 dark:text-gray-200 font-semibold">{{ $alumno['codigo_educando'] }}</span>
                                        </div>
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-gray-600 dark:text-gray-400 font-medium">Grado:</span>
                                            <span class="text-gray-800 dark:text-gray-200 font-semibold">
                                                {{ $matriculaActiva?->grado?->nombre_grado ?? 'N/A' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-gray-600 dark:text-gray-400 font-medium">Sección:</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                                         {{ $matriculaActiva?->nombreSeccion ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-gray-200 text-gray-600 dark:bg-gray-600 dark:text-gray-300' }}">
                                                {{ $matriculaActiva?->nombreSeccion ?? 'Sin asignar' }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Botón Seleccionar --}}
                                    <button type="button"
                                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700
                                               dark:from-blue-500 dark:to-indigo-500 dark:hover:from-blue-600 dark:hover:to-indigo-600
                                               text-white font-semibold py-2.5 px-4 rounded-xl text-sm
                                               transition-all duration-300 shadow-lg hover:shadow-xl
                                               transform hover:scale-105 active:scale-95
                                               flex items-center justify-center gap-2"
                                        onclick="event.stopPropagation(); seleccionarAlumno({{ $alumno['id_alumno'] }})">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                        </svg>
                                        Seleccionar
                                    </button>

                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>

            {{-- Indicadores de página (solo si hay más de 3) --}}
            @if(count($alumnos) > 3)
                <div class="flex justify-center gap-2 mt-8">
                    @for($i = 0; $i < count($alumnos); $i++)
                        <button type="button" 
                            class="indicator w-2.5 h-2.5 rounded-full transition-all duration-300 {{ $i === 0 ? 'bg-blue-600 dark:bg-blue-400 w-8' : 'bg-gray-300 dark:bg-gray-600' }}"
                            data-index="{{ $i }}"
                            onclick="goToSlide({{ $i }})">
                        </button>
                    @endfor
                </div>
            @endif
        </div>
    </div>
</div>

<script>
let currentIndex = 0;
const totalAlumnos = {{ count($alumnos) }};
const itemsPerPage = 3;
const maxIndex = Math.max(0, totalAlumnos - itemsPerPage);

function updateCarousel() {
    const track = document.getElementById('carouselTrack');
    const items = document.querySelectorAll('.carousel-item');
    
    if (items.length === 0) return;
    
    const itemWidth = items[0].offsetWidth;
    const gap = 24; // 1.5rem = 24px
    const offset = -(currentIndex * (itemWidth + gap));
    
    track.style.transform = `translateX(${offset}px)`;
    
    // Actualizar opacidad y escala de las tarjetas
    items.forEach((item, index) => {
        const isVisible = index >= currentIndex && index < currentIndex + itemsPerPage;
        
        if (isVisible) {
            // Tarjetas visibles: opacidad 100%, escala normal
            item.style.opacity = '1';
            item.style.transform = 'scale(1)';
            item.style.filter = 'blur(0px)';
        } else {
            // Tarjetas no visibles: opacidad 30%, escala reducida, blur
            item.style.opacity = '0.3';
            item.style.transform = 'scale(0.9)';
            item.style.filter = 'blur(2px)';
        }
    });
    
    // Actualizar botones
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    if (prevBtn) prevBtn.disabled = currentIndex === 0;
    if (nextBtn) nextBtn.disabled = currentIndex >= maxIndex;
    
    // Actualizar indicadores
    document.querySelectorAll('.indicator').forEach((indicator, index) => {
        if (index === currentIndex) {
            indicator.classList.add('bg-blue-600', 'dark:bg-blue-400', 'w-8');
            indicator.classList.remove('bg-gray-300', 'dark:bg-gray-600', 'w-2.5');
        } else {
            indicator.classList.remove('bg-blue-600', 'dark:bg-blue-400', 'w-8');
            indicator.classList.add('bg-gray-300', 'dark:bg-gray-600', 'w-2.5');
        }
    });
}

function goToSlide(index) {
    currentIndex = Math.max(0, Math.min(index, maxIndex));
    updateCarousel();
}

// Botones de navegación
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');

if (prevBtn) {
    prevBtn.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
            updateCarousel();
        }
    });
}

if (nextBtn) {
    nextBtn.addEventListener('click', () => {
        if (currentIndex < maxIndex) {
            currentIndex++;
            updateCarousel();
        }
    });
}

// Inicializar
updateCarousel();

// Función de selección
function seleccionarAlumno(idAlumno) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("principal") }}';

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';

    const alumnoInput = document.createElement('input');
    alumnoInput.type = 'hidden';
    alumnoInput.name = 'idalumno';
    alumnoInput.value = idAlumno;

    form.appendChild(csrfInput);
    form.appendChild(alumnoInput);
    document.body.appendChild(form);
    form.submit();
}
</script>

@else
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-12 px-4 flex items-center justify-center">
    <div class="max-w-md mx-auto rounded-2xl bg-white dark:bg-gray-800 shadow-2xl p-12 text-center border border-gray-200 dark:border-gray-700">

        <div class="text-white text-6xl mb-6">
            <i class="fas fa-user-slash"></i>
        </div>

        <h3 class="text-white text-2xl font-bold mb-4">
            No hay alumnos vinculados
        </h3>

        <p class="text-white/90">
            Actualmente no tienes ningún alumno asociado a tu cuenta.<br>
            Contacta con el administrador del sistema.
        </p>
    </div>
</div>
@endif
