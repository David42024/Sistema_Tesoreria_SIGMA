<?php

namespace App\Http\Controllers;

use App\Helpers\FilteredSearchQuery;
use App\Helpers\CRUDTablePage;
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
use App\Models\User;
use App\Models\Administrativo;
use App\Models\Personal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private static function doSearch($sqlColumns, $search, $maxEntriesShow, $appliedFilters = [])
    {
        $columnMap = [
            'ID' => 'id_usuario',
            'Usuario' => 'username',
            'Tipo' => 'tipo',
            'Estado' => 'estado',
            'Último Login' => 'last_login'
        ];

        $query = User::where('estado', '=', true);

        FilteredSearchQuery::fromQuery($query, $sqlColumns, $search, $appliedFilters, $columnMap);

        if ($maxEntriesShow == null)
            return $query->get();

        if ($maxEntriesShow === null) {
            return $query->get();
        } else {
            return $query->paginate($maxEntriesShow);
        }
    }

    public function index(Request $request, $long = false)
    {
        $sqlColumns = ['id_usuario', 'username', 'tipo', 'estado', 'last_login'];
        $resource = 'usuarios';

        $params = RequestHelper::extractSearchParams($request);

        $page = CRUDTablePage::new()
            ->title("Usuarios")
            ->sidebar(new AdministrativoSidebarComponent())
            ->header(new AdministrativoHeaderComponent());

        $content = CRUDTableComponent::new()
            ->title("Usuarios");

        $filterButton = new TableButtonComponent("tablesv2.buttons.filtros");
        $content->addButton($filterButton);

        /* Definición de botones */
        $descargaButton = new TableButtonComponent("tablesv2.buttons.download");
        $createNewEntryButton = new TableButtonComponent("tablesv2.buttons.createNewEntry", ["redirect" => "usuario_create"]);

        if (!$long) {
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermas", ["redirect" => "usuario_viewAll"]);
        } else {
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermenos", ["redirect" => "usuario_view"]);
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
            ->action('Estás eliminando el Usuario')
            ->columns(['ID', 'Usuario', 'Tipo', 'Estado'])
            ->rows(['', '', '', ''])
            ->lastWarningMessage('Borrar esto afectará a todo lo que esté vinculado a este Usuario.')
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
            "Usuario",
            "Tipo",
            "Estado"
        ];
        $filterConfig->filterOptions = [
            "Tipo" => ["Administrativo", "Personal", "PreApoderado"],
            "Estado" => ["Activo", "Inactivo"]
        ];
        $content->filterConfig = $filterConfig;

        $table = new TableComponent();
        $table->columns = ["ID", "Usuario", "Tipo", "Último Login", "Estado"];
        $table->rows = [];

        foreach ($query as $usuario) {
            array_push(
                $table->rows,
                [
                    $usuario->id_usuario,
                    $usuario->username,
                    $usuario->tipo,
                    $usuario->last_login ? date('d/m/Y H:i', strtotime($usuario->last_login)) : 'Nunca',
                    $usuario->estado ? 'Activo' : 'Inactivo',
                ]
            );
        }

        $table->actions = [
            new TableAction('edit', 'usuario_edit', $resource),
            new TableAction('change_password', 'usuario_change_password', $resource),
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

    public function viewAll(Request $request)
    {
        return static::index($request, true);
    }

    public function create(Request $request)
    {
        $tipos = [
            ['id' => 'Administrativo', 'descripcion' => 'Administrativo'],
            ['id' => 'Familiar', 'descripcion' => 'Familiar'],
            ['id' => 'PreApoderado', 'descripcion' => 'PreApoderado'],
            ['id' => 'Personal', 'descripcion' => 'Personal']
        ];

        $data = [
            'return' => route('usuario_view', ['abort' => true]),
            'tipos' => $tipos
        ];

        return view('gestiones.usuario.create', compact('data'));
    }

    public function createNewEntry(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
            'tipo' => 'required|in:Administrativo,Familiar,PreApoderado,Personal',
            'foto' => 'nullable|image|mimes:jpeg,jpg,png|max:2048'
        ], [
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.unique' => 'El nombre de usuario ya existe.',
            'username.max' => 'El nombre de usuario no puede superar los 50 caracteres.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'tipo.required' => 'El tipo de usuario es obligatorio.',
            'tipo.in' => 'El tipo de usuario no es válido.',
            'foto.image' => 'El archivo debe ser una imagen.',
            'foto.mimes' => 'La foto debe ser un archivo de tipo: jpeg, jpg, png.',
            'foto.max' => 'La foto no debe ser mayor a 2MB.'
        ]);

        $idArchivo = null;
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $nombreFoto = 'usuario_' . time() . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();
            $fotoPath = $foto->storeAs('usuarios', $nombreFoto, 'public');

            // Crear registro en tabla archivos
            $archivo = new \App\Models\Archivo(['foto' => $fotoPath]);
            $archivo->save();
            $idArchivo = $archivo->idArchivo;
        }

        $usuario = new User([
            'username' => $request->input('username'),
            'password' => Hash::make($request->input('password')),
            'tipo' => $request->input('tipo'),
            'estado' => true,
            'idArchivo' => $idArchivo
        ]);

        $usuario->save();

        $this->vincularUsuario($request, $usuario);

        return redirect()->route('usuario_view')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $tipos = [
            ['id' => 'Administrativo', 'descripcion' => 'Administrativo'],
            ['id' => 'Familiar', 'descripcion' => 'Familiar'],
            ['id' => 'PreApoderado', 'descripcion' => 'PreApoderado'],
            ['id' => 'Personal', 'descripcion' => 'Personal']
        ];

        $data = [
            'return' => route('usuario_view', ['abort' => true]),
            'id' => $id,
            'tipos' => $tipos,
            'default' => [
                'username' => $usuario->username,
                'tipo' => $usuario->tipo,
                'estado' => $usuario->estado,
                'foto' => $usuario->archivo ? $usuario->archivo->foto : null
            ]
        ];

        return view('gestiones.usuario.edit', compact('data'));
    }

    public function editEntry(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'username')->ignore($usuario->id_usuario, 'id_usuario')
            ],
            'tipo' => 'required|in:Administrativo,Familiar,PreApoderado,Personal',
            'estado' => 'required|boolean',
            'foto' => 'nullable|image|mimes:jpeg,jpg,png|max:2048'
        ], [
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.unique' => 'El nombre de usuario ya existe.',
            'username.max' => 'El nombre de usuario no puede superar los 50 caracteres.',
            'tipo.required' => 'El tipo de usuario es obligatorio.',
            'tipo.in' => 'El tipo de usuario no es válido.',
            'estado.required' => 'El estado es obligatorio.',
            'foto.image' => 'El archivo debe ser una imagen.',
            'foto.mimes' => 'La foto debe ser un archivo de tipo: jpeg, jpg, png.',
            'foto.max' => 'La foto no debe ser mayor a 2MB.'
        ]);

        // Manejar foto
        if ($request->input('eliminar_foto') == '1') {
            // Eliminar foto existente
            if ($usuario->archivo) {
                if (\Storage::disk('public')->exists($usuario->archivo->foto)) {
                    \Storage::disk('public')->delete($usuario->archivo->foto);
                }
                $usuario->archivo->delete();
                $usuario->idArchivo = null;
            }
        } elseif ($request->hasFile('foto')) {
            // Eliminar foto anterior si existe
            if ($usuario->archivo) {
                if (\Storage::disk('public')->exists($usuario->archivo->foto)) {
                    \Storage::disk('public')->delete($usuario->archivo->foto);
                }
                $usuario->archivo->delete();
            }

            // Subir nueva foto
            $foto = $request->file('foto');
            $nombreFoto = 'usuario_' . time() . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();
            $fotoPath = $foto->storeAs('usuarios', $nombreFoto, 'public');

            // Crear nuevo registro en archivos
            $archivo = new \App\Models\Archivo(['foto' => $fotoPath]);
            $archivo->save();
            $usuario->idArchivo = $archivo->idArchivo;
        }

        // Actualizar datos del usuario
        $usuario->username = $request->input('username');
        $usuario->tipo = $request->input('tipo');
        $usuario->estado = $request->input('estado');
        $usuario->save();

        $this->vincularUsuario($request, $usuario);

        return redirect()->route('usuario_view')->with('success', 'Usuario actualizado correctamente.');
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        $usuario = User::findOrFail($id);

        // Limpiar vinculaciones antes de eliminar
        if ($usuario->tipo === 'Administrativo' && $usuario->administrativo) {
            $admin = $usuario->administrativo;
            $admin->estado = false;
            $admin->save();
        } elseif ($usuario->tipo === 'Personal' && $usuario->personal) {
            $personal = $usuario->personal;
            $personal->estado = false;
            $personal->save();
        }

        $usuario->estado = false;
        $usuario->save();

        return redirect()->route('usuario_view')->with('success', 'Usuario desactivado correctamente.');
    }

    public function showChangePassword(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $data = [
            'return' => route('usuario_view'),
            'id' => $id,
            'username' => $usuario->username
        ];


        return view('gestiones.usuario.change_password', compact('data'));
    }


    // public function updatePassword(Request $request, $id)
    // {
    //     $usuario = User::findOrFail($id);

    //     $data = [
    //         'return' => route('usuario_view'),
    //         'id' => $id,
    //         'username' => $usuario->username
    //     ];


    //     return view('gestiones.usuario.change_password', compact('data'));
    // }


    public function export(Request $request)
    {
        $search = $request->input('search');
        $appliedFilters = $request->input('applied_filters', []);
        $sqlColumns = ['id_usuario', 'username', 'tipo', 'estado', 'last_login'];

        $usuarios = static::doSearch($sqlColumns, $search, null, $appliedFilters);

        $data = [];
        foreach ($usuarios as $usuario) {
            $data[] = [
                'ID' => $usuario->id_usuario,
                'Usuario' => $usuario->username,
                'Tipo' => $usuario->tipo,
                'Último Login' => $usuario->last_login ? date('d/m/Y H:i', strtotime($usuario->last_login)) : 'Nunca',
                'Estado' => $usuario->estado ? 'Activo' : 'Inactivo'
            ];
        }

        $format = $request->input('format', 'xlsx');

        if ($format === 'pdf') {
            return response()->streamDownload(function() use ($data) {
                $pdf = \App\Helpers\PDFExportHelper::generate(
                    'Usuarios',
                    ['ID', 'Usuario', 'Tipo', 'Último Login', 'Estado'],
                    $data
                );
                echo $pdf->output();
            }, 'usuarios.pdf');
        } else {
            return \App\Helpers\ExcelExportHelper::exportExcel(
                'usuarios.' . $format,
                ['ID', 'Usuario', 'Tipo', 'Último Login', 'Estado'],
                $data,
                function ($sheet, $rowIndex, $item) {
                    $column = 'A';
                    foreach ($item as $value) {
                        $sheet->setCellValue($column . $rowIndex, $value);
                        $column++;
                    }
                },
                'Usuarios'
            );
        }
    }

    private function vincularUsuario(Request $request, User $usuario): void
    {
        $idVinculado = $request->input('id_vinculado');
        if (!$idVinculado) {
            return;
        }

        $tipo = $request->input('tipo');

        if ($tipo === 'Administrativo') {
            $administrativo = Administrativo::find($idVinculado);
            if ($administrativo) {
                $administrativo->id_usuario = $usuario->id_usuario;
                $administrativo->save();
            }
            return;
        }

        if ($tipo === 'Personal') {
            $personal = Personal::where('codigo_personal', $idVinculado)->first();
            if ($personal) {
                $personal->id_usuario = $usuario->id_usuario;
                $personal->save();
            }
        }
    }



    //Eso estaba
    // public function changePassword(Request $request, $id)
    // {
    //     $usuario = User::findOrFail($id);

    //     $data = [
    //         'return' => route('usuario_view', ['abort' => true]),
    //         'id' => $id,
    //         'username' => $usuario->username
    //     ];

    //     return view('gestiones.usuario.change_password', compact('data'));
    // }

    // public function updatePassword(Request $request, $id)
    // {
    //     $usuario = User::findOrFail($id);

    //     $request->validate([
    //         'password' => 'required|string|min:6|confirmed'
    //     ], [
    //         'password.required' => 'La nueva contraseña es obligatoria.',
    //         'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
    //         'password.confirmed' => 'Las contraseñas no coinciden.'
    //     ]);

    //     // Actualizar la contraseña
    //     $usuario->password = Hash::make($request->input('password'));
    //     $usuario->save();

    //     return redirect()->route('usuario_view')->with('success', 'Contraseña actualizada correctamente');
    // }

    public function changePassword(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            'password' => 'required|string|min:6|confirmed'
        ], [
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.'
        ]);

        // Actualizar la contraseña
        $usuario->password = Hash::make($request->input('password'));
        $usuario->save();

        return redirect()->route('usuario_view')->with('success', 'Contraseña actualizada correctamente');
    }
}
