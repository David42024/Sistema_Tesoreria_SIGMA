<?php

namespace App\Http\Controllers\Home;

use App\Helpers\Home\Familiar\FamiliarHeaderComponent;
use App\Helpers\Home\Familiar\FamiliarSidebarComponent;
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
        return static::familiarIndex($request);
    }

    public static function familiarIndex(Request $request){
        if ($request->session()->get('alumno') == null) return static::familiarSinAlumnoSeleccionado($request);

        $header = Utils::crearHeaderConAlumnos($request);

        $page = CRUDTablePage::new()
            ->title("Selección de Alumno")
            ->header($header)
            ->sidebar(new FamiliarSidebarComponent());
        
        $content = CRUDTableComponent::new()
            ->title("Página principal");

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
        
        $content = CRUDTableComponent::new()
            ->title("Página principal");

        return $page->render();
    }

    public static function definirSesion(Request $request){
        $alumno = Alumno::findOrFail($request->input('idalumno'));
        $request->session()->put('alumno', $alumno);

        return redirect(route('principal'));
    }
}