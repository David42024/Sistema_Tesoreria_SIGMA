<?php

namespace App\Http\Controllers;

use App\Helpers\CRUDTablePage;
use App\Helpers\FilteredSearchQuery;
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
use App\Models\Alumno;
use App\Models\ComposicionFamiliar;
use App\Models\Familiar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ComposicionFamiliarController extends Controller
{
    private static function doSearch($sqlColumns, $search, $maxEntriesShow, $appliedFilters = [])
    {
        $query = ComposicionFamiliar::with(['alumno', 'familiar'])
            ->where('estado', 1); // Solo mostrar registros activos

        if (!empty($search)) {
            $query->whereHas('alumno', function ($q) use ($search) {
                $q->where('primer_nombre', 'LIKE', "%{$search}%")
                    ->orWhere('apellido_paterno', 'LIKE', "%{$search}%")
                    ->orWhere('apellido_materno', 'LIKE', "%{$search}%");
            })->orWhereHas('familiar', function ($q) use ($search) {
                $q->where('primer_nombre', 'LIKE', "%{$search}%")
                    ->orWhere('apellido_paterno', 'LIKE', "%{$search}%")
                    ->orWhere('apellido_materno', 'LIKE', "%{$search}%");
            })->orWhere('parentesco', 'LIKE', "%{$search}%");
        }

        if ($maxEntriesShow === null) {
            return $query->get();
        } else {
            return $query->paginate($maxEntriesShow);
        }
    }

    public function index(Request $request, $long = false)
    {
        $sqlColumns = ['id_alumno', 'id_familiar', 'parentesco'];
        $resource = 'composicion_familiar'; // ✅ CORREGIDO
        $params = RequestHelper::extractSearchParams($request);

        $page = CRUDTablePage::new()
            ->title("Asignación Familiar-Alumno")
            ->sidebar(new AdministrativoSidebarComponent())
            ->header(new AdministrativoHeaderComponent());

        $content = CRUDTableComponent::new()
            ->title("Composiciones Familiares");

        /* Definición de botones */
        $createNewEntryButton = new TableButtonComponent("tablesv2.buttons.createNewEntry", ["redirect" => "composicion_familiar_create"]);

        if (!$long) {
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermas", ["redirect" => "composicion_familiar_viewAll"]);
        } else {
            $vermasButton = new TableButtonComponent("tablesv2.buttons.vermenos", ["redirect" => "composicion_familiar_view"]);
            $params->showing = 100;
        }

        $content->addButton($vermasButton);
        $content->addButton($createNewEntryButton);

        /* Paginador */
        $paginatorRowsSelector = new PaginatorRowsSelectorComponent();
        if ($long)
            $paginatorRowsSelector = new PaginatorRowsSelectorComponent([100]);

        $paginatorRowsSelector->valueSelected = $params->showing;
        $content->paginatorRowsSelector($paginatorRowsSelector);

        /* Searchbox */
        $searchBox = new SearchBoxComponent();
        $searchBox->placeholder = "Buscar por nombre de alumno, familiar o parentesco...";
        $searchBox->value = $params->search;
        $content->searchBox($searchBox);

        /* ✅ Modal configurado para mostrar solo 3 columnas */
        $cautionModal = CautionModalComponent::new()
            ->cautionMessage('¿Estás seguro?')
            ->action('Estás eliminando permanentemente esta asignación')
            ->columns(['ID Familiar', 'ID Alumno', 'Parentesco']) // Solo 3 columnas
            ->rows([0, 2, 4]) // Posición 0=ID Familiar, 2=ID Alumno, 4=Parentesco
            ->lastWarningMessage('Esta acción no se puede deshacer. La asignación será eliminada permanentemente de la base de datos.')
            ->confirmButton('Sí, eliminar')
            ->cancelButton('Cancelar')
            ->isForm(true)
            ->build();

        $page->modals([$cautionModal]);

        /* Lógica del controller */
        $query = static::doSearch($sqlColumns, $params->search, $params->showing, $params->applied_filters);

        if ($params->page > $query->lastPage()) {
            $params->page = 1;
            $query = static::doSearch($sqlColumns, $params->search, $params->showing, $params->applied_filters);
        }

        $table = new TableComponent();
        $table->columns = ["ID Familiar", "Nombre Familiar", "ID Alumno", "Nombre Alumno", "Parentesco"];

        $table->rows = [];
        foreach ($query as $composicion) {
            $nombreFamiliar = trim($composicion->familiar->apellido_paterno . ' ' .
                                   $composicion->familiar->apellido_materno . ' ' .
                                   $composicion->familiar->primer_nombre . ' ' .
                                   $composicion->familiar->otros_nombres);
            $nombreAlumno = trim($composicion->alumno->apellido_paterno . ' ' .
                                 $composicion->alumno->apellido_materno . ' ' .
                                 $composicion->alumno->primer_nombre . ' ' .
                                 $composicion->alumno->otros_nombres);

            /* ✅ ESTRUCTURA DEL ARRAY CORREGIDA
             * Posición 0: ID Familiar (se muestra en modal, columna 0)
             * Posición 1: Nombre Familiar (se muestra en tabla)
             * Posición 2: ID Alumno (se muestra en modal, columna 1)
             * Posición 3: Nombre Alumno (se muestra en tabla)
             * Posición 4: Parentesco (se muestra en modal, columna 2)
             * Posición 5: id_alumno (hidden input para delete)
             * Posición 6: id_familiar (hidden input para delete)
             */
            array_push(
                $table->rows,
                [
                    $composicion->id_familiar,      // 0: ID Familiar (visible en modal)
                    $nombreFamiliar,                // 1: Nombre Familiar (visible en tabla)
                    $composicion->id_alumno,        // 2: ID Alumno (visible en modal)
                    $nombreAlumno,                  // 3: Nombre Alumno (visible en tabla)
                    $composicion->parentesco,       // 4: Parentesco (visible en modal)
                    $composicion->id_alumno,        // 5: id_alumno (hidden para delete)
                    $composicion->id_familiar       // 6: id_familiar (hidden para delete)
                ]
            );
        }

        // ✅ Acción de delete con el resource correcto
        $table->actions = [
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

    public function create()
    {
        // Obtener todos los alumnos activos
        $alumnos = Alumno::where('estado', true)
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('primer_nombre')
            ->get();

        $alumnosData = [];
        foreach ($alumnos as $alumno) {
            $alumnosData[] = [
                'id' => $alumno->id_alumno,
                'nombre' => trim($alumno->apellido_paterno . ' ' .
                                $alumno->apellido_materno . ' ' .
                                $alumno->primer_nombre . ' ' .
                                $alumno->otros_nombres)
            ];
        }

        // Obtener todos los familiares activos
        $familiares = Familiar::where('estado', true)
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('primer_nombre')
            ->get();

        $familiaresData = [];
        foreach ($familiares as $familiar) {
            $familiaresData[] = [
                'id' => $familiar->idFamiliar,
                'nombre' => trim($familiar->apellido_paterno . ' ' .
                                 $familiar->apellido_materno . ' ' .
                                 $familiar->primer_nombre . ' ' .
                                 $familiar->otros_nombres)
            ];
        }

        $parentescos = [
            ['id' => 'Padre', 'descripcion' => 'Padre'],
            ['id' => 'Madre', 'descripcion' => 'Madre'],
            ['id' => 'Tutor', 'descripcion' => 'Tutor'],
            ['id' => 'Abuelo', 'descripcion' => 'Abuelo'],
            ['id' => 'Abuela', 'descripcion' => 'Abuela'],
            ['id' => 'Tío', 'descripcion' => 'Tío'],
            ['id' => 'Tía', 'descripcion' => 'Tía'],
        ];

        $data = [
            'return' => route('composicion_familiar_view', ['abort' => true]),
            'alumnos' => $alumnosData,
            'familiares' => $familiaresData,
            'parentescos' => $parentescos,
        ];

        return view('gestiones.composicion_familiar.create', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_alumno' => 'required|exists:alumnos,id_alumno',
            'id_familiar' => 'required|exists:familiares,idFamiliar',
            'parentesco' => 'required|string|in:Padre,Madre,Tutor,Abuelo,Abuela,Tío,Tía',
            'estado' => 'nullable|integer'
        ], [
            'id_alumno.required' => 'Debe seleccionar un alumno.',
            'id_alumno.exists' => 'El alumno seleccionado no existe.',
            'id_familiar.required' => 'Debe seleccionar un familiar.',
            'id_familiar.exists' => 'El familiar seleccionado no existe.',
            'parentesco.required' => 'Debe seleccionar el parentesco.',
            'parentesco.in' => 'El parentesco seleccionado no es válido.',
        ]);

        // Verificar si la composición ya existe
        $existingComposition = ComposicionFamiliar::where('id_alumno', $request->id_alumno)
            ->where('id_familiar', $request->id_familiar)
            ->first();

        if ($existingComposition) {
            return redirect()->back()
                ->withErrors(['id_familiar' => 'Esta asignación ya existe.'])
                ->withInput();
        }

        // Crear la asignación
        ComposicionFamiliar::create([
            'id_alumno' => $request->id_alumno,
            'id_familiar' => $request->id_familiar,
            'parentesco' => $request->parentesco,
            'estado' => $request->estado ?? 1, // Por defecto estado = 1
        ]);

        return redirect()->route('composicion_familiar_view')
            ->with('success', 'Asignación creada exitosamente.');
    }

    public function delete(Request $request)
    {
        try {
            // 🔍 DEBUG: Registrar todos los datos recibidos
            Log::info('===== DELETE COMPOSICION FAMILIAR - DEBUG COMPLETO =====');
            Log::info('Método HTTP: ' . $request->method());
            Log::info('Todos los datos del request:', $request->all());
            Log::info('id_alumno recibido: ' . ($request->input('id_alumno') ?? 'NULL'));
            Log::info('id_familiar recibido: ' . ($request->input('id_familiar') ?? 'NULL'));
            Log::info('Tipo de id_alumno: ' . gettype($request->input('id_alumno')));
            Log::info('Tipo de id_familiar: ' . gettype($request->input('id_familiar')));
            Log::info('========================================================');

            // Obtener IDs del formulario
            $idAlumno = $request->input('id_alumno');
            $idFamiliar = $request->input('id_familiar');

            // Validar que los IDs existan
            if (!$idAlumno || !$idFamiliar) {
                Log::warning('Delete falló: IDs no proporcionados.');
                Log::warning('id_alumno: ' . var_export($idAlumno, true));
                Log::warning('id_familiar: ' . var_export($idFamiliar, true));

                return redirect()->route('composicion_familiar_view')
                    ->with('error', 'IDs no proporcionados en la solicitud. Por favor, intenta de nuevo.');
            }

            // Convertir y validar que sean enteros positivos
            $idAlumno = (int) $idAlumno;
            $idFamiliar = (int) $idFamiliar;

            if ($idAlumno <= 0 || $idFamiliar <= 0) {
                Log::warning('Delete falló: IDs inválidos después de conversión.');
                Log::warning('id_alumno convertido: ' . $idAlumno);
                Log::warning('id_familiar convertido: ' . $idFamiliar);

                return redirect()->route('composicion_familiar_view')
                    ->with('error', 'Los IDs proporcionados no son válidos.');
            }

            Log::info('IDs validados correctamente:');
            Log::info('id_alumno: ' . $idAlumno);
            Log::info('id_familiar: ' . $idFamiliar);

            // Buscar la composición por la clave primaria compuesta
            $composicion = ComposicionFamiliar::where('id_alumno', $idAlumno)
                ->where('id_familiar', $idFamiliar)
                ->first();

            if (!$composicion) {
                Log::warning('Delete falló: Registro no encontrado en la base de datos.');
                Log::warning('Búsqueda con id_alumno=' . $idAlumno . ', id_familiar=' . $idFamiliar);

                return redirect()->route('composicion_familiar_view')
                    ->with('error', 'La asignación no fue encontrada. Puede que ya haya sido eliminada.');
            }

            Log::info('Registro encontrado:');
            Log::info('ID Alumno: ' . $composicion->id_alumno);
            Log::info('ID Familiar: ' . $composicion->id_familiar);
            Log::info('Parentesco: ' . $composicion->parentesco);
            Log::info('Estado actual: ' . ($composicion->estado ? 'Activo (1)' : 'Inactivo (0)'));

            // Verificar si ya está desactivada
            if (!$composicion->estado) {
                Log::info('Delete omitido: La asignación ya estaba desactivada.');

                return redirect()->route('composicion_familiar_view')
                    ->with('warning', 'La asignación ya estaba desactivada anteriormente.');
            }

            // Realizar soft delete (cambiar estado a 0)
            $composicion->estado = 0;
            $saved = $composicion->save();

            if ($saved) {
                Log::info('✅ DELETE EXITOSO: Asignación desactivada correctamente.');
                Log::info('id_alumno=' . $idAlumno . ', id_familiar=' . $idFamiliar);

                return redirect()->route('composicion_familiar_view')
                    ->with('success', 'Asignación eliminada correctamente.');
            } else {
                Log::error('Delete falló en save(): No se pudo guardar el cambio de estado.');

                return redirect()->route('composicion_familiar_view')
                    ->with('error', 'Error al guardar los cambios. Por favor, intenta de nuevo.');
            }

        } catch (\Exception $e) {
            Log::error('❌ DELETE ERROR - Excepción capturada:');
            Log::error('Mensaje: ' . $e->getMessage());
            Log::error('Archivo: ' . $e->getFile());
            Log::error('Línea: ' . $e->getLine());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->route('composicion_familiar_view')
                ->with('error', 'Error al procesar la eliminación: ' . $e->getMessage());
        }
    }
}
