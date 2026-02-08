<?php

namespace App\Http\Controllers;

use App\Models\Administrativo;
use App\Models\User;
use Illuminate\Http\Request;


use App\Helpers\FilteredSearchQuery;
use App\Interfaces\IExporterService;
use App\Interfaces\IExportRequestFactory;
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
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class AdministrativoController extends Controller
{
    private static function doSearch($sqlColumns, $search, $maxEntriesShow, $appliedFilters = []){
        $columnMap = [
            'ID' => 'id_administrativo',
            'DNI' => 'dni',
            'Apellido Paterno' => 'apellido_paterno',
            'Apellido Materno' => 'apellido_materno',
            'Primer Nombre' => 'primer_nombre',
            'Cargo' => 'cargo',
            'Sueldo' => 'sueldo'
        ];

        $query = Administrativo::where('estado', '=', '1');

        FilteredSearchQuery::fromQuery($query, $sqlColumns, $search, $appliedFilters, $columnMap);

        if ($maxEntriesShow === null) {
            return $query->get();
        } else {
            return $query->paginate($maxEntriesShow);
        }
    }

    public function index(Request $request, $long = false){
        $sqlColumns = ['id_administrativo', 'dni', 'apellido_paterno', 'apellido_materno', 'primer_nombre', 'cargo', 'sueldo'];
        $resource = 'administrativa';

        $params = RequestHelper::extractSearchParams($request);
        
        $page = CRUDTablePage::new()
            ->title("Administrativos")
            ->sidebar(new AdministrativoSidebarComponent())
            ->header(new AdministrativoHeaderComponent());
        
        $content = CRUDTableComponent::new()
            ->title("Administrativos");

        $filterButton = new TableButtonComponent("tablesv2.buttons.filtros");
        $content->addButton($filterButton);

        /* Definición de botones */

        $descargaButton = new TableButtonComponent("tablesv2.buttons.download");
        $createNewEntryButton = new TableButtonComponent("tablesv2.buttons.createNewEntry", ["redirect" => "administrativo_create"]);

        if (!$long){
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermas", ["redirect" => "administrativo_viewAll"]);
        } else {
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermenos", ["redirect" => "administrativo_view"]);
            $params->showing = 100;
        }

        $content->addButton($vermasButton);
        $content->addButton($descargaButton);
        $content->addButton($createNewEntryButton);

        /* Paginador */
        $paginatorRowsSelector = new PaginatorRowsSelectorComponent();
        if ($long) $paginatorRowsSelector = new PaginatorRowsSelectorComponent([100]);
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
            ->action('Estás eliminando el Administrativo')
            ->columns(['Cargo', 'DNI', 'Apellidos', 'Nombres', 'Sueldo'])
            ->rows(['', '', '', '', ''])
            ->lastWarningMessage('Borrar esto afectará a todo lo que esté vinculado a este Administrativo.')
            ->confirmButton('Sí, bórralo')
            ->cancelButton('Cancelar')
            ->isForm(true)
            ->dataInputName('id')
            ->build();

        $page->modals([$cautionModal]);

        /* Lógica del controller */
        
        $query = static::doSearch($sqlColumns, $params->search, $params->showing, $params->applied_filters);

        if ($params->page > $query->lastPage()){
            $params->page = 1;
            $query = static::doSearch($sqlColumns, $params->search, $params->showing, $params->applied_filters);
        }

        $filterConfig = new FilterConfig();
        $filterConfig->filters = [
            "ID", "DNI", "Apellido Paterno", "Apellido Materno", "Primer Nombre", "Cargo", "Sueldo"
        ];
        $filterConfig->filterOptions = [
            "Cargo" => ["Director", "Secretaria"]
        ];
        $content->filterConfig = $filterConfig;
        
        $table = new TableComponent();
        $table->columns = ["ID", "Cargo", "DNI", "Apellidos", "Nombres", "Sueldo"];
        $table->rows = [];

        foreach ($query as $administrativo){
            array_push($table->rows,
            [
                $administrativo->id_administrativo,
                $administrativo->cargo,
                $administrativo->dni,
                $administrativo->apellido_paterno . " " . $administrativo->apellido_materno,
                $administrativo->primer_nombre,
                $administrativo->sueldo,
            ]); 
        }
        $table->actions = [
            new TableAction('edit', 'administrativo_edit', $resource),
            new TableAction('delete', '', $resource),
        ];

        $paginator = new TablePaginator($params->page, $query->lastPage(), [
            'search' => $params->search,
            'showing' => $params->showing,
            'applied_filters' => $params->applied_filters
        ]);
        $table->paginator = $paginator;

        $content->tableComponent($table);

        $page->content($content->build());

        return $page->render();
    }

    public function viewAll(Request $request){
        return static::index($request, true);
    }


    public function create(Request $request)
    {
        $estadosciviles = [
            ['id' => 'C', 'descripcion' => 'Casado'],
            ['id' => 'S', 'descripcion' => 'Soltero'],
            ['id' => 'V', 'descripcion' => 'Viudo'],
            ['id' => 'D', 'descripcion' => 'Divorciado']
        ];

        $cargos = [
            ['id' => 'Director', 'descripcion' => 'Director'],
            ['id' => 'Secretaria', 'descripcion' => 'Secretaria']
        ];

        $data = [
            'return' => route('administrativo_view', ['abort' => true]),
            'estadosciviles' => $estadosciviles,
            'cargos' => $cargos,
        ];

        return view('gestiones.administrativo.create', compact('data'));
    }

    public function createNewEntry(Request $request){
        $request->validate([
            'apellido_paterno' => 'required|max:50',
            'apellido_materno' => 'required|max:50',
            'primer_nombre' => 'required|max:50',
            'otros_nombres' => 'required',
            'd_n_i' => 'required|string|max:8|unique:administrativos,dni',
            'teléfono' => 'required|max:20',
            'seguro_social' => 'required|max:20',
            'estado_civil' => 'required|max:1',
            'dirección' => 'required|max:80',
            'fecha_de_ingreso' => 'required|date',
            'cargo' => 'required|max:255',
            'sueldo' => 'required|numeric|max:999999999',
            'nombre_de_usuario' => 'required|string|max:50|unique:users,username',
            'contraseña' => 'required|string|min:6|max:100',
        ],[
            'd_n_i.unique' => 'El DNI ya está registrado.',
            'nombre_de_usuario.unique' => 'El nombre de usuario ya existe.',
            'contraseña.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'apellido_paterno.max' => 'El apellido paterno no puede superar los 50 caracteres.',
            'apellido_materno.required' => 'El apellido materno es obligatorio.',
            'apellido_materno.max' => 'El apellido materno no puede superar los 50 caracteres.',
            'primer_nombre.required' => 'El primer nombre es obligatorio.',
            'primer_nombre.max' => 'El primer nombre no puede superar los 50 caracteres.',
            'otros_nombres.required' => 'Los otros nombres son obligatorios.',
            'otros_nombres.max' => 'Los otros nombres no pueden superar los 50 caracteres.',
            'd_n_i.required' => 'El DNI es obligatorio.',
            'd_n_i.max' => 'El DNI no puede superar los 8 caracteres.',
            'teléfono.required' => 'El teléfono es obligatorio.',
            'teléfono.max' => 'El teléfono no puede superar los 20 caracteres.',
            'seguro_social.required' => 'El seguro social es obligatorio.',
            'seguro_social.max' => 'El seguro social no puede superar los 20 caracteres.',
            'estado_civil.required' => 'El estado civil es obligatorio.',
            'estado_civil.max' => 'El estado civil no puede superar 1 carácter.',
            'dirección.required' => 'La dirección es obligatoria.',
            'dirección.max' => 'La dirección no puede superar los 80 caracteres.',
            'fecha_de_ingreso.required' => 'La fecha de ingreso es obligatoria.',
            'fecha_de_ingreso.date' => 'La fecha de ingreso debe ser una fecha válida.',
            'cargo.required' => 'El cargo es obligatorio.',
            'cargo.max' => 'El cargo no puede superar los 255 caracteres.',
            'sueldo.required' => 'El sueldo es obligatorio.',
            'sueldo.numeric' => 'El sueldo debe ser un número.',
            'sueldo.max' => 'El sueldo no puede superar los 11 dígitos.',
            'nombre_de_usuario.required' => 'El nombre de usuario es obligatorio.',
            'nombre_de_usuario.max' => 'El nombre de usuario no puede superar los 50 caracteres.',
            'contraseña.required' => 'La contraseña es obligatoria.',
            'contraseña.max' => 'La contraseña no puede superar los 100 caracteres.',
        ]);

        $apellidoPaterno = $request->input('apellido_paterno');
        $apellidoMaterno = $request->input('apellido_materno');
        $primerNombre = $request->input('primer_nombre');
        $otrosNombres = $request->input('otros_nombres');
        $dni = $request->input('d_n_i');
        $telefono = $request->input('teléfono');
        $seguroSocial = $request->input('seguro_social');
        $estadoCivil = $request->input('estado_civil');
        $direccion = $request->input('dirección');
        $fechaIngreso = $request->input('fecha_de_ingreso');
        $cargo = $request->input('cargo');
        $sueldo = $request->input('sueldo');
        $nombreUsuario = $request->input('nombre_de_usuario');
        $contraseña = $request->input('contraseña');

        $createdUser = User::create([
            'username' => $nombreUsuario,
            'password' => bcrypt($contraseña),
            'tipo' => 'Administrativo',
        ]);

        Administrativo::create([
            'id_usuario' => $createdUser->getKey(),
            'apellido_paterno' => $apellidoPaterno,
            'apellido_materno' => $apellidoMaterno,
            'primer_nombre' => $primerNombre,
            'otros_nombres' => $otrosNombres,
            'dni' => $dni,
            'direccion' => $direccion,
            'estado_civil' => $estadoCivil,
            'telefono' => $telefono,
            'seguro_social' => $seguroSocial,
            'fecha_ingreso' => $fechaIngreso,
            'cargo' => $cargo,
            'sueldo' => $sueldo,
        ]);

        return redirect()->route('administrativo_view')->with('success', 'Administrativo creado correctamente.');
    }

    public function edit(Request $request, $id){
        if (!isset($id)){
            return redirect(route('administrativo_view'));
        }

        $estadosciviles = [
            ['id' => 'C', 'descripcion' => 'Casado'],
            ['id' => 'S', 'descripcion' => 'Soltero'],
            ['id' => 'V', 'descripcion' => 'Viudo'],
            ['id' => 'D', 'descripcion' => 'Di  vorciado']
        ];

        $cargos = [
            ['id' => 'Director', 'descripcion' => 'Director'],
            ['id' => 'Secretaria', 'descripcion' => 'Secretaria'],
            ['id' => 'Contador', 'descripcion' => 'Contador'],
            ['id' => 'Coordinador', 'descripcion' => 'Coordinador'],
            ['id' => 'Auxiliar', 'descripcion' => 'Auxiliar'],
        ];

        $requested = Administrativo::findOrFail($id);

        $data = [
            'return' => route('administrativo_view', ['abort' => true]),
            'id' => $id,
            'estadosciviles' => $estadosciviles,
            'cargos' => $cargos,
            'default' => [
                'apellido_paterno' => $requested->apellido_paterno,
                'apellido_materno' => $requested->apellido_materno,
                'primer_nombre' => $requested->primer_nombre,
                'otros_nombres' => $requested->otros_nombres,
                'dni' => $requested->dni,
                'direccion' => $requested->direccion,
                'estado_civil' => $requested->estado_civil,
                'telefono' => $requested->telefono,
                'seguro_social' => $requested->seguro_social,
                'fecha_ingreso' => $requested->fecha_ingreso,
                'cargo' => $requested->cargo,
                'sueldo' => $requested->sueldo,
                'estado' => $requested->estado,
            ]
        ];
        
        return view('gestiones.administrativo.edit', compact('data'));
    }

    public function editEntry(Request $request, $id){
        if (!isset($id)){
            return redirect(route('administrativo_view'));
        }

        $request->validate([
            'apellido_paterno' => 'required|string|max:50',
            'apellido_materno' => 'required|string|max:50',
            'primer_nombre' => 'required|string|max:50',
            'otros_nombres' => 'nullable|string|max:50',
            'd_n_i' => 'required|string|max:8|unique:administrativos,dni,' . $id . ',id_administrativo',
            'telefono' => 'nullable|string|max:20',
            'seguro_social' => 'nullable|string|max:20',
            'estado_civil' => 'required|in:S,C,V,D',
            'direccion' => 'required|string|max:255',
            'fecha_de_ingreso' => 'required|date',
            'cargo' => 'required|string|max:100',
            'sueldo' => 'required|numeric|min:0',
        ]);

        $requested = Administrativo::find($id);

        if (isset($requested)){
            $apellidoPaterno = $request->input('apellido_paterno');
            $apellidoMaterno = $request->input('apellido_materno');
            $primerNombre = $request->input('primer_nombre');
            $otrosNombres = $request->input('otros_nombres');
            $dni = $request->input('d_n_i');
            $direccion = $request->input('dirección');
            $estadoCivil = $request->input('estado_civil');
            $telefono = $request->input('teléfono');
            $seguroSocial = $request->input('seguro_social');
            $fechaIngreso = $request->input('fecha_de_ingreso');
            $cargo = $request->input('cargo');
            $sueldo = $request->input('sueldo');

            $requested->update([
                'apellido_paterno' => $apellidoPaterno,
                'apellido_materno' => $apellidoMaterno,
                'primer_nombre' => $primerNombre,
                'otros_nombres' => $otrosNombres,
                'dni' => $dni,
                'direccion' => $direccion,
                'estado_civil' => $estadoCivil,
                'telefono' => $telefono,
                'seguro_social' => $seguroSocial,
                'fecha_ingreso' => $fechaIngreso,
                'cargo' => $cargo,
                'sueldo' => $sueldo,
            ]);
        }

        return redirect()->route('administrativo_view')->with('success', 'Administrativo actualizado correctamente.');
    }

    public function delete(Request $request){
        $id = $request->input('id');

        $requested = Administrativo::find($id);

        $requested->update(['estado' => '0']);

        return redirect()->route('administrativo_view')->with('success', 'Administrativo desactivado correctamente.');
    }

    public function export(Request $request, IExportRequestFactory $requestFactory, IExporterService $exporterService)
    {
        $sqlColumns = ['id_administrativo', 'dni', 'apellido_paterno', 'apellido_materno', 'primer_nombre', 'otros_nombres', 'cargo', 'sueldo'];

        $params = RequestHelper::extractSearchParams($request);
        $query = static::doSearch($sqlColumns, $params->search, null, $params->applied_filters);

        $data = $query->map(function ($administrativo) {
            return [
                $administrativo->id_administrativo,
                $administrativo->cargo,
                $administrativo->dni,
                $administrativo->apellido_paterno . ' ' . $administrativo->apellido_materno,
                $administrativo->primer_nombre . ' ' . $administrativo->otros_nombres,
                'S/ ' . number_format($administrativo->sueldo, 2),
            ];
        });

        $title = 'Listado de Administrativos';
        $headers = ['ID', 'Cargo', 'DNI', 'Apellidos', 'Nombres', 'Sueldo'];

        $exportRequest = $requestFactory->create(
            $title,
            $headers,
            $data->toArray(),
            ['filename' => 'administrativos_' . date('d_m_Y')]
        );

        return $exporterService->exportAsResponse($request, $exportRequest);
    }

}
