<?php

namespace App\Http\Controllers;

use App\Helpers\ExcelExportHelper;
use App\Helpers\PDFExportHelper;
use App\Helpers\RequestHelper;
use App\Helpers\TableAction;
use App\Interfaces\IExporterService;
use App\Interfaces\IExportRequestFactory;
use App\Models\Catedra;
use App\Models\Curso;
use App\Models\Grado;
use App\Models\NivelEducativo;
use App\Models\Personal;
use App\Models\Seccion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CatedraController extends Controller
{

    private static function doSearch($sqlColumns, $search, $pagination, $appliedFilters = [])
    {

        $query = Catedra::where('estado', '=', '1')
            ->whereHas('personal', fn($q) => $q->where('estado', 1))
            ->whereHas('curso', fn($q) => $q->where('estado', 1))
            ->whereHas('grado', fn($q) => $q->where('estado', 1))
            ->whereExists(function ($sub) {
                $sub->select(\DB::raw(1))
                    ->from('secciones')
                    ->whereColumn('secciones.id_grado', 'catedras.id_grado')
                    ->whereColumn('secciones.nombreSeccion', 'catedras.secciones_nombreSeccion')
                    ->where('secciones.estado', 1);
            });

        if (isset($search)) {
            $query->where(function ($q) use ($search) {
                // Buscar en columnas propias
                $q->where('id_catedra', 'LIKE', "%{$search}%")
                    ->orWhere('año_escolar', 'LIKE', "%{$search}%");

                // Buscar en la relación Personal
                $q->orWhereHas('personal', function ($sub) use ($search) {
                    $sub->where('apellido_paterno', 'LIKE', "%{$search}%")
                        ->orWhere('apellido_materno', 'LIKE', "%{$search}%")
                        ->orWhere('primer_nombre', 'LIKE', "%{$search}%")
                        ->orWhere('otros_nombres', 'LIKE', "%{$search}%");
                });

                // Buscar en la relación Curso
                $q->orWhereHas('curso', function ($sub) use ($search) {
                    $sub->where('nombre_curso', 'LIKE', "%{$search}%");
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
                'ID' => 'id_catedra',
                'Año Escolar' => 'año_escolar',
                'Docente' => 'personal',
                'Curso' => 'curso',
                'Grado' => 'grado',
                'Seccion' => 'secciones_nombreSeccion'
            ];

            $dbColumn = $columnMap[$columnName] ?? strtolower($columnName);

            if ($columnName === 'Docente') {
                // Filtro especial en relación personal
                $query->whereHas('personal', function ($q) use ($value) {
                    $q->where(function ($q2) use ($value) {
                        $q2->where('apellido_paterno', 'LIKE', "%{$value}%")
                            ->orWhere('apellido_materno', 'LIKE', "%{$value}%")
                            ->orWhere('primer_nombre', 'LIKE', "%{$value}%")
                            ->orWhere('otros_nombres', 'LIKE', "%{$value}%");
                    });
                });
            } elseif ($dbColumn === 'curso') {
                // Filtro en relación curso
                $query->whereHas('curso', function ($q) use ($value) {
                    $q->where('nombre_curso', 'LIKE', "%{$value}%");
                });
            } elseif ($dbColumn === 'grado') {
                $query->whereHas('grado', function ($q) use ($value) {
                    $q->where('nombre_grado', 'LIKE', "%{$value}%");
                });
            } elseif ($dbColumn === 'seccion') {
                $query->whereHas('seccion', function ($q) use ($value) {
                    $q->where('secciones_nombreSeccion', 'LIKE', "%{$value}%");
                });
            } elseif ($dbColumn === 'id_catedra') {
                if (is_numeric($value)) {
                    $query->where($dbColumn, '=', $value);
                } else {
                    $query->where($dbColumn, 'LIKE', "%{$value}%");
                }
            } else {
                $query->where($dbColumn, 'LIKE', "%{$value}%");
            }
        }

        if ($pagination === null) {
            // Para exportación: devolver todos los registros
            return $query->get();
        } else {
            // Para vista normal: paginar
            return $query->paginate($pagination);
        }

    }

    public function index(Request $request)
    {
        $sqlColumns = ["id_catedra", "año_escolar", "id_personal", "id_curso", "id_grado", "secciones_nombreSeccion"];
        $tipoDeRecurso = "academica";

        $pagination = $request->input('showing', 10);
        $paginaActual = $request->input('page', 1);
        $search = $request->input('search');

        $appliedFilters = json_decode($request->input('applied_filters', '[]'), true) ?? [];

        if (!is_numeric($paginaActual) || $paginaActual <= 0)
            $paginaActual = 1;
        if (!is_numeric($pagination) || $pagination <= 0)
            $pagination = 10;

        $query = CatedraController::doSearch($sqlColumns, $search, $pagination, $appliedFilters);

        if ($paginaActual > $query->lastPage()) {
            $paginaActual = 1;
            $request['page'] = $paginaActual;
            $query = CatedraController::doSearch($sqlColumns, $search, $pagination, $appliedFilters);
        }

        $cursosExistentes = Curso::where("estado", 1)
            ->pluck("nombre_curso")->unique()->values();

        $gradosExistentes = Grado::where("estado", 1)
            ->pluck("nombre_grado")->unique()->values();

        $seccionesExistentes = Seccion::where("estado", 1)
            ->pluck("nombreSeccion")->unique()->values();

        $data = [
            'titulo' => 'Catedras',
            'columnas' => [
                'ID',
                'Año Escolar',
                'Docente',
                'Curso',
                'Grado',
                'Seccion'
            ],
            'filas' => [],
            'showing' => $pagination,
            'paginaActual' => $paginaActual,
            'totalPaginas' => $query->lastPage(),
            'resource' => $tipoDeRecurso,
            'view' => 'catedra_view',
            'create' => 'catedra_create',
            'edit' => 'catedra_edit',
            'delete' => 'catedra_delete',
            'filters' => $data['columnas'] ?? [],
            'filterOptions' => [
                'Curso' => $cursosExistentes,
                'Grado' => $gradosExistentes,
                'Seccion' => $seccionesExistentes,
            ],
            'actions' => [
                new TableAction("edit", "catedra_edit", $tipoDeRecurso),
                new TableAction("delete", '', $tipoDeRecurso),
            ]
        ];

        if ($request->input("created", false)) {
            $data['created'] = $request->input('created');
        }

        if ($request->input("edited", false)) {
            $data['edited'] = $request->input('edited');
        }

        if ($request->input("abort", false)) {
            $data['abort'] = $request->input('abort');
        }

        if ($request->input("deleted", false)) {
            $data['deleted'] = $request->input('deleted');
        }

        foreach ($query as $itemcatedra) {
            array_push(
                $data['filas'],
                [
                    $itemcatedra->id_catedra,
                    $itemcatedra->año_escolar,
                    $itemcatedra->personal->apellido_paterno . ' ' . $itemcatedra->personal->apellido_materno . ' ' . $itemcatedra->personal->primer_nombre . ' ' . $itemcatedra->personal->otros_nombres,
                    $itemcatedra->curso->nombre_curso,
                    $itemcatedra->grado->nombre_grado,
                    $itemcatedra->seccion->nombreSeccion
                ]
            );
        }


        return view('gestiones.catedra.index', compact('data'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $personales = Personal::where("estado", "=", "1")->get();

        $resultado_personales = $personales->map(function ($personal) {
            return [
                'id' => $personal->id_personal, // o el campo de tu PK
                'nombres' => trim(
                    $personal->apellido_paterno . ' ' .
                    $personal->apellido_materno . ' ' .
                    $personal->primer_nombre . ' ' .
                    $personal->otros_nombres
                )
            ];
        })->values()->toArray();

        $cursos = Curso::where("estado", "=", "1")->get();

        $resultados_cursos = $cursos->map(function ($curso) {
            return [
                'id' => $curso->id_curso,
                'nombres' => trim(
                    $curso->nombre_curso . ' - ' . $curso->nivel->nombre_nivel
                )
            ];
        })->values()->toArray();

        $añosEscolares = [
            ['id' => '2025', 'descripcion' => '2025'],
            ['id' => '2026', 'descripcion' => '2026']
        ];



        $niveles = NivelEducativo::where("estado", "=", "1")->get();

        $grados = Grado::where("estado", "=", "1")->get();

        $secciones = Seccion::where("estado", "=", "1")->get();

        $data = [
            'return' => route('catedra_view', ['abort' => true]),
            'docentes' => $resultado_personales,
            'cursos' => $resultados_cursos,
            'añosEscolares' => $añosEscolares,
            'grados' => $grados,
            'secciones' => $secciones,
            "niveles" => $niveles
        ];

        return view('gestiones.catedra.create', compact('data'));
    }

    public function createNewEntry(Request $request)
    {

        $seccionData = $this->parseSeccionValue($request->seccion);

        $request->validate([
            'docente' => 'required',
            'curso' => 'required',
            'año_escolar' => 'required',
            'nivel_educativo' => 'required',
            'grado' => 'required',
            'seccion' => [
                'required',
                function ($attribute, $value, $fail) use ($request, $seccionData) {
                    $catedraExistente = Catedra::with('personal')
                        ->where('id_curso', $request->curso)
                        ->where('año_escolar', $request->año_escolar)
                        ->where('id_grado', $seccionData['id_grado'])
                        ->where('secciones_nombreSeccion', $seccionData['nombreSeccion'])
                        ->where('estado', '1')
                        ->first();

                    if ($catedraExistente) {
                        $docenteActual = $catedraExistente->personal->apellido_paterno . ' ' .
                            $catedraExistente->personal->apellido_materno . ' ' .
                            $catedraExistente->personal->primer_nombre;

                        $fail("Esta combinación ya está asignada al docente: {$docenteActual}");
                    }
                },
            ],
        ], [
            'docente.required' => 'El docente es obligatorio.',
            'curso.required' => 'El curso es obligatorio.',
            'año_escolar.required' => 'El año escolar es obligatorio.',
            'nivel_educativo.required' => 'El nivel educativo es obligatorio.',
            'grado.required' => 'El grado es obligatorio.',
            'seccion.required' => 'La sección es obligatoria.',
            'seccion.unique' => 'Esta combinación de docente, curso, año escolar y sección ya existe.',
        ]);

        Catedra::create([
            'id_personal' => $request->docente,
            'id_curso' => $request->curso,
            'año_escolar' => $request->año_escolar,
            'id_grado' => $seccionData['id_grado'],
            'secciones_nombreSeccion' => $seccionData['nombreSeccion']
        ]);

        return redirect()->route('catedra_view')->with('success', 'Catedra creada correctamente.');

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

    public function edit(Request $request, $id)
    {
        if (!isset($id)) {
            return redirect(route('catedra_view'));
        }

        $catedra = Catedra::findOrFail($id);

        $personal = $catedra->personal;

        $curso = $catedra->curso;

        $año = $catedra->año_escolar;

        $grado = $catedra->grado;

        $id_grado = $catedra->seccion->id_grado;
        $nombreSeccion = $catedra->seccion->nombreSeccion;

        $seccion = $id_grado . '|' . $nombreSeccion;

        $nivel_educativo = $grado->nivelEducativo;

        $personales = Personal::where("estado", "=", "1")->get();

        $resultado_personales = $personales->map(function ($personal) {
            return [
                'id' => $personal->id_personal, // o el campo de tu PK
                'nombres' => trim(
                    $personal->apellido_paterno . ' ' .
                    $personal->apellido_materno . ' ' .
                    $personal->primer_nombre . ' ' .
                    $personal->otros_nombres
                )
            ];
        })->values()->toArray();

        $cursos = Curso::where("estado", "=", "1")->get();

        $resultados_cursos = $cursos->map(function ($curso) {
            return [
                'id' => $curso->id_curso,
                'nombres' => trim(
                    $curso->nombre_curso . ' - ' . $curso->nivel->nombre_nivel
                )
            ];
        })->values()->toArray();

        $añosEscolares = [
            ['id' => '2025', 'descripcion' => '2025'],
            ['id' => '2026', 'descripcion' => '2026']
        ];

        $niveles = NivelEducativo::where("estado", "=", "1")->get();

        $grados = Grado::where("estado", "=", "1")->get();

        $secciones = Seccion::where("estado", "=", "1")->get();



        $data = [
            'return' => route('grado_view', ['abort' => true]),
            'id' => $id,
            'docentes' => $resultado_personales,
            'cursos' => $resultados_cursos,
            'añosEscolares' => $añosEscolares,
            'grados' => $grados,
            'secciones' => $secciones,
            'niveles' => $niveles,
            'default' => [
                'docente' => $personal->id_personal,
                'curso' => $curso->id_curso,
                'año_escolar' => $año,
                'nivel_educativo' => $nivel_educativo->id_nivel,
                'grado' => $grado->id_grado,
                'seccion' => $seccion
            ]
        ];



        return view('gestiones.catedra.edit', compact('data'));
    }


    public function editEntry(Request $request, $id)
    {

        if (!isset($id)) {
            return redirect(route('catedra_view'));
        }

        $seccionData = $this->parseSeccionValue($request->seccion);

        $request->validate([
            'docente' => 'required',
            'curso' => 'required',
            'año_escolar' => 'required',
            'nivel_educativo' => 'required',
            'grado' => 'required',
            'seccion' => [
                'required',
                function ($attribute, $value, $fail) use ($request, $seccionData) {
                    $exists = Catedra::where('id_personal', $request->docente)
                        ->where('id_curso', $request->curso)
                        ->where('año_escolar', $request->año_escolar)
                        ->where('id_grado', $seccionData['id_grado'])
                        ->where('secciones_nombreSeccion', $seccionData['nombreSeccion'])
                        ->exists();

                    if ($exists) {
                        $fail('Esta combinación de docente, curso, año escolar y sección ya existe.');
                    }
                },
            ],
        ], [
            'docente.required' => 'El docente es obligatorio.',
            'curso.required' => 'El curso es obligatorio.',
            'año_escolar.required' => 'El año escolar es obligatorio.',
            'nivel_educativo.required' => 'El nivel educativo es obligatorio.',
            'grado.required' => 'El grado es obligatorio.',
            'seccion.required' => 'La sección es obligatoria.',
            'seccion.unique' => 'Esta combinación de docente, curso, año escolar y sección ya existe.',
        ]);

        $catedra = Catedra::findOrFail($id);


        $catedra->id_personal = $request->input('docente');
        $catedra->id_curso = $request->input('curso');
        $catedra->año_escolar = $request->input('año_escolar');
        $catedra->id_grado = $seccionData['id_grado'];
        $catedra->secciones_nombreSeccion = $seccionData['nombreSeccion'];
        $catedra->save();


        return redirect()->route('catedra_view')->with('success', 'Catedra actualizada correctamente.');
    }


    public function delete(Request $request)
    {
        $id = $request->input('id');
        $catedra = Catedra::findOrFail($id);
        $catedra->update(['estado' => '0']);

        return redirect()->route('catedra_view')->with('success', 'Catedra desactivada correctamente.');
    }


    public function export(Request $request, IExportRequestFactory $requestFactory, IExporterService $exporterService)
    {
        $sqlColumns = [
            'id_catedra',
            'año_escolar',
            'id_personal',
            'id_curso',
            'id_grado',
            'secciones_nombreSeccion'
        ];

        $params = RequestHelper::extractSearchParams($request);

        $query = static::doSearch($sqlColumns, $params->search, null, $params->applied_filters);
        $query = $query->sortBy('año_escolar');

        $data = $query->map(function ($catedra) {
            $grado = $catedra->grado;
            $seccion = $catedra->seccion;
            $curso = $catedra->curso;
            $personal = $catedra->personal;

            return [
                $catedra->año_escolar,
                $grado->nombre_grado,
                $seccion->nombreSeccion,
                $curso->nombre_curso,
                $personal->codigo_personal,
                $personal->apellido_paterno . " " . $personal->apellido_materno,
                $personal->primer_nombre . " " . $personal->otros_nombres
            ];
        });

        $title = 'Listado de Cátedras';
        $headers = ["Año Escolar", "Grado", "Sección", "Curso", "Código Personal", "Apellidos", "Nombres"];
        $exportRequest = $requestFactory->create(
            $title,
            $headers,
            $data->toArray(),
            ['filename' => 'catedras_' . date('d_m_Y')]
        );

        return $exporterService->exportAsResponse($request, $exportRequest);

    }
}
