<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selección de Alumno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            padding: 2rem 0;
        }

        .page-title {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 2rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .student-card {
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            height: 100%;
        }

        .student-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.2);
        }

        /* Colores claros y suaves para cada tarjeta */
        .card-gradient-1 {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        }

        .card-gradient-2 {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
        }

        .card-gradient-3 {
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
        }

        .card-gradient-4 {
            background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%);
        }

        .card-gradient-5 {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
        }

        .card-gradient-6 {
            background: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%);
        }

        .student-photo {
            width: 130px;
            height: 130px;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .student-name {
            color: #2c3e50;
            font-weight: 700;
            font-size: 1.3rem;
            margin-top: 1rem;
            margin-bottom: 1rem;
        }

        .info-box {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 1.5rem;
            margin-top: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.8rem;
            color: #34495e;
            font-size: 0.95rem;
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-item i {
            color: #3498db;
            width: 25px;
            font-size: 1.1rem;
        }

        .btn-select {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 0.7rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 1rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .btn-select:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0,0,0,0.3);
            color: white;
        }

        .no-students-alert {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            color: #5a6c7d;
        }
    </style>
</head>
<body>
    <div class="container main-container">
        <h2 class="text-center page-title">
            <i class="fas fa-users me-2"></i>Seleccione un Alumno
        </h2>

        @if(count($alumnos) > 0)
            <div class="row g-4">
                @foreach($alumnos as $index => $alumno)
                    @php
                        $matriculaActiva = \App\Models\Matricula::where('id_alumno', $alumno['id_alumno'])
                            ->where('estado', true)
                            ->orderBy('id_periodo_academico', 'desc')
                            ->first();
                        $gradientClass = 'card-gradient-' . (($index % 6) + 1);
                    @endphp
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card student-card {{ $gradientClass }}"
                             onclick="seleccionarAlumno({{ $alumno['id_alumno'] }})">
                            <div class="card-body text-center p-4">
                                {{-- Foto del alumno --}}
                                <div class="mb-3">
                                    <img src="{{ $alumno['foto'] ? asset('storage/' . $alumno['foto']) : asset('storage/fotos/alumnos/default.jpg') }}"
                                         alt="Foto"
                                         class="rounded-circle student-photo">
                                </div>

                                {{-- Nombre completo --}}
                                <h5 class="student-name">
                                    {{ $alumno['primer_nombre'] }}
                                    {{ $alumno['apellido_paterno'] }}
                                    {{ $alumno['apellido_materno'] }}
                                </h5>

                                {{-- Información del alumno --}}
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

                                {{-- Botón de selección --}}
                                <button type="button" class="btn btn-select"
                                        onclick="event.stopPropagation(); seleccionarAlumno({{ $alumno['id_alumno'] }})">
                                    <i class="fas fa-arrow-right me-2"></i>Seleccionar
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert no-students-alert text-center">
                <i class="fas fa-info-circle fa-3x mb-3"></i>
                <h4>No tienes alumnos vinculados</h4>
                <p class="mb-0">Contacta con el administrador para vincular alumnos a tu cuenta.</p>
            </div>
        @endif
    </div>

    {{-- Script para manejar la selección --}}
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
