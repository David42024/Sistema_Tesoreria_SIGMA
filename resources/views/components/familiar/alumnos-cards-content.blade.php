@if(count($alumnos) > 0)
<div class="container-fluid py-4">
    <style>
        .cards-container {
            display: flex;
            flex-wrap: nowrap;
            gap: 1.5rem;
            overflow-x: auto;
            padding: 1rem 0;
            scroll-behavior: smooth;
            justify-content: center;
        }

        .cards-container::-webkit-scrollbar {
            height: 8px;
        }

        .cards-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .cards-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .cards-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .student-card {
            min-width: 280px;
            max-width: 320px;
            flex: 0 0 auto;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
        }

        .student-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 12px 30px rgba(0,0,0,0.2);
        }

        .card-gradient-1 { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
        .card-gradient-2 { background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); }
        .card-gradient-3 { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); }
        .card-gradient-4 { background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%); }
        .card-gradient-5 { background: linear-gradient(135deg, #fad0c4 0%, #ffd1ff 100%); }
        .card-gradient-6 { background: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%); }

        .student-photo {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            margin: 0 auto;
            display: block;
        }

        .student-name {
            color: #2c3e50;
            font-weight: 700;
            font-size: 1.1rem;
            margin-top: 0.8rem;
            margin-bottom: 0.8rem;
            line-height: 1.3;
            min-height: 2.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .info-box {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            padding: 1rem;
            margin-top: 0.8rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.6rem;
            color: #34495e;
            font-size: 0.85rem;
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-item i {
            color: #3498db;
            width: 22px;
            font-size: 1rem;
            margin-right: 8px;
            flex-shrink: 0;
        }

        .info-item span {
            text-align: left;
            flex: 1;
        }

        .btn-select {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 0.6rem 1.5rem;
            border-radius: 20px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 1rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            font-size: 0.9rem;
        }

        .btn-select:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0,0,0,0.25);
            color: white;
        }

        .card-body {
            padding: 1.5rem 1.2rem !important;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .cards-container {
                flex-wrap: wrap;
                justify-content: center;
            }

            .student-card {
                min-width: 260px;
                max-width: 100%;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .cards-container {
                justify-content: center;
            }
        }

        @media (min-width: 1025px) {
            .cards-container {
                justify-content: center;
            }
        }
    </style>

    <div class="cards-container">
        @foreach($alumnos as $index => $alumno)
            @php
                $matriculaActiva = \App\Models\Matricula::where('id_alumno', $alumno['id_alumno'])
                    ->where('estado', true)
                    ->orderBy('año_escolar', 'desc')
                    ->first();
                $gradientClass = 'card-gradient-' . (($index % 6) + 1);
                $foto = isset($alumno['foto']) && $alumno['foto']
                    ? asset('storage/' . $alumno['foto'])
                    : asset('storage/fotos/alumnos/default.jpg');
            @endphp
            <div class="card student-card {{ $gradientClass }}" onclick="seleccionarAlumno({{ $alumno['id_alumno'] }})">
                <div class="card-body text-center">
                    {{-- Foto --}}
                    <div class="mb-2 d-flex justify-content-center">
                        <img src="{{ $foto }}" alt="Foto" class="rounded-circle student-photo">
                    </div>

                    {{-- Nombre --}}
                    <h5 class="student-name">
                        {{ $alumno['primer_nombre'] }} {{ $alumno['apellido_paterno'] }} {{ $alumno['apellido_materno'] }}
                    </h5>

                    {{-- Info box --}}
                    <div class="info-box">
                        <div class="info-item">
                            <i class="fas fa-id-card"></i>
                            <span><strong>DNI:</strong> {{ $alumno['dni'] }}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-barcode"></i>
                            <span><strong>Código:</strong> {{ $alumno['codigo_educando'] }}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-graduation-cap"></i>
                            <span><strong>Grado:</strong> {{ $matriculaActiva?->grado?->nombre_grado ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-users"></i>
                            <span><strong>Sección:</strong> {{ $matriculaActiva?->nombreSeccion ?? 'N/A' }}</span>
                        </div>
                    </div>

                    {{-- Botón --}}
                    <button type="button" class="btn btn-select" onclick="event.stopPropagation(); seleccionarAlumno({{ $alumno['id_alumno'] }})">
                        <i class="fas fa-arrow-right me-2"></i>Seleccionar
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
@endif
