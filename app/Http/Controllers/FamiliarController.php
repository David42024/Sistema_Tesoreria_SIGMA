<?php
namespace App\Http\Controllers;

use App\Interfaces\IExporterService;
use App\Interfaces\IExportRequestFactory;
use App\Models\Familiar;
use App\Models\User;
use App\Models\Alumno;
use App\Models\Archivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Helpers\FilteredSearchQuery;
use App\Helpers\CRUDTablePage;
use App\Helpers\ExcelExportHelper;
use App\Helpers\PDFExportHelper;
use App\Helpers\RequestHelper;
use App\Helpers\TableAction;
use App\Helpers\Tables\AdministrativoHeaderComponent;
use App\Helpers\Tables\AdministrativoSidebarComponent;
use App\Helpers\Home\Familiar\FamiliarSidebarComponent;
use App\Helpers\Tables\CautionModalComponent;
use App\Helpers\Tables\CRUDTableComponent;
use App\Helpers\Tables\FilterConfig;
use App\Helpers\Tables\PaginatorRowsSelectorComponent;
use App\Helpers\Tables\SearchBoxComponent;
use App\Helpers\Tables\TableButtonComponent;
use App\Helpers\Tables\TableComponent;
use App\Helpers\Tables\TablePaginator;
use App\Http\Controllers\Controller;

class FamiliarController extends Controller
{

    private static function doSearch($sqlColumns, $search, $maxEntriesShow, $appliedFilters = [])
    {
        $columnMap = [
            'ID' => 'idFamiliar',
            'DNI' => 'dni',
            'Apellido Paterno' => 'apellido_paterno',
            'Apellido Materno' => 'apellido_materno',
            'Primer Nombre' => 'primer_nombre',
            'Otros Nombres' => 'otros_nombres',
            'Número de Contacto' => 'numero_contacto',
            'Correo Electrónico' => 'correo_electronico',
        ];

        $query = Familiar::where('estado', '=', true);

        FilteredSearchQuery::fromQuery($query, $sqlColumns, $search, $appliedFilters, $columnMap);

        if ($maxEntriesShow == null)
            return $query->get();

        return $query->paginate($maxEntriesShow);
    }

    public function index(Request $request, $long = false)
    {
        $sqlColumns = ['idFamiliar', 'dni', 'apellido_paterno', 'apellido_materno', 'primer_nombre', 'otros_nombres', 'numero_contacto', 'correo_electronico'];
        $resource = 'alumnos';

        $params = RequestHelper::extractSearchParams($request);

        $page = CRUDTablePage::new()
            ->title("Familiares")
            ->sidebar(new AdministrativoSidebarComponent())
            ->header(new AdministrativoHeaderComponent());

        $content = CRUDTableComponent::new()
            ->title("Familiares");

        $filterButton = new TableButtonComponent("tablesv2.buttons.filtros");
        $content->addButton($filterButton);

        /* Definición de botones */

        $descargaButton = new TableButtonComponent("tablesv2.buttons.download");
        $createNewEntryButton = new TableButtonComponent("tablesv2.buttons.createNewEntry", ["redirect" => "familiar_create"]);

        if (!$long) {
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermas", ["redirect" => "familiar_viewAll"]);
        } else {
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermenos", ["redirect" => "familiar_view"]);
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
            ->action('Estás eliminando el Alumno')
            ->columns(['Nivel', 'Descripción'])
            ->rows(['nombre', 'descripcion'])
            ->lastWarningMessage('Borrar esto afectará a todo lo que esté vinculado a este Nivel Educativo')
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

        $filterConfig = new FilterConfig();
        $filterConfig->filters = [
            "ID",
            "DNI",
            "Apellido Paterno",
            "Apellido Materno",
            "Primer Nombre",
            "Otros Nombres",
            "Número de Contacto",
            "Correo Electrónico"
        ];
        $filterConfig->filterOptions = [];
        $content->filterConfig = $filterConfig;

        $table = new TableComponent();
        $table->columns = ["ID", "DNI", "Apellidos", "Nombres", "Número de Contacto", "Correo Electrónico"];
        $table->rows = [];

        foreach ($query as $familiar) {
            array_push(
                $table->rows,
                [
                    $familiar->idFamiliar,
                    $familiar->dni,
                    $familiar->apellido_paterno . " " . $familiar->apellido_materno,
                    $familiar->primer_nombre . " " . $familiar->otros_nombres,
                    $familiar->numero_contacto,
                    $familiar->correo_electronico,
                ]
            );
        }
        $table->actions = [
            new TableAction('edit', 'familiar_edit', $resource),
            new TableAction('delete', '', $resource),
            new TableAction('familiar_ver_alumnos', 'familiar_detalles', $resource),
        ];

        $paginator = new TablePaginator($params->page, $query->lastPage(), []);
        $table->paginator = $paginator;

        $content->tableComponent($table);

        $page->content($content->build());

        return $page->render();
    }

    public function viewAll(Request $request)
    {
        return static::index($request, true);
    }

    private static function fallbackDoSearch($sqlColumns, $search, $maxEntriesShow)
    {
        if (!isset($search)) {
            $query = Familiar::where('estado', '=', '1')->orderBy('apellido_paterno')
                ->orderBy('apellido_materno')
                ->orderBy('primer_nombre')
                ->orderBy('otros_nombres')->paginate($maxEntriesShow);
        } else {
            $query = Familiar::where('estado', '=', '1')
                ->whereAny($sqlColumns, 'LIKE', "%{$search}%")
                ->paginate($maxEntriesShow);
        }
        return $query;
    }


    public function fallback(Request $request)
    {
        $sqlColumns = ['idFamiliar', 'dni', 'apellido_paterno', 'apellido_materno', 'primer_nombre', 'otros_nombres', 'numero_contacto', 'correo_electronico'];
        $resource = 'alumnos';

        $maxEntriesShow = $request->input('showing', 10);
        $paginaActual = $request->input('page', 1);
        $search = $request->input('search');

        if (!is_numeric($paginaActual) || $paginaActual <= 0)
            $paginaActual = 1;
        if (!is_numeric($maxEntriesShow) || $maxEntriesShow <= 0)
            $maxEntriesShow = 10;

        $query = FamiliarController::fallbackDoSearch($sqlColumns, $search, $maxEntriesShow);

        if ($paginaActual > $query->lastPage()) {
            $paginaActual = 1;
            $request['page'] = $paginaActual;
            $query = FamiliarController::fallbackDoSearch($sqlColumns, $search, $maxEntriesShow);
        }

        $data = [
            'titulo' => 'Familiares',
            'columnas' => [
                'ID',
                'DNI',
                'Apellidos',
                'Nombres',
                'Contacto',
                'Correo',
            ],
            'filas' => [],
            'showing' => $maxEntriesShow,
            'paginaActual' => $paginaActual,
            'totalPaginas' => $query->lastPage(),
            'resource' => $resource,
            'view' => 'familiar_view',
            'create' => 'familiar_create',
            'edit' => 'familiar_edit',
            'delete' => 'familiar_delete',
            'show_route' => 'familiar_detalles',
            'show_text' => 'Ver Alumnos',
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

        foreach ($query as $familiar) {
            $familiar = Familiar::findOrFail($familiar->idFamiliar);


            array_push(
                $data['filas'],
                [
                    $familiar->idFamiliar,
                    $familiar->dni,
                    $familiar->apellido_paterno . ' ' . $familiar->apellido_materno,
                    $familiar->primer_nombre . ' ' . $familiar->otros_nombres,
                    $familiar->numero_contacto,
                    $familiar->correo_electronico,
                ]
            );
        }

        return view('gestiones.familiar.index', compact('data'));

    }

    public function create(Request $request)
    {
        $alumnos = Alumno::where('estado', '=', '1')->get();
        $tiposUsuario = [
            'Familiar' => 'Familiar',
            'Administrativo' => 'Administrativo',
            'Personal' => 'Personal',
            'PreApoderado' => 'Pre-Apoderado',
        ];
        $data = [
            'return' => route('familiar_view', ['abort' => true]),
            'alumnos' => $alumnos,
            'tiposUsuario' => $tiposUsuario,
        ];
        return view('gestiones.familiar.create', compact('data'));
    }

    public function createNewEntry(Request $request, $returnModel = false)
    {
        $request->validate([
            // Validaciones para usuario
            'nombre_usuario' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
            'tipo_usuario' => 'required|string|in:Familiar,Administrativo,Personal,PreApoderado',
            'fotografia' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // Validaciones para familiar
            'dni' => 'required|string|max:20',
            'apellido_paterno' => 'required|string|max:50',
            'apellido_materno' => 'required|string|max:50',
            'primer_nombre' => 'required|string|max:50',
            'otros_nombres' => 'nullable|string|max:100',
            'numero_contacto' => 'nullable|string|max:20',
            'correo_electronico' => 'nullable|email|max:100',
        ], [
            'nombre_usuario.required' => 'Ingrese un nombre de usuario.',
            'nombre_usuario.unique' => 'El nombre de usuario ya existe.',
            'password.required' => 'Ingrese una contraseña.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'tipo_usuario.required' => 'Seleccione un tipo de usuario.',
            'dni.required' => 'Ingrese un DNI válido.',
            'apellido_paterno.required' => 'Ingrese el apellido paterno.',
            'apellido_materno.required' => 'Ingrese el apellido materno.',
            'primer_nombre.required' => 'Ingrese el primer nombre.',
        ]);

        \DB::beginTransaction();
        try {
            // Manejar foto si se sube
            $idArchivo = null;
            if ($request->hasFile('fotografia')) {
                $foto = $request->file('fotografia');
                $nombreFoto = 'familiar_' . time() . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();
                $fotoPath = $foto->storeAs('usuarios', $nombreFoto, 'public');

                // Crear registro en tabla archivos
                $archivo = new \App\Models\Archivo(['foto' => $fotoPath]);
                $archivo->save();
                $idArchivo = $archivo->idArchivo;
            }

            // Crear usuario
            $userData = [
                'username' => $request->nombre_usuario,
                'password' => bcrypt($request->password),
                'tipo' => $request->tipo_usuario,
                'estado' => '1',
                'idArchivo' => $idArchivo,
            ];

            $usuario = User::create($userData);

            // Crear familiar con el usuario recién creado
            $familiar = Familiar::create([
                'id_usuario' => $usuario->id_usuario,
                'dni' => $request->dni,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'primer_nombre' => $request->primer_nombre,
                'otros_nombres' => $request->otros_nombres,
                'numero_contacto' => $request->numero_contacto,
                'correo_electronico' => $request->correo_electronico,
            ]);

            // Sincroniza alumnos y parentesco si se envían
            if ($request->has('alumnos')) {
                $syncData = [];
                foreach ($request->input('alumnos') as $id_alumno => $parentesco) {
                    $syncData[$id_alumno] = ['parentesco' => $parentesco];
                }
                $familiar->alumnos()->sync($syncData);
            }

            \DB::commit();

            if ($returnModel) {
                return $familiar;
            }

            return redirect()->route('familiar_view')->with('success', 'Familiar creado correctamente.');

        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error al crear el familiar: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(Request $request, $id)
    {
        if (!isset($id)) {
            return redirect(route('familiar_view'));
        }

        $familiar = Familiar::findOrFail($id);
        $usuarios = User::all();

        $data = [
            'return' => route('familiar_view', ['abort' => true]),
            'id' => $id,
            'familiar' => $familiar,
            'usuarios' => $usuarios,
        ];

        return view('gestiones.familiar.edit', compact('data'));
    }

    public function editEntry(Request $request, $id)
    {
        if (!isset($id)) {
            return redirect(route('familiar_view'));
        }



        $familiar = Familiar::findOrFail($id);
        $familiar->update($request->only([
            'apellido_paterno',
            'apellido_materno',
            'primer_nombre',
            'otros_nombres',
            'numero_contacto',
            'correo_electronico',
        ]));

        if ($request->has('alumnos')) {
            $syncData = [];
            foreach ($request->input('alumnos') as $id_alumno => $parentesco) {
                $syncData[$id_alumno] = ['parentesco' => $parentesco];
            }
            $familiar->alumnos()->sync($syncData);
        }

        return redirect()->route('familiar_view')->with('success', 'Familiar actualizado correctamente.');
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');

        $requested = Familiar::find($id);

        $requested->update(['estado' => '0']);

        return redirect()->route('familiar_view')->with('success', 'Familiar desactivado correctamente.');
    }


    public function viewDetalles($id)
    {
        if (!isset($id)) {
            return redirect(route('familiar_view'));
        }

        $familiar = Familiar::with(['alumnos'])->findOrFail($id);

        // Prepara los datos para la tabla de alumnos asociados
        $titulo = "Alumnos asociados";
        $columnas = ['ID', 'Nombres', 'Apellidos', 'DNI', 'Parentesco'];
        $filas = [];
        foreach ($familiar->alumnos as $alumno) {
            $filas[] = [
                $alumno->id_alumno,
                $alumno->primer_nombre . ' ' . $alumno->otros_nombres,
                $alumno->apellido_paterno . ' ' . $alumno->apellido_materno,
                $alumno->dni,
                $alumno->pivot->parentesco, // <-- parentesco desde la tabla pivote
            ];
        }
        $resource = 'alumnos';
        $create = null;
        $showing = 10;
        $paginaActual = 1;
        $totalPaginas = 1;

        return view('gestiones.familiar.detalles', compact(
            'familiar',
            'titulo',
            'columnas',
            'filas',
            'resource',
            'create',
            'showing',
            'paginaActual',
            'totalPaginas'
        ));
    }

    public function export(Request $request, IExportRequestFactory $requestFactory, IExporterService $exporterService)
    {
        $sqlColumns = ['idFamiliar', 'dni', 'apellido_paterno', 'apellido_materno', 'primer_nombre', 'otros_nombres', 'numero_contacto', 'correo_electronico'];

        $params = RequestHelper::extractSearchParams($request);

        $query = static::doSearch($sqlColumns, $params->search, null, $params->applied_filters);
        $query = $query->sortBy('apellido_paterno');

        $data = $query->map(function ($familiar) {
            $alumnos_dni = $familiar->alumnos->pluck('dni')->toArray();
            return [
                $familiar->apellido_paterno . " " . $familiar->apellido_materno,
                $familiar->primer_nombre . " " . $familiar->otros_nombres,
                $familiar->dni,
                $familiar->numero_contacto,
                $familiar->correo_electronico,
                join(', ', $alumnos_dni),
            ];
        });

        $title = 'Listado de Familiares';
        $headers = ["Apellidos", "Nombres", "DNI", "Número de Contacto", "Correo Electrónico", "Alumnos"];
        $exportRequest = $requestFactory->create(
            $title,
            $headers,
            $data->toArray(),
            ['filename' => 'familiares_' . date('d_m_Y')]
        );

        return $exporterService->exportAsResponse($request, $exportRequest);
    }

    /**
     * Mostrar el formulario para cambiar contraseña del familiar autenticado
     */
    public function showChangePassword(Request $request)
    {
        $user = Auth::user();

        // Verificar que el usuario sea de tipo Familiar
        if ($user->tipo !== 'Familiar') {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }

        // Obtener el familiar asociado al usuario logueado
        $familiar = Familiar::where('id_usuario', $user->id_usuario)->first();

        if (!$familiar) {
            abort(404, 'No se encontró información del familiar.');
        }

        // Crear página con el sidebar y header correctos de Familiar
        $page = CRUDTablePage::new()
            ->title('Cambiar Contraseña')
            ->sidebar(new FamiliarSidebarComponent());

        $data = [
            'return' => route('familiar_cambiar_password_view'),
            'username' => $user->username
        ];

        $page->content(view('gestiones.familiar.change_password', compact('data'))->render());

        return $page->render();
    }

    /**
     * Actualizar la contraseña del familiar autenticado
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        // Verificar que el usuario sea de tipo Familiar
        if ($user->tipo !== 'Familiar') {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        // Obtener el familiar asociado al usuario logueado
        $familiar = Familiar::where('id_usuario', $user->id_usuario)->first();

        if (!$familiar) {
            abort(404, 'No se encontró información del familiar.');
        }

        // Validar los campos de contraseña
        $request->validate([
            'password' => 'required|string|min:6|confirmed'
        ], [
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.'
        ]);

        // Buscar el usuario en la tabla users usando el id_usuario del familiar
        $userToUpdate = User::where('id_usuario', $familiar->id_usuario)->first();

        if (!$userToUpdate) {
            abort(404, 'No se encontró el usuario asociado.');
        }

        // Actualizar la contraseña
        $userToUpdate->password = Hash::make($request->input('password'));
        $userToUpdate->save();

        return redirect()->route('familiar_cambiar_password_view')
            ->with('success', 'Contraseña actualizada correctamente. Por favor, inicia sesión nuevamente con tu nueva contraseña.');
    }
}
