<?php

namespace App\Providers;

use App;
use App\Helpers\Exporter\Factories\ExporterFactory;
use App\Helpers\Exporter\Factories\ExportRequestFactory;
use App\Helpers\Exporter\Services\ExporterService;
use App\Http\Controllers\AdministrativoController;
use App\Interfaces\ICronogramaAcademicoService;
use App\Interfaces\IExporterFactory;
use App\Interfaces\IExporterService;
use App\Interfaces\IExportRequestFactory;
use App\Interfaces\IRegistroPagosService;
use App\Models\Administrativo;
use App\Models\Alumno;
use App\Models\Catedra;
use App\Models\ConceptoAccion;
use App\Models\ConceptoPago;
use App\Models\Configuracion;
use App\Models\Curso;
use App\Models\DepartamentoAcademico;
use App\Models\DetallePago;
use App\Models\Deuda;
use App\Models\Familiar;
use App\Models\Matricula;
use App\Models\NivelEducativo;
use App\Models\Pago;
use App\Models\PeriodoAcademico;
use App\Models\Personal;
use App\Models\Seccion;
use App\Models\User;
use App\Observers\AdministrativoObserver;
use App\Observers\AlumnoObserver;
use App\Observers\CatedraObserver;
use App\Observers\ConceptoAccionObserver;
use App\Observers\ConceptoPagoObserver;
use App\Observers\CursoObserver;
use App\Observers\DepartamentoAcademicoObserver;
use App\Observers\DetallePagoObserver;
use App\Observers\DeudaObserver;
use App\Observers\FamiliarObserver;
use App\Observers\MatriculaObserver;
use App\Observers\NivelEducativoObserver;
use App\Observers\PagoObserver;
use App\Observers\PersonalObserver;
use App\Observers\SeccionObserver;
use App\Observers\UserObserver;
use App\Services\Contable\RegistroPagosService;
use App\Services\Cronograma\CronogramaAcademicoService;
use App\Services\Matricula\GeneraConstanciaMatricula;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(IExporterFactory::class, ExporterFactory::class);
        $this->app->singleton(IExportRequestFactory::class, ExportRequestFactory::class);
        $this->app->singleton(IExporterService::class, ExporterService::class);
        $this->app->singleton(GeneraConstanciaMatricula::class, function () {
            return new GeneraConstanciaMatricula('constancias.matricula');
        });
        $this->app->singleton(ICronogramaAcademicoService::class, CronogramaAcademicoService::class);
        $this->app->singleton(IRegistroPagosService::class, RegistroPagosService::class);
    }

    public function boot(): void
    {
        if (!App::runningInConsole()) {
            $this->noConsoleBoot();
        }

        $this->registerObservers();

        Paginator::useBootstrapFour();

        $this->defineResourcePermissions();

        Gate::define('is-admin', function (User $user) {
            $admin = Administrativo::where('id_usuario', '=', $user->id_usuario)->get();
            return count($admin) == 1;
        });

        Gate::define('is-preapoderado', function (User $user) {
            return $user->tipo === 'PreApoderado';
        });

        Gate::define('access-resource', function (User $user, $resource) {
            return $this->hasResourcePermissions($user, $resource);
        });

        Gate::define('manage-resource', function (User $user, $resource, $action = 'view') {
            return $this->hasResourcePermissions($user, $resource, $action);
        });
    }

    protected function noConsoleBoot()
    {
        CronogramaAcademicoService::establecerPeriodo(PeriodoAcademico::find(Configuracion::obtener(Configuracion::ID_PERIODO_ACADEMICO_ACTUAL)));
    }

    private function registerObservers()
    {
        Administrativo::observe(AdministrativoObserver::class);
        Alumno::observe(AlumnoObserver::class);
        Catedra::observe(CatedraObserver::class);
        ConceptoAccion::observe(ConceptoAccionObserver::class);
        ConceptoPago::observe(ConceptoPagoObserver::class);
        Curso::observe(CursoObserver::class);
        DepartamentoAcademico::observe(DepartamentoAcademicoObserver::class);
        // DetallePago::observe(DetallePagoObserver::class);
        Deuda::observe(DeudaObserver::class);
        Familiar::observe(FamiliarObserver::class);
        Matricula::observe(MatriculaObserver::class);
        NivelEducativo::observe(NivelEducativoObserver::class);
        Pago::observe(PagoObserver::class);
        Personal::observe(PersonalObserver::class);
        Seccion::observe(SeccionObserver::class);
        User::observe(UserObserver::class);
    }

    private function defineResourcePermissions()
    {
        config([
            'permissions' => [
                'academica' => [
                    'view' => ['Director'],
                    'create' => ['Director'],
                    'edit' => ['Director'],
                    'delete' => ['Director'],
                    'view_details' => ['Director', 'Secretaria'],
                    'download' => ['Director'],
                ],
                'alumnos' => [
                    'view' => ['Director', 'Secretaria'],
                    'create' => ['Director', 'Secretaria'],
                    'edit' => ['Director', 'Secretaria'],
                    'delete' => ['Director', 'Secretaria'],
                    'view_details' => ['Director', 'Secretaria'],
                    'download' => ['Director', 'Secretaria'],
                ],
                'personal' => [
                    'view' => ['Director', 'Secretaria'],
                    'create' => ['Director', 'Secretaria'],
                    'edit' => ['Director'],
                    'delete' => ['Director'],
                    'view_details' => ['Director', 'Secretaria'],
                    'download' => ['Director', 'Secretaria'],
                ],
                'administrativa' => [
                    'view' => ['Director'],
                    'create' => ['Director'],
                    'edit' => ['Director'],
                    'delete' => ['Director'],
                    'download' => ['Director'],
                ],
                'usuarios' => [
                    'view' => ['Director', 'Secretaria'],
                    'create' => ['Director'],
                    'edit' => ['Director'],
                    'delete' => ['Director'],
                    'download' => ['Director', 'Secretaria'],
                ],
                'financiera' => [
                    'view' => ['Director', 'Secretaria'],
                    'create' => ['Director', 'Secretaria'],
                    'edit' => ['Director'],
                    'delete' => ['Director'],
                    'view_details' => ['Director', 'Secretaria'],
                    'download' => ['Director', 'Secretaria'],
                ],
                'configuracion' => [
                    'view' => ['Director'],
                    'create' => ['Director'],
                    'edit' => ['Director'],
                    'delete' => ['Director'],
                ],
                'reportes' => [
                    'view' => ['Director'],
                    'create' => ['Director'],
                    'edit' => ['Director'],
                    'delete' => ['Director'],
                    'download' => ['Director'],
                ],
                // ✅ AGREGADO: Permisos para composicion_familiar
                'composicion_familiar' => [
                    'view' => ['Director', 'Secretaria'],
                    'create' => ['Director'],
                    'edit' => ['Director'],
                    'delete' => ['Director'],
                    'view_details' => ['Director', 'Secretaria'],
                    'download' => ['Director'],
                ],
                // ✅ AGREGADO: Permisos para solicitudes_prematricula
                'solicitudes_prematricula' => [
                    'view' => ['Director', 'Secretaria'],
                    'create' => ['Director', 'Secretaria'],
                    'edit' => ['Director', 'Secretaria'],
                    'delete' => ['Director'],
                    'download' => ['Director', 'Secretaria'],
                ],
            ]
        ]);

        config([
            'familiar-permissions' => [
                'datos' => [
                    'view' => ['Familiar'],
                ],
                'matriculas' => [
                    'view' => ['Familiar'],
                ],
                'pagos' => [
                    'view' => ['Familiar'],
                    'create' => ['Familiar'],
                ],
                'traslado' => [
                    'view' => ['Familiar'],
                    'create' => ['Familiar'],
                ],
                'academica' => [
                    'view' => ['Familiar'],
                ],
                'alumnos' => [
                    'view' => ['Familiar'],
                ],
                'personal' => [
                    'view' => ['Familiar'],
                ],
                'administrativa' => [
                    'view' => ['Familiar'],
                ],
                'financiera' => [
                    'view' => ['Familiar'],
                    'create' => ['Familiar'],
                    'view_details' => ['Familiar'],
                ],
                'reportes' => [
                    'view' => ['Familiar'],
                ],
                'orden_pago' => [
                    'view' => ['Familiar'],
                    'create' => ['Familiar'],
                ],
                'cambiar_password' => [
                    'view' => ['Familiar'],
                    'edit' => ['Familiar'],
                ],
            ]
        ]);

        config([
            'preapoderado-permissions' => [
                'solicitud' => [
                    'view' => ['PreApoderado'],
                ],
            ]
        ]);
    }

    private function hasResourcePermissions(User $user, string $resource, string $action = 'view')
    {
        if ($user->tipo == 'Administrativo') {
            $permissions = config('permissions');
            $query = Administrativo::where('id_usuario', '=', $user->id_usuario)->get();
            if ($query->count() == 0)
                return false;
            $adminAccount = $query[0];

            // Verificar que la clave del recurso y la acción existen
            if (!isset($permissions[$resource]) || !isset($permissions[$resource][$action])) {
                return false;
            }

            return in_array($adminAccount->cargo, $permissions[$resource][$action]);
        } else if ($user->tipo == 'Familiar') {
            $permissions = config('familiar-permissions');

            // Verificar que la clave del recurso y la acción existen
            if (!isset($permissions[$resource]) || !isset($permissions[$resource][$action])) {
                return false;
            }

            return in_array('Familiar', $permissions[$resource][$action]);
        } else if ($user->tipo == 'PreApoderado') {
            $permissions = config('preapoderado-permissions');

            if (!isset($permissions[$resource]) || !isset($permissions[$resource][$action])) {
                return false;
            }

            return in_array('PreApoderado', $permissions[$resource][$action]);
        } else {
            return false;
        }
    }
}
