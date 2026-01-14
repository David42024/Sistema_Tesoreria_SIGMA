<?php

namespace App\Http\Controllers\Home;

use App\Helpers\Home\Familiar\FamiliarHeaderComponent;
use App\Helpers\Home\Familiar\FamiliarSidebarComponent;
use App\Helpers\Home\Familiar\AlumnosCardsComponent;
use App\Http\Controllers\Controller;
use App\Helpers\CRUDTablePage;
use App\Helpers\ExcelExportHelper;
use App\Helpers\FilteredSearchQuery;
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
use App\Models\Alumno;
use App\Models\Familiar;
use App\Models\NivelEducativo;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller {
    public static function index(Request $request){
        // Si se solicita limpiar sesión, hacerlo
        if ($request->has('limpiar_sesion')) {
            $request->session()->forget('alumno');
        }
        
        return static::familiarIndex($request);
    }

    public static function familiarIndex(Request $request){
        // Si no hay alumno seleccionado, mostrar solo el selector
        if ($request->session()->get('alumno') == null) {
            return static::familiarSinAlumnoSeleccionado($request);
        }

        // Si hay alumno seleccionado, mostrar vista en blanco (solo sidebar con info del alumno)
        $header = Utils::crearHeaderConAlumnos($request);

        $page = CRUDTablePage::new()
            ->title("Inicio")
            ->header($header)
            ->sidebar(new FamiliarSidebarComponent());

        // No se agrega contenido - la página quedará en blanco mostrando solo el sidebar
        return $page->render();
    }

    public static function familiarSinAlumnoSeleccionado(Request $request){
        $usuario = Auth::user();
        $familiar = Familiar::whereEstado(true)->whereIdUsuario($usuario->getKey())->first();
        $header = new FamiliarHeaderComponent();
        if (!empty($familiar->alumnos)){
            $header->alumnos = $familiar->alumnos->toArray();
        }else{
            $header->alumnos = [];
        }

        $alumnoSesion = $request->session()->get('alumno');
        $header->alumnoSeleccionado = $alumnoSesion;

        $page = CRUDTablePage::new()
            ->title("Selección de Alumno")
            ->header($header);

        // Agregar las tarjetas siempre (mostrará mensaje si no hay alumnos)
        $cardsComponent = new AlumnosCardsComponent($header->alumnos);
        $page->content($cardsComponent);

        return $page->render();
    }

    public static function definirSesion(Request $request){
        $alumno = Alumno::findOrFail($request->input('idalumno'));
        $request->session()->put('alumno', $alumno);

        return redirect(route('principal'));
    }
}
