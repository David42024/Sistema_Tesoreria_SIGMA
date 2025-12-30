<?php

namespace App\Http\Controllers;

use App\Helpers\TableAction;
use App\Models\DepartamentoAcademico;
use App\Models\Personal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DocenteController extends Controller
{
    private static function doSearch($sqlColumns, $search, $pagination, $appliedFilters = []){
        
        $query = Personal::where('estado', '=', '1');
        
        if(isset($search)){
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

        return $query->paginate($pagination);
    }

    public function index(Request $request)
    {
        $sqlColumns = ["id_personal","codigo_personal","dni","apellido_paterno","apellido_materno","primer_nombre","otros_nombres","departamento"];
        $tipoDeRecurso = "personal";

        $pagination = $request->input('showing', 10);
        $paginaActual = $request->input('page', 1);
        $search = $request->input('search');

         $appliedFilters = json_decode($request->input('applied_filters', '[]'), true) ?? [];

        if (!is_numeric($paginaActual) || $paginaActual <= 0) $paginaActual = 1;
        if (!is_numeric($pagination) || $pagination <= 0) $pagination = 10;

        $query = DocenteController::doSearch($sqlColumns, $search, $pagination, $appliedFilters);

        if ($paginaActual > $query->lastPage()){
            $paginaActual = 1;
            $request['page'] = $paginaActual;
            $query = DocenteController::doSearch($sqlColumns, $search, $pagination, $appliedFilters);
        }

        $departamentosExistentes = DepartamentoAcademico::where("estado", 1)
            ->pluck("nombre")
            ->unique()
            ->values();

        $data = [
            'titulo' => 'Docentes',
            'columnas' => [
                'ID',
                'Codigo de Personal',
                'DNI',
                'Apellidos',
                'Nombres',
                'Departamento Académico'
            ],
            'filas' => [],
            'showing' => $pagination,
            'paginaActual' => $paginaActual,
            'totalPaginas' => $query->lastPage(),
            'resource' => $tipoDeRecurso,
            'view' => 'docente_view',
            'create' => 'docente_create',
            'edit' => 'docente_edit',
            'delete' => 'docente_delete',
            'filters' => $data['columnas'] ?? [],
            'filterOptions' => [
                'Departamento Académico' => $departamentosExistentes,
            ],
            'actions' => [
                new TableAction("edit", "docente_edit", $tipoDeRecurso),
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

        foreach ($query as $itempersonal){
            array_push($data['filas'],
            [
                $itempersonal->id_personal,
                $itempersonal->codigo_personal,
                $itempersonal->dni,
                $itempersonal->apellido_paterno . ' ' . $itempersonal->apellido_materno,
                $itempersonal->primer_nombre . ' ' . $itempersonal->otros_nombres,
                $itempersonal->departamentos_academicos->nombre ?? 'N/A'
            ]); 
        }


        return view('gestiones.docente.index', compact('data'));
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

    public function createNewEntry(Request $request){

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
        ],[
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
        if (!isset($id) ) {
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
        ],[
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


}
