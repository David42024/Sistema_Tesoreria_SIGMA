<?php

namespace App\Http\Controllers;

use App\Helpers\CRUDTablePage;
use App\Helpers\ExcelExportHelper;
use App\Helpers\PDFExportHelper;
use App\Helpers\RequestHelper;
use App\Helpers\TableAction;
use App\Helpers\Tables\AdministrativoHeaderComponent;
use App\Helpers\Tables\AdministrativoSidebarComponent;
use App\Helpers\Tables\CautionModalComponent;
use App\Helpers\Tables\CRUDTableComponent;
use App\Helpers\Tables\FilterConfig;
use App\Helpers\Tables\PaginatorRowsSelectorComponent;
use App\Helpers\Tables\SearchBoxComponent;
use App\Helpers\Tables\TableButtonComponent;
use App\Helpers\Tables\TableComponent;
use App\Helpers\Tables\TablePaginator;
use App\Interfaces\IExporterService;
use App\Interfaces\IExportRequestFactory;
use App\Models\Alumno;
use App\Models\Grado;
use App\Models\Matricula;
use App\Models\NivelEducativo;
use App\Models\Seccion;
use App\Services\Matricula\GeneraConstanciaMatricula;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MatriculaController extends Controller
{

    private static function doSearch($sqlColumns, $search, $pagination, $appliedFilters = [])
    {

        $query = Matricula::where('estado', '=', '1')
            ->whereHas('alumno', fn($q) => $q->where('estado', 1))
            ->whereHas('grado', fn($q) => $q->where('estado', 1))
            ->whereExists(function ($sub) {
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
                'Año Escolar' => 'id_periodo_academico',
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
            } elseif ($dbColumn === 'grado') {
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

        if ($pagination === null) {
            return $query->get();
        }
        return $query->paginate($pagination);
    }


    public function viewAll(Request $request)
    {
        return $this->index($request, true);
    }
    public function index(Request $request, bool $long = false)
    {
        $sqlColumns = ["id_matricula", "fecha_matricula", "id_periodo_academico", "id_alumno", "id_grado", "nombreSeccion", "escala", "observaciones",];
        $resource = "alumnos";

        $params = RequestHelper::extractSearchParams($request);

        $page = CRUDTablePage::new()
            ->title("Matrículas")
            ->sidebar(new AdministrativoSidebarComponent())
            ->header(new AdministrativoHeaderComponent());

        $content = CRUDTableComponent::new()
            ->title("Matrículas");

        $filterButton = new TableButtonComponent("tablesv2.buttons.filtros");
        $content->addButton($filterButton);

        /* Definición de botones */
        $descargaButton = new TableButtonComponent("tablesv2.buttons.download");
        $createNewEntryButton = new TableButtonComponent("tablesv2.buttons.createNewEntry", ["redirect" => "matricula_create"]);

        if (!$long) {
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermas", ["redirect" => "matricula_viewAll"]);
        } else {
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermenos", ["redirect" => "matricula_view"]);
            $params->showing = 100;
        }

        $content->addButton($vermasButton);
        $content->addButton($descargaButton);
        $content->addButton($createNewEntryButton);

        /* Paginador */
        $paginatorRowsSelector = new PaginatorRowsSelectorComponent();
        if ($long)
            $paginatorRowsSelector = new PaginatorRowsSelectorComponent([100]);
        $paginatorRowsSelector->valueSelected = $params->showing;
        $content->paginatorRowsSelector($paginatorRowsSelector);

        /* Searchbox */
        $searchBox = new SearchBoxComponent();
        $searchBox->placeholder = "Buscar...";
        $searchBox->value = $params->search;
        $content->searchBox($searchBox);

        /* Modales usados */
        $cautionModal = CautionModalComponent::new()
            ->cautionMessage('¿Estás seguro?')
            ->action('Estás eliminando la Matrícula')
            ->columns(['ID', 'Alumno', 'Grado'])
            ->rows(['id_matricula', 'alumno_nombre', 'grado_nombre']) // Ajustar según lo que queramos mostrar
            ->lastWarningMessage('Esta acción no se puede deshacer.')
            ->confirmButton('Sí, bórralo')
            ->cancelButton('Cancelar')
            ->isForm(true)
            ->dataInputName('id')
            ->build();

        $page->modals([$cautionModal]);

        /* Lógica del controller */
        $query = static::doSearch($sqlColumns, $params->search, $params->showing, $params->applied_filters);

        if ($params->page > $query->lastPage()) {
            $params->page = 1;
            $query = static::doSearch($sqlColumns, $params->search, $params->showing, $params->applied_filters);
        }

        // Filtros
        $gradosExistentes = Grado::where("estado", 1)
            ->pluck("nombre_grado")->unique()->values();

        $seccionesExistentes = Seccion::where("estado", 1)
            ->pluck("nombreSeccion")->unique()->values();

        $filterConfig = new FilterConfig();
        $filterConfig->filters = [
            "ID",
            "Fecha de matricula",
            "Año Escolar",
            "Alumno",
            "Grado",
            "Seccion",
            "Escala",
            "Observaciones"
        ];

        $filterConfig->filterOptions = [
            'Grado' => $gradosExistentes,
            'Seccion' => $seccionesExistentes,
        ];
        $content->filterConfig = $filterConfig;

        $table = new TableComponent();
        $table->columns = [
            'ID',
            'Fecha Matricula',
            'Año Escolar',
            'Alumno',
            'Nivel',
            'Grado',
            'Seccion',
            'Escala',
            'Observaciones'
        ];
        $table->rows = [];

        foreach ($query as $itemmatricula) {
            array_push(
                $table->rows,
                [
                    $itemmatricula->id_matricula,
                    Carbon::parse($itemmatricula->fecha_matricula)->format('d/m/Y'),
                    $itemmatricula->periodoAcademico->nombre ?? 'N/A',
                    $itemmatricula->alumno->apellido_paterno . ' ' . $itemmatricula->alumno->apellido_materno . ' ' . $itemmatricula->alumno->primer_nombre . ' ' . $itemmatricula->alumno->otros_nombres,
                    $itemmatricula->grado->nivelEducativo->nombre_nivel,
                    $itemmatricula->grado->nombre_grado,
                    $itemmatricula->nombreSeccion,
                    $itemmatricula->escala,
                    $itemmatricula->observaciones,
                    // Hidden fields for modal
                    'hidden_data' => [
                        'id_matricula' => $itemmatricula->id_matricula,
                        'alumno_nombre' => $itemmatricula->alumno->apellido_paterno . ' ...',
                        'grado_nombre' => $itemmatricula->grado->nombre_grado
                    ]
                ]
            );
        }

        $table->actions = [
            new TableAction("generar_constancia", "matricula_generar_constancia", $resource),
            new TableAction("edit", "matricula_edit", $resource),
            new TableAction("delete", '', $resource),
        ];

        $paginator = new TablePaginator($params->page, $query->lastPage(), []);
        $table->paginator = $paginator;

        $content->tableComponent($table);

        $page->content($content->build());

        return $page->render();
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

        $resultado_alumnos = $alumnos->map(function ($alumno) {
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

        $periodosAcademicos = \App\Models\PeriodoAcademico::where('estado', 1)
            ->select('id_periodo_academico', 'nombre')
            ->get()
            ->map(function($periodo) {
                return [
                    'id' => $periodo->id_periodo_academico,
                    'descripcion' => $periodo->nombre
                ];
            });

        $niveles = NivelEducativo::where("estado", "=", "1")
            ->select('id_nivel', 'nombre_nivel')
            ->distinct()
            ->get();

        $grados = Grado::where("estado", "=", "1")
            ->select('id_grado', 'nombre_grado', 'id_nivel')
            ->distinct()
            ->get();

        $secciones = Seccion::where("estado", "=", "1")
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
            'periodosAcademicos' => $periodosAcademicos,
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
            'id_periodo_academico' => 'required|exists:periodos_academicos,id_periodo_academico',
            'nivel_educativo' => 'required',
            'grado' => 'required',
            'seccion' => [
                'required',
                $this->validarCombinacionUnica($request, $seccionData),
            ],
        ], [
            'alumno.required' => 'El alumno es obligatorio.',
            'id_periodo_academico.required' => 'El periodo académico es obligatorio.',
            'nivel_educativo.required' => 'El nivel educativo es obligatorio.',
            'grado.required' => 'El grado es obligatorio.',
            'seccion.required' => 'La sección es obligatoria.',
        ]);

        $matricula = Matricula::create([
            'id_alumno' => $request->alumno,
            'id_periodo_academico' => $request->id_periodo_academico,
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
                ->where('id_periodo_academico', $request->id_periodo_academico)
                ->exists();

            if ($exists) {
                $fail('Este alumno ya está matriculado en el periodo académico seleccionado.');
            }
        };
    }

    private function validarCombinacionUnica($request, $seccionData)
    {
        return function ($attribute, $value, $fail) use ($request, $seccionData) {
            $exists = Matricula::where('id_alumno', $request->alumno)
                ->where('id_periodo_academico', $request->id_periodo_academico)
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
        $id_periodo_academico = $matricula->id_periodo_academico;

        $grado = $matricula->grado;
        $id_grado = $matricula->seccion->id_grado;
        $nombreSeccion = $matricula->seccion->nombreSeccion;
        $seccion = $id_grado . '|' . $nombreSeccion;
        $nivel_educativo = $grado->nivelEducativo;
        $observaciones = $matricula->observaciones;
        $fecha_matricula = $matricula->fecha_matricula;
        $alumnos = Alumno::where("estado", "=", "1")->get();

        $resultado_alumnos = $alumnos->map(function ($alumno) {
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

        $periodosAcademicos = \App\Models\PeriodoAcademico::where('estado', 1)
            ->select('id_periodo_academico', 'nombre')
            ->get()
            ->map(function($periodo) {
                return [
                    'id' => $periodo->id_periodo_academico,
                    'descripcion' => $periodo->nombre
                ];
            })->toArray();

        $niveles = NivelEducativo::where("estado", "=", "1")
            ->select('id_nivel', 'nombre_nivel')
            ->distinct()
            ->get();

        $grados = Grado::where("estado", "=", "1")
            ->select('id_grado', 'nombre_grado', 'id_nivel')
            ->distinct()
            ->get();

        $secciones = Seccion::where("estado", "=", "1")
            ->select('id_grado', 'nombreSeccion')
            ->distinct()
            ->get();



        $data = [
            'return' => route('grado_view', ['abort' => true]),
            'id' => $id,
            'alumnos' => $resultado_alumnos,
            'periodosAcademicos' => $periodosAcademicos,
            'grados' => $grados,
            'secciones' => $secciones,
            'niveles' => $niveles,
            'default' => [
                'alumno' => $alumno->id_alumno,
                'id_periodo_academico' => $id_periodo_academico,
                'nivel_educativo' => $nivel_educativo->id_nivel,
                'grado' => $grado->id_grado,
                'seccion' => $seccion,
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
            'id_periodo_academico' => 'required|exists:periodos_academicos,id_periodo_academico',
            'nivel_educativo' => 'required',
            'grado' => 'required',
            'seccion' => [
                'required',
                function ($attribute, $value, $fail) use ($request, $seccionData) {
                    $exists = Matricula::where('id_alumno', $request->alumno)
                        ->where('id_periodo_academico', $request->id_periodo_academico)
                        ->where('id_grado', $seccionData['id_grado'])
                        ->where('nombreSeccion', $seccionData['nombreSeccion'])
                        ->exists();

                    if ($exists) {
                        $fail('Esta combinación de alumno, periodo académico y sección ya existe.');
                    }
                },
            ],
        ], [
            'alumno.required' => 'El alumno es obligatorio.',
            'id_periodo_academico.required' => 'El periodo académico es obligatorio.',
            'nivel_educativo.required' => 'El nivel educativo es obligatorio.',
            'grado.required' => 'El grado es obligatorio.',
            'seccion.required' => 'La sección es obligatoria.',
            'seccion.unique' => 'Esta combinación de docente, curso, periodo académico y sección ya existe.',
        ]);

        $matricula = Matricula::findOrFail($id);


        $matricula->id_alumno = $request->input('alumno');
        $matricula->id_periodo_academico = $request->input('id_periodo_academico');
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
        $mesActual = (int) date('n'); // 1-12
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


    public function export(Request $request, IExportRequestFactory $requestFactory, IExporterService $exporterService)
    {
        $sqlColumns = ["id_matricula", "fecha_matricula", "id_periodo_academico", "id_alumno", "id_grado", "nombreSeccion", "escala", "observaciones",];

        $params = RequestHelper::extractSearchParams($request);

        $query = static::doSearch($sqlColumns, $params->search, null, $params->applied_filters);

        $data = $query->map(function ($matricula) {
            $alumno = $matricula->alumno;
            $grado = $matricula->grado;
            $seccion = $matricula->seccion;

            return [
                $matricula->periodoAcademico->nombre ?? 'N/A',
                $grado->nombre_grado,
                $seccion->nombreSeccion,
                $alumno->apellido_paterno . " " . $alumno->apellido_materno,
                $alumno->primer_nombre . " " . $alumno->otros_nombres,
            ];
        });

        $title = 'Listado de Matrículas';
        $headers = ["Año Escolar", "Grado", "Sección", "Apellidos", "Nombres"];
        $exportRequest = $requestFactory->create(
            $title,
            $headers,
            $data->toArray(),
            ['filename' => 'matriculas_' . date('d_m_Y')]
        );

        return $exporterService->exportAsResponse($request, $exportRequest);
    }

    public function generarConstancia($id, GeneraConstanciaMatricula $service)
    {
        return $service->generarAsResponse($id);
    }
}