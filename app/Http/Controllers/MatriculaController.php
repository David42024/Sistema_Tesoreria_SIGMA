<?php

namespace App\Http\Controllers;

use App\Helpers\TableAction;
use App\Models\Alumno;
use App\Models\Grado;
use App\Models\Matricula;
use App\Models\NivelEducativo;
use App\Models\Seccion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MatriculaController extends Controller
{
    
    private static function doSearch($sqlColumns, $search, $pagination, $appliedFilters = []){
        
        $query = Matricula::where('estado', '=', '1')
        ->whereHas('alumno', fn($q) => $q->where('estado', 1))
        ->whereHas('grado', fn($q) => $q->where('estado', 1))
            ->whereExists(function($sub){
            $sub->select(\DB::raw(1))
                ->from('secciones')
                ->whereColumn('secciones.id_grado', 'matriculas.id_grado')
                ->whereColumn('secciones.nombreSeccion', 'matriculas.nombreSeccion')
                ->where('secciones.estado', 1);
        });
        
        if (isset($search)) {
            $query->where(function ($q) use ($search) {
                // Buscar en columnas propias
                $q->where('id_matricula', 'LIKE', "%{$search}%")
                ->orWhere('año_escolar', 'LIKE', "%{$search}%")
                ->orWhere('escala', 'LIKE', "%{$search}%")
                ->orWhere('observaciones', 'LIKE', "%{$search}%");

                // Buscar en la relación Alumno
                $q->orWhereHas('alumno', function ($sub) use ($search) {
                    $sub->where('apellido_paterno', 'LIKE', "%{$search}%")
                        ->orWhere('apellido_materno', 'LIKE', "%{$search}%")
                        ->orWhere('primer_nombre', 'LIKE', "%{$search}%")
                        ->orWhere('otros_nombres', 'LIKE', "%{$search}%");
                });

                // Buscar en la relación Grado
                $q->orWhereHas('grado', function ($sub) use ($search) {
                    $sub->where('nombre_grado', 'LIKE', "%{$search}%");
                });

                // Buscar en la relación Seccion
                $q->orWhereHas('seccion', function ($sub) use ($search) {
                    $sub->where('nombreSeccion', 'LIKE', "%{$search}%");
                });
            });
        }

        

        foreach ($appliedFilters as $filter) {
            $columnName = $filter['key'];
            $value = $filter['value'];

            // Mapeo
            $columnMap = [
                'ID' => 'id_matricula',
                'Fecha de matricula' => 'fecha_matricula',
                'Año Escolar' => 'año_escolar',
                'Alumno' => 'alumno',
                'Grado' => 'grado',
                'Seccion' => 'nombreSeccion',
                'Escala' => 'escala',
                'Observaciones' => 'observaciones'
            ];

            $dbColumn = $columnMap[$columnName] ?? strtolower($columnName);


            if ($columnName === 'Alumno') {
                // Filtro especial en relación personal
                $query->whereHas('alumno', function ($q) use ($value) {
                    $q->where(function ($q2) use ($value) {
                        $q2->where('apellido_paterno', 'LIKE', "%{$value}%")
                        ->orWhere('apellido_materno', 'LIKE', "%{$value}%")
                        ->orWhere('primer_nombre', 'LIKE', "%{$value}%")
                        ->orWhere('otros_nombres', 'LIKE', "%{$value}%");
                    });
                });
            }  elseif ($dbColumn === 'grado') {
                $query->whereHas('grado', function ($q) use ($value) {
                    $q->where('nombre_grado', 'LIKE', "%{$value}%");
                });
            } elseif ($dbColumn === 'seccion') {
                $query->whereHas('seccion', function ($q) use ($value) {
                    $q->where('nombreSeccion', 'LIKE', "%{$value}%");
                });
            } elseif ($dbColumn === 'id_matricula') {
                if (is_numeric($value)) {
                    $query->where($dbColumn, '=', $value);
                } else {
                    $query->where($dbColumn, 'LIKE', "%{$value}%");
                }
            } else {
                $query->where($dbColumn, 'LIKE', "%{$value}%");
            }
        }

        return $query->paginate($pagination);
    }


   
    public function index(Request $request)
    {
        $sqlColumns = ["id_matricula","fecha_matricula","año_escolar","id_alumno","id_grado","nombreSeccion","escala","observaciones",];
        $tipoDeRecurso = "alumnos";


        $pagination = $request->input('showing', 10);
        $paginaActual = $request->input('page', 1);
        $search = $request->input('search');

         $appliedFilters = json_decode($request->input('applied_filters', '[]'), true) ?? [];

        if (!is_numeric($paginaActual) || $paginaActual <= 0) $paginaActual = 1;
        if (!is_numeric($pagination) || $pagination <= 0) $pagination = 10;

        $query = MatriculaController::doSearch($sqlColumns, $search, $pagination, $appliedFilters);

        if ($paginaActual > $query->lastPage()){
            $paginaActual = 1;
            $request['page'] = $paginaActual;
            $query = MatriculaController::doSearch($sqlColumns, $search, $pagination, $appliedFilters);
        }

        //Para los selects

        $gradosExistentes = Grado::where("estado",1)
        ->pluck("nombre_grado")->unique()->values();

        $seccionesExistentes = Seccion::where("estado",1)
        ->pluck("nombreSeccion")->unique()->values();

         $data = [
            'titulo' => 'Matriculas',
            'columnas' => [
                'ID',
                'Fecha Matricula',
                'Año Escolar',
                'Alumno',
                'Nivel',
                'Grado',
                'Seccion',
                'Escala',
                'Observaciones'
            ],
            'filas' => [],
            'showing' => $pagination,
            'paginaActual' => $paginaActual,
            'totalPaginas' => $query->lastPage(),
            'resource' => $tipoDeRecurso,
            'view' => 'matricula_view',
            'create' => 'matricula_create',
            'edit' => 'matricula_edit',
            'delete' => 'matricula_delete',
            'filters' => $data['columnas'] ?? [],
            'filterOptions' => [
                'Grado' => $gradosExistentes,
                'Seccion' => $seccionesExistentes,
            ],
            'actions' => [
                new TableAction("edit", "matricula_edit", $tipoDeRecurso),
                new TableAction("delete", '', $tipoDeRecurso),
            ]
        ];

        if ($request->input("created", false)){
            $data['created'] = $request->input('created');
        }

        if ($request->input("edited", false)){
            $data['edited'] = $request->input('edited');
        }

        if ($request->input("abort", false)){
            $data['abort'] = $request->input('abort');
        }

        if ($request->input("deleted", false)){
            $data['deleted'] = $request->input('deleted');
        }

        foreach ($query as $itemmatricula){
            array_push($data['filas'],
            [
                $itemmatricula->id_matricula,
                $itemmatricula->fecha_matricula,
                $itemmatricula->año_escolar,
                $itemmatricula->alumno->apellido_paterno . ' ' . $itemmatricula->alumno->apellido_materno . ' '. $itemmatricula->alumno->primer_nombre . ' '. $itemmatricula->alumno->otros_nombres ,
                $itemmatricula->grado->nivelEducativo->nombre_nivel,
                $itemmatricula->grado->nombre_grado,
                $itemmatricula->nombreSeccion,
                $itemmatricula->escala,
                $itemmatricula->observaciones
            ]); 
        }


        return view('gestiones.matricula.index', compact('data')); 

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $nivel = $request->input('nivel_educativo');
        $grado = $request->input('grado');
        $seccion = $grado . '|' . $request->input('seccion');

        $alumnos = Alumno::where("estado", "=", "1")->get();

        $resultado_alumnos = $alumnos->map(function($alumno) {
            return [
                'id' => $alumno->id_alumno, // o el campo de tu PK
                'nombres' => trim(
                    $alumno->apellido_paterno . ' ' .
                    $alumno->apellido_materno . ' ' .
                    $alumno->primer_nombre . ' ' .
                    $alumno->otros_nombres
                )
            ];
        })->values()->toArray();

        $añosEscolares = [
            ['id' => '2025', 'descripcion' => '2025'],
            ['id' => '2026', 'descripcion' => '2026']
        ];

        $niveles = NivelEducativo::where("estado","=","1")
            ->select('id_nivel', 'nombre_nivel')
            ->distinct()
            ->get();

        $grados = Grado::where("estado","=","1")
            ->select('id_grado', 'nombre_grado', 'id_nivel')
            ->distinct()
            ->get();

        $secciones = Seccion::where("estado","=","1")
            ->select('id_grado', 'nombreSeccion')
            ->distinct()
            ->get();

        $escalas = [
            ['id' => 'A', 'descripcion' => 'A'],
            ['id' => 'B', 'descripcion' => 'B'],
            ['id' => 'C', 'descripcion' => 'C'],
            ['id' => 'D', 'descripcion' => 'D'],
            ['id' => 'E', 'descripcion' => 'E']
        ];

        $data = [
            'return' => route('matricula_view', ['abort' => true]),
            'alumnos' => $resultado_alumnos,
            'añosEscolares' => $añosEscolares,
            'escalas' => $escalas,
            'grados' => $grados,
            'secciones' => $secciones,
            "niveles" => $niveles,
            'nivelSeleccionado' => $nivel,
            'gradoSeleccionado' => $grado,
            'seccionSeleccionada' => $seccion
        ];

        return view('gestiones.matricula.create', compact('data'));

    }

    public function createNewEntry(Request $request)
{
    $seccionData = $this->parseSeccionValue($request->seccion);

    $request->validate([
        'alumno' => [
            'required',
            $this->validarAlumnoYaMatriculado($request),
        ],
        'año_escolar' => 'required',
        'nivel_educativo' => 'required',
        'grado' => 'required',
        'seccion' => [
            'required',
            $this->validarCombinacionUnica($request, $seccionData),
        ],
    ], [
        'alumno.required' => 'El alumno es obligatorio.',
        'año_escolar.required' => 'El año escolar es obligatorio.',
        'nivel_educativo.required' => 'El nivel educativo es obligatorio.',
        'grado.required' => 'El grado es obligatorio.',
        'seccion.required' => 'La sección es obligatoria.',
    ]);

        $matricula = Matricula::create([
            'id_alumno' => $request->alumno,
            'año_escolar' => $request->año_escolar,
            'fecha_matricula' => Carbon::now(),
            'id_grado' => $seccionData['id_grado'],
            'nombreSeccion' => $seccionData['nombreSeccion'],
            'escala' => $request->escala,
            'observaciones' => $request->observaciones
        ]);
    
        $matricula->generarDeudas();

        return redirect(route('matricula_view', ['created' => true]));
    }

    private function validarAlumnoYaMatriculado($request)
    {
        return function ($attribute, $value, $fail) use ($request) {
            $exists = Matricula::where('id_alumno', $value)
                ->where('año_escolar', $request->año_escolar)
                ->exists();

            if ($exists) {
                $fail('Este alumno ya está matriculado en el año escolar seleccionado.');
            }
        };
    }

    private function validarCombinacionUnica($request, $seccionData)
    {
        return function ($attribute, $value, $fail) use ($request, $seccionData) {
            $exists = Matricula::where('id_alumno', $request->alumno)
                ->where('año_escolar', $request->año_escolar)
                ->where('id_grado', $seccionData['id_grado'])
                ->where('nombreSeccion', $seccionData['nombreSeccion'])
                ->exists();

            if ($exists) {
                $fail('Esta combinación de alumno y sección ya existe.');
            }
        };
    }


    public function edit(Request $request, $id)
    {
        if (!isset($id)) {
            return redirect(route('matricula_view'));
        }

        $matricula = Matricula::findOrFail($id);
        
        $alumno = $matricula->alumno;
        $año = $matricula->año_escolar;

        $grado = $matricula->grado;
        $id_grado = $matricula->seccion->id_grado;
        $nombreSeccion = $matricula->seccion->nombreSeccion;
        $seccion = $id_grado . '|' . $nombreSeccion;
        $nivel_educativo = $grado->nivelEducativo;
        $observaciones = $matricula->observaciones;
        $fecha_matricula = $matricula->fecha_matricula;
        $alumnos = Alumno::where("estado", "=", "1")->get();

        $resultado_alumnos = $alumnos->map(function($alumno) {
            return [
                'id' => $alumno->id_alumno, // o el campo de tu PK
                'nombres' => trim(
                    $alumno->apellido_paterno . ' ' .
                    $alumno->apellido_materno . ' ' .
                    $alumno->primer_nombre . ' ' .
                    $alumno->otros_nombres
                )
            ];
        })->values()->toArray();

        $añosEscolares = [
            ['id' => '2025', 'descripcion' => '2025'],
            ['id' => '2026', 'descripcion' => '2026']
        ];

        $niveles = NivelEducativo::where("estado","=","1")
            ->select('id_nivel', 'nombre_nivel')
            ->distinct()
            ->get();

        $grados = Grado::where("estado","=","1")
            ->select('id_grado', 'nombre_grado', 'id_nivel')
            ->distinct()
            ->get();

        $secciones = Seccion::where("estado","=","1")
            ->select('id_grado', 'nombreSeccion')
            ->distinct()
            ->get();
        
        

        $data = [
            'return' => route('grado_view', ['abort' => true]),
            'id' => $id,
            'alumnos' => $resultado_alumnos,
            'añosEscolares' => $añosEscolares,
            'grados' => $grados,
            'secciones' => $secciones,
            'niveles' => $niveles,
            'default' => [
                'alumno' => $alumno->id_alumno,
                'año_escolar' => $año,
                'nivel_educativo' => $nivel_educativo->id_nivel,
                'grado' => $grado->id_grado,
                'seccion' =>  $seccion,
                'observaciones' => $observaciones,
                'fecha_matricula' => $fecha_matricula
            ]
        ];
        
        return view('gestiones.matricula.edit', compact('data'));
    }

    public function editEntry(Request $request, $id)
    {

        if (!isset($id)) {
            return redirect(route('matricula_view'));
        }

        $seccionData = $this->parseSeccionValue($request->seccion);

        $request->validate([
            'alumno' => 'required',
            'año_escolar' => 'required',
            'nivel_educativo' => 'required',
            'grado' => 'required',
            'seccion' => [
                'required',
                function ($attribute, $value, $fail) use ($request, $seccionData) {
                    $exists = Matricula::where('id_alumno', $request->alumno)
                        ->where('año_escolar', $request->año_escolar)
                        ->where('id_grado', $seccionData['id_grado'])
                        ->where('nombreSeccion', $seccionData['nombreSeccion'])
                        ->exists();
                    
                    if ($exists) {
                        $fail('Esta combinación de alumno, año escolar y sección ya existe.');
                    }
                },
            ],
        ], [
            'alumno.required' => 'El alumno es obligatorio.',
            'año_escolar.required' => 'El año escolar es obligatorio.',
            'nivel_educativo.required' => 'El nivel educativo es obligatorio.',
            'grado.required' => 'El grado es obligatorio.',
            'seccion.required' => 'La sección es obligatoria.',
            'seccion.unique' => 'Esta combinación de docente, curso, año escolar y sección ya existe.',
        ]);

        $matricula = Matricula::findOrFail($id);


        $matricula->id_alumno = $request->input('alumno');
        $matricula->año_escolar = $request->input('año_escolar');
        $matricula->fecha_matricula = $request->input('fecha_matricula');
        $matricula->escala = $request->input('escala');
        $matricula->observaciones = $request->input('observaciones');
        $matricula->id_grado = $seccionData['id_grado'];
        $matricula->nombreSeccion = $seccionData['nombreSeccion'];
        $matricula->save();


        return redirect()->route('matricula_view', ['edited' => true]);
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        $matricula = Matricula::findOrFail($id);
        $matricula->update(['estado' => '0']);

        return redirect(route('matricula_view', ['deleted' => true]));
    }

    private function parseSeccionValue($seccionValue)
    {
        if (empty($seccionValue)) {
            return ['id_grado' => null, 'nombreSeccion' => null];
        }

        // Si ya es un array (por alguna razón), devolverlo
        if (is_array($seccionValue)) {
            return $seccionValue;
        }

        // Separar la clave compuesta
        $parts = explode('|', $seccionValue);
        
        if (count($parts) !== 2) {
            throw new \InvalidArgumentException('Formato de sección inválido. Esperado: id_grado|nombreSeccion');
        }

        return [
            'id_grado' => $parts[0],
            'nombreSeccion' => $parts[1]
        ];
    }

    public function getAlumnoInfo($id)
    {
        $alumno = Alumno::findOrFail($id);

        // Supongamos que el alumno tiene un campo "escala"
        $escala = $alumno->escala ?? 'E';

        // Tabla de escalas
        $escalas = [
            'A' => 500.00,
            'B' => 400.00,
            'C' => 300.00,
            'D' => 200.00,
            'E' => 100.00,
        ];

        // Monto mensual
        $montoMensual = $escalas[$escala] ?? 0;

        // Calcular número de cuotas desde mes actual hasta diciembre
        // Las clases empiezan en marzo, así que:
        // - Si estamos en enero o febrero, contar desde marzo
        // - Si estamos en marzo-diciembre, contar desde mes actual
        $mesActual = (int)date('n'); // 1-12
        $mesInicio = ($mesActual < 3) ? 3 : $mesActual; // Si es enero o febrero, empezar desde marzo
        $cuotasPendientes = max(0, 12 - $mesInicio + 1);

        // Total deuda
        $totalDeuda = $montoMensual * $cuotasPendientes;

        return response()->json([
            'escala' => $escala,
            'deuda_mensual' => number_format($montoMensual, 2),
            'cuotas_pendientes' => $cuotasPendientes,
            'deuda_total' => number_format($totalDeuda, 2),
        ]);
    }


}