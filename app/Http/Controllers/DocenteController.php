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
use App\Models\DepartamentoAcademico;
use App\Models\Personal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DocenteController extends Controller
{
    private static function doSearch($sqlColumns, $search, $pagination, $appliedFilters = [])
    {

        $query = Personal::where('estado', '=', '1');

        if (isset($search)) {
            $query->whereAny($sqlColumns, 'LIKE', "%{$search}%");
        }

        foreach ($appliedFilters as $filter) {
            $columnName = $filter['key'];
            $value = $filter['value'];

            // Mapear nombres de columnas de la vista a nombres de BD
            $columnMap = [
                'ID' => 'id_personal',
                'Codigo de Personal' => 'codigo_personal',
                'DNI' => 'dni',
                'Apellidos' => 'apellido_paterno',
                'Nombres' => 'primer_nombre',
                'Departamento Académico' => 'departamento'
            ];



            $dbColumn = $columnMap[$columnName] ?? strtolower($columnName);


            // Aplicar filtro según el tipo de columna
            if ($columnName === 'Apellidos') {
                // Filtro especial: apellido paterno
                $query->where(function ($q) use ($value) {
                    $q->where('apellido_paterno', 'LIKE', "%{$value}%")
                        ->orWhere('apellido_materno', 'LIKE', "%{$value}%");
                });
            } elseif ($columnName === 'Nombres') {
                // Filtro especial: primer_nombre o otros_nombres
                $query->where(function ($q) use ($value) {
                    $q->where('primer_nombre', 'LIKE', "%{$value}%")
                        ->orWhere('otros_nombres', 'LIKE', "%{$value}%");
                });
            } elseif ($dbColumn === 'id_personal') {
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

    public function index(Request $request, bool $long = false)
    {
        $sqlColumns = ["id_personal", "codigo_personal", "dni", "apellido_paterno", "apellido_materno", "primer_nombre", "otros_nombres", "departamento"];
        $resource = "personal";

        $params = RequestHelper::extractSearchParams($request);

        $page = CRUDTablePage::new()
            ->title("Docentes")
            ->sidebar(new AdministrativoSidebarComponent())
            ->header(new AdministrativoHeaderComponent());

        $content = CRUDTableComponent::new()
            ->title("Docentes");

        $filterButton = new TableButtonComponent("tablesv2.buttons.filtros");
        $content->addButton($filterButton);

        /* Definición de botones */
        $descargaButton = new TableButtonComponent("tablesv2.buttons.download");
        $createNewEntryButton = new TableButtonComponent("tablesv2.buttons.createNewEntry", ["redirect" => "docente_create"]);

        if (!$long) {
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermas", ["redirect" => "docente_viewAll"]);
        } else {
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermenos", ["redirect" => "docente_view"]);
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
            ->action('Estás eliminando el Docente')
            ->columns(['ID', 'DNI', 'Nombre'])
            ->rows(['id_personal', 'dni', 'nombre_completo'])
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

        $departamentosExistentes = DepartamentoAcademico::where("estado", 1)
            ->pluck("nombre")
            ->unique()
            ->values();

        $filterConfig = new FilterConfig();
        $filterConfig->filters = [
            'ID',
            'Codigo de Personal',
            'DNI',
            'Apellidos',
            'Nombres',
            'Departamento Académico'
        ];

        $filterConfig->filterOptions = [
            'Departamento Académico' => $departamentosExistentes,
        ];
        $content->filterConfig = $filterConfig;

        $table = new TableComponent();
        $table->columns = [
            'ID',
            'Codigo de Personal',
            'DNI',
            'Apellidos',
            'Nombres',
            'Departamento Académico'
        ];
        $table->rows = [];

        foreach ($query as $itempersonal) {
            array_push(
                $table->rows,
                [
                    $itempersonal->id_personal,
                    $itempersonal->codigo_personal,
                    $itempersonal->dni,
                    $itempersonal->apellido_paterno . ' ' . $itempersonal->apellido_materno,
                    $itempersonal->primer_nombre . ' ' . $itempersonal->otros_nombres,
                    $itempersonal->departamentos_academicos->nombre,
                    // Hidden modal data
                    'hidden_data' => [
                        'id_personal' => $itempersonal->id_personal,
                        'dni' => $itempersonal->dni,
                        'nombre_completo' => $itempersonal->apellido_paterno . ' ' . $itempersonal->primer_nombre
                    ]
                ]
            );
        }

        $table->actions = [
            new TableAction("edit", "docente_edit", $resource),
            new TableAction("delete", '', $resource),
        ];

        $paginator = new TablePaginator($params->page, $query->lastPage(), []);
        $table->paginator = $paginator;

        $content->tableComponent($table);

        $page->content($content->build());

        return $page->render();
    }

    public function viewAll(Request $request)
    {
        return $this->index($request, true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departamentos = DepartamentoAcademico::where('estado', '=', '1')->get();

        $estadosCiviles = [
            ['id' => 'S', 'descripcion' => 'Soltero'],
            ['id' => 'C', 'descripcion' => 'Casado'],
            ['id' => 'D', 'descripcion' => 'Divorciado'],
            ['id' => 'V', 'descripcion' => 'Viudo'],
        ];

        $categorias = [
            ['id' => 'Titular', 'descripcion' => 'Titular'],
            ['id' => 'Asociado', 'descripcion' => 'Asociado'],
            ['id' => 'Auxiliar', 'descripcion' => 'Auxiliar'],
        ];

        $data = [
            'return' => route('docente_view', ['abort' => true]),
            'departamentos' => $departamentos,
            'estadosCiviles' => $estadosCiviles,
            'categorias' => $categorias
        ];

        return view('gestiones.docente.create', compact('data'));
    }

    public function createNewEntry(Request $request)
    {

        $request->validate([
            'dni' => 'required|string|max:8|unique:personal,dni', // Asegúrate que 'personal' es el nombre de tu tabla
            'primer_nombre' => 'required|string|max:50',
            'otros_nombres' => 'nullable|string|max:50', // 'otros_nombres' puede ser nulo según tu tabla
            'apellido_paterno' => 'required|string|max:50', // Agregado, ya que es requerido en tu tabla (no 'NULL')
            'apellido_materno' => 'required|string|max:50', // Agregado, ya que es requerido en tu tabla (no 'NULL')
            'codigo_de_personal' => 'nullable|string|max:20', // 'codigo_personal' puede ser nulo
            'direccion' => 'nullable|string|max:255',
            'estado_civil' => 'nullable|string|max:1', // char(1)
            'telefono' => 'nullable|string|max:20',
            'seguro_social' => 'nullable|string|max:20',
            'fecha_ingreso' => 'required|date', // 'fecha_ingreso' es requerido
            'categoria' => 'nullable|string|max:50',
            'departamento' => 'nullable|integer', // 'id_departamento' es int
        ], [
            'dni.required' => 'El campo DNI es obligatorio.',
            'dni.string' => 'El DNI debe ser una cadena de texto.',
            'dni.max' => 'El DNI no puede exceder los 8 caracteres.',
            'dni.unique' => 'Ya existe un registro de personal con este DNI.',

            'primer_nombre.required' => 'El campo Primer Nombre es obligatorio.',
            'primer_nombre.string' => 'El Primer Nombre debe ser una cadena de texto.',
            'primer_nombre.max' => 'El Primer Nombre no puede exceder los 50 caracteres.',

            'otros_nombres.string' => 'Otros Nombres deben ser una cadena de texto.',
            'otros_nombres.max' => 'Otros Nombres no pueden exceder los 50 caracteres.',

            'apellido_paterno.required' => 'El campo Apellido Paterno es obligatorio.',
            'apellido_paterno.string' => 'El Apellido Paterno debe ser una cadena de texto.',
            'apellido_paterno.max' => 'El Apellido Paterno no puede exceder los 50 caracteres.',

            'apellido_materno.required' => 'El campo Apellido Materno es obligatorio.',
            'apellido_materno.string' => 'El Apellido Materno debe ser una cadena de texto.',
            'apellido_materno.max' => 'El Apellido Materno no puede exceder los 50 caracteres.',

            'codigo_de_personal.string' => 'El Código de Personal debe ser una cadena de texto.',
            'codigo_de_personal.max' => 'El Código de Personal no puede exceder los 20 caracteres.',

            'direccion.string' => 'La Dirección debe ser una cadena de texto.',
            'direccion.max' => 'La Dirección no puede exceder los 255 caracteres.',

            'estado_civil.string' => 'El Estado Civil debe ser un caracter.',
            'estado_civil.max' => 'El Estado Civil solo puede ser 1 caracter (M/S/C/V/D).',

            'telefono.string' => 'El Teléfono debe ser una cadena de texto.',
            'telefono.max' => 'El Teléfono no puede exceder los 20 caracteres.',

            'seguro_social.string' => 'El Seguro Social debe ser una cadena de texto.',
            'seguro_social.max' => 'El Seguro Social no puede exceder los 20 caracteres.',

            'fecha_ingreso.required' => 'El campo Fecha de Ingreso es obligatorio.',
            'fecha_ingreso.date' => 'La Fecha de Ingreso debe ser una fecha válida.',

            'categoria.string' => 'La Categoría debe ser una cadena de texto.',
            'categoria.max' => 'La Categoría no puede exceder los 50 caracteres.',

            'departamento.integer' => 'El Id de Departamento debe ser un número entero.',
        ]);


        $departamentoId = $request->input('departamento');


        $departamentoNombre = DepartamentoAcademico::findOrFail($departamentoId)->nombre;

        Personal::create([
            'id_usuario' => User::inRandomOrder()->first()?->id_usuario,
            'dni' => $request->input('dni'),
            'primer_nombre' => $request->input('primer_nombre'),
            'otros_nombres' => $request->input('otros_nombres'),
            'apellido_paterno' => $request->input('apellido_paterno'),
            'apellido_materno' => $request->input('apellido_materno'),
            'codigo_personal' => $request->input('codigo_de_personal'),
            'direccion' => $request->input('direccion'),
            'estado_civil' => $request->input('estado_civil'),
            'telefono' => $request->input('telefono'),
            'seguro_social' => $request->input('seguro_social'),
            'fecha_ingreso' => $request->input('fecha_ingreso'),
            'categoria' => $request->input('categoria'),
            'id_departamento' => $departamentoId,
            'departamento' => $departamentoNombre
        ]);

        return redirect(route('docente_view', ['created' => true]));

    }

    public function edit(Request $request, $id)
    {
        if (!isset($id)) {
            return redirect(route('docente_view'));
        }

        $docente = Personal::findOrFail($id);

        $departamentos = DepartamentoAcademico::where('estado', '=', '1')->get();

        $estadosCiviles = [
            ['id' => 'S', 'descripcion' => 'Soltero'],
            ['id' => 'C', 'descripcion' => 'Casado'],
            ['id' => 'D', 'descripcion' => 'Divorciado'],
            ['id' => 'V', 'descripcion' => 'Viudo'],
        ];

        $categorias = [
            ['id' => 'Titular', 'descripcion' => 'Titular'],
            ['id' => 'Asociado', 'descripcion' => 'Asociado'],
            ['id' => 'Auxiliar', 'descripcion' => 'Auxiliar'],
        ];

        $data = [
            'return' => route('docente_view', ['abort' => true]),
            'id' => $id,
            'departamentos' => $departamentos,
            'estadosCiviles' => $estadosCiviles,
            'categorias' => $categorias,
            'default' => [
                'dni' => $docente->dni,
                'primer_nombre' => $docente->primer_nombre,
                'otros_nombres' => $docente->otros_nombres,
                'apellido_paterno' => $docente->apellido_paterno,
                'apellido_materno' => $docente->apellido_materno,
                'codigo_de_personal' => $docente->codigo_personal,
                'direccion' => $docente->direccion,
                'estado_civil' => $docente->estado_civil,
                'telefono' => $docente->telefono,
                'seguro_social' => $docente->seguro_social,
                'fecha_ingreso' => $docente->fecha_ingreso,
                'categoria' => $docente->categoria,
                'departamento' => $docente->id_departamento
            ]
        ];

        return view('gestiones.docente.edit', compact('data'));
    }

    public function editEntry(Request $request, $id)
    {
        if (!isset($id)) {
            return redirect(route('docente_view'));
        }

        $request->validate([
            'dni' => [
                'required',
                'string',
                'max:8',
                Rule::unique('personal', 'dni')->ignore($id, 'id_personal')
            ],
            'primer_nombre' => 'required|string|max:50',
            'otros_nombres' => 'nullable|string|max:50', // 'otros_nombres' puede ser nulo según tu tabla
            'apellido_paterno' => 'required|string|max:50', // Agregado, ya que es requerido en tu tabla (no 'NULL')
            'apellido_materno' => 'required|string|max:50', // Agregado, ya que es requerido en tu tabla (no 'NULL')
            'codigo_de_personal' => 'nullable|string|max:20', // 'codigo_personal' puede ser nulo
            'direccion' => 'nullable|string|max:255',
            'estado_civil' => 'nullable|string|max:1', // char(1)
            'telefono' => 'nullable|string|max:20',
            'seguro_social' => 'nullable|string|max:20',
            'fecha_ingreso' => 'required|date', // 'fecha_ingreso' es requerido
            'categoria' => 'nullable|string|max:50',
            'departamento' => 'nullable|integer', // 'id_departamento' es int
        ], [
            'dni.required' => 'El campo DNI es obligatorio.',
            'dni.string' => 'El DNI debe ser una cadena de texto.',
            'dni.max' => 'El DNI no puede exceder los 8 caracteres.',
            'dni.unique' => 'Ya existe un registro de personal con este DNI.',

            'primer_nombre.required' => 'El campo Primer Nombre es obligatorio.',
            'primer_nombre.string' => 'El Primer Nombre debe ser una cadena de texto.',
            'primer_nombre.max' => 'El Primer Nombre no puede exceder los 50 caracteres.',

            'otros_nombres.string' => 'Otros Nombres deben ser una cadena de texto.',
            'otros_nombres.max' => 'Otros Nombres no pueden exceder los 50 caracteres.',

            'apellido_paterno.required' => 'El campo Apellido Paterno es obligatorio.',
            'apellido_paterno.string' => 'El Apellido Paterno debe ser una cadena de texto.',
            'apellido_paterno.max' => 'El Apellido Paterno no puede exceder los 50 caracteres.',

            'apellido_materno.required' => 'El campo Apellido Materno es obligatorio.',
            'apellido_materno.string' => 'El Apellido Materno debe ser una cadena de texto.',
            'apellido_materno.max' => 'El Apellido Materno no puede exceder los 50 caracteres.',

            'codigo_de_personal.string' => 'El Código de Personal debe ser una cadena de texto.',
            'codigo_de_personal.max' => 'El Código de Personal no puede exceder los 20 caracteres.',

            'direccion.string' => 'La Dirección debe ser una cadena de texto.',
            'direccion.max' => 'La Dirección no puede exceder los 255 caracteres.',

            'estado_civil.string' => 'El Estado Civil debe ser un caracter.',
            'estado_civil.max' => 'El Estado Civil solo puede ser 1 caracter (M/S/C/V/D).',

            'telefono.string' => 'El Teléfono debe ser una cadena de texto.',
            'telefono.max' => 'El Teléfono no puede exceder los 20 caracteres.',

            'seguro_social.string' => 'El Seguro Social debe ser una cadena de texto.',
            'seguro_social.max' => 'El Seguro Social no puede exceder los 20 caracteres.',

            'fecha_ingreso.required' => 'El campo Fecha de Ingreso es obligatorio.',
            'fecha_ingreso.date' => 'La Fecha de Ingreso debe ser una fecha válida.',

            'categoria.string' => 'La Categoría debe ser una cadena de texto.',
            'categoria.max' => 'La Categoría no puede exceder los 50 caracteres.',

            'departamento.integer' => 'El Id de Departamento debe ser un número entero.',
        ]);


        $docente = Personal::findOrFail($id);

        $departamentoId = $request->input('departamento');

        $departamentoNombre = DepartamentoAcademico::findOrFail($departamentoId)->nombre;

        $docente->dni = $request->input('dni');
        $docente->primer_nombre = $request->input('primer_nombre');
        $docente->apellido_paterno = $request->input('apellido_paterno');
        $docente->apellido_materno = $request->input('apellido_materno');
        $docente->codigo_personal = $request->input('codigo_de_personal');
        $docente->direccion = $request->input('direccion');
        $docente->estado_civil = $request->input('estado_civil');
        $docente->telefono = $request->input('telefono');
        $docente->seguro_social = $request->input('seguro_social');
        $docente->fecha_ingreso = $request->input('fecha_ingreso');
        $docente->categoria = $request->input('categoria');
        $docente->id_departamento = $departamentoId;
        $docente->departamento = $departamentoNombre;
        $docente->estado = 1;

        $docente->save();

        return redirect()->route('docente_view', ['edited' => true]);
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        $docente = Personal::findOrFail($id);
        $docente->update(['estado' => '0']);

        return redirect(route('docente_view', ['deleted' => true]));
    }


    public function export(Request $request)
    {
        $format = $request->input('export', 'excel');
        $sqlColumns = ["id_personal", "codigo_personal", "dni", "apellido_paterno", "apellido_materno", "primer_nombre", "otros_nombres", "departamento"];

        $params = RequestHelper::extractSearchParams($request);

        $query = static::doSearch($sqlColumns, $params->search, null, $params->applied_filters);

        if ($format === 'excel') {
            return $this->exportExcel($query);
        } elseif ($format === 'pdf') {
            return $this->exportPdf($query);
        }

        return abort(400, 'Formato no válido');
    }

    private function exportExcel($docentes)
    {
        $headers = ['ID', 'Codigo de Personal', 'DNI', 'Apellidos', 'Nombres', 'Departamento Académico'];
        $fileName = 'docentes_' . date('Y-m-d_H-i-s') . '.xlsx';
        $title = 'Docentes';
        $subject = 'Exportación de Docentes';
        $description = 'Listado de docentes del sistema';

        return ExcelExportHelper::exportExcel(
            $fileName,
            $headers,
            $docentes,
            function ($sheet, $row, $docente) {
                $sheet->setCellValue('A' . $row, $docente->id_personal);
                $sheet->setCellValue('B' . $row, $docente->codigo_personal);
                $sheet->setCellValue('C' . $row, $docente->dni);
                $sheet->setCellValue('D' . $row, $docente->apellido_paterno . ' ' . $docente->apellido_materno);
                $sheet->setCellValue('E' . $row, $docente->primer_nombre . ' ' . $docente->otros_nombres);
                $sheet->setCellValue('F' . $row, $docente->departamentos_academicos->nombre);
            },
            $title,
            $subject,
            $description
        );
    }

    private function exportPdf($docentes)
    {
        try {
            if ($docentes instanceof \Illuminate\Database\Eloquent\Collection) {
                $data = $docentes;
            } elseif ($docentes instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                $data = collect($docentes->items());
            } else {
                $data = collect($docentes);
            }

            if ($data->isEmpty()) {
                return response()->json(['error' => 'No hay datos para exportar'], 400);
            }

            $fileName = 'docentes_' . date('Y-m-d_H-i-s') . '.pdf';

            $rows = $data->map(function ($docente) {
                return [
                    $docente->id_personal ?? 'N/A',
                    $docente->codigo_personal ?? 'N/A',
                    $docente->dni ?? 'N/A',
                    ($docente->apellido_paterno ?? '') . ' ' . ($docente->apellido_materno ?? ''),
                    ($docente->primer_nombre ?? '') . ' ' . ($docente->otros_nombres ?? ''),
                    $docente->departamentos_academicos->nombre ?? 'N/A'
                ];
            })->toArray();

            $html = PDFExportHelper::generateTableHtml([
                'title' => 'Docentes',
                'subtitle' => 'Listado de Docentes',
                'headers' => ['ID', 'Código', 'DNI', 'Apellidos', 'Nombres', 'Departamento'],
                'rows' => $rows,
                'footer' => 'Sistema de Gestión Académica SIGMA - Generado automáticamente',
            ]);

            return PDFExportHelper::exportPdf($fileName, $html);

        } catch (\Exception $e) {
            \Log::error('Error en exportPdf: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error generando PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
