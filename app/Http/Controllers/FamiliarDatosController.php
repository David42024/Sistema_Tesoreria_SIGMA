<?php

namespace App\Http\Controllers;

use App\Helpers\CRUDTablePage;
use App\Helpers\Home\Familiar\FamiliarSidebarComponent;
use App\Helpers\Tables\CRUDTableComponent;
use App\Helpers\Tables\ViewBasedComponent;
use App\Http\Controllers\Home\Utils;
use App\Models\Alumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FamiliarDatosController extends Controller
{
    public static function index(Request $request){
        $requested = $request->session()->get('alumno');

        if ($requested == null){
            return redirect(route('principal'));
        }

        $header = Utils::crearHeaderConAlumnos($request);

        $page = CRUDTablePage::new()
            ->title("Selección de Alumno")
            ->header($header)
            ->sidebar(new FamiliarSidebarComponent());

        $sexos = [
            ['id' => 'M', 'descripcion' => 'Masculino'],
            ['id' => 'F', 'descripcion' => 'Femenino']
        ];

        $estadosciviles = [
            ['id' => 'C', 'descripcion' => 'Casado'],
            ['id' => 'S', 'descripcion' => 'Soltero'],
            ['id' => 'V', 'descripcion' => 'Viudo'],
            ['id' => 'D', 'descripcion' => 'Divorciado']
        ];

        $escalas = [
            ['id' => 'A', 'descripcion' => 'A'],
            ['id' => 'B', 'descripcion' => 'B'],
            ['id' => 'C', 'descripcion' => 'C'],
            ['id' => 'D', 'descripcion' => 'D'],
            ['id' => 'E', 'descripcion' => 'E'],
        ];

        $lenguasmaternas = [
            ['id' => 'Castellano', 'descripcion' => 'Castellano'],
            ['id' => 'Quechua', 'descripcion' => 'Quechua'],
            ['id' => 'Aymara', 'descripcion' => 'Aymara'],
            ['id' => 'Ashaninka', 'descripcion' => 'Asháninka'],
            ['id' => 'Shipibo-Konibo', 'descripcion' => 'Shipibo-Konibo'],
            ['id' => 'Awajun', 'descripcion' => 'Awajún'],
            ['id' => 'Achuar', 'descripcion' => 'Achuar'],
            ['id' => 'Shawi', 'descripcion' => 'Shawi'],
            ['id' => 'Matsigenka', 'descripcion' => 'Matsigenka'],
            ['id' => 'Yanesha', 'descripcion' => 'Yánesha'],
            ['id' => 'Otro', 'descripcion' => 'Otro']
        ];


        $ubigeo = json_decode(file_get_contents(resource_path('data/ubigeo_peru.json')), true);
        $paises = $ubigeo['paises'];
        $departamentos = $ubigeo['departamentos'];
        $provincias = $ubigeo['provincias'];
        $distritos = $ubigeo['distritos'];

        // Buscar las descripciones del ubigeo
        $departamentoDesc = collect($departamentos)->firstWhere('id_departamento', $requested->departamento)['descripcion'] ?? $requested->departamento;
        $provinciaDesc = collect($provincias)->firstWhere('id_provincia', $requested->provincia)['descripcion'] ?? $requested->provincia;
        $distritoDesc = collect($distritos)->firstWhere('id_distrito', $requested->distrito)['descripcion'] ?? $requested->distrito;

        $data = [
            'return' => route('principal'),
            'id' => $requested->getKey(),
            'alumno' => $requested,
            'foto_url' => $requested->foto_url,
            'sexos' => $sexos,
            'paises' => $paises,
            'provincias' => $provincias,
            'distritos' => $distritos,
            'departamentos' => $departamentos,
            'estadosciviles' => $estadosciviles,
            'lenguasmaternas' => $lenguasmaternas,
            'escalas' => $escalas,
            'default' => [
                'codigo_educando' => $requested->codigo_educando,
                'codigo_modular' => $requested->codigo_modular,
                'año_ingreso' => $requested->año_ingreso,
                'd_n_i' => $requested->dni,
                'apellido_paterno' => $requested->apellido_paterno,
                'apellido_materno' => $requested->apellido_materno,
                'primer_nombre' => $requested->primer_nombre,
                'otros_nombres' => $requested->otros_nombres,
                'sexo' => $requested->sexo,
                'fecha_nacimiento' => $requested->fecha_nacimiento,
                'pais' => $requested->pais,
                'departamento' => $departamentoDesc,
                'provincia' => $provinciaDesc,
                'distrito' => $distritoDesc,
                'lengua_materna' => $requested->lengua_materna,
                'estado_civil' => $requested->estado_civil,
                'religion' => $requested->religion,
                'fecha_bautizo' => $requested->fecha_bautizo,
                'parroquia_de_bautizo' => $requested->parroquia_bautizo,
                'colegio_de_procedencia' => $requested->colegio_procedencia,
                'direccion' => $requested->direccion,
                'telefono' => $requested->telefono,
                'medio_de_transporte' => $requested->medio_transporte,
                'tiempo_de_demora' => $requested->tiempo_demora,
                'material_vivienda' => $requested->material_vivienda,
                'energia_electrica' => $requested->energia_electrica,
                'agua_potable' => $requested->agua_potable,
                'desague' => $requested->desague,
                's_s__h_h' => $requested->ss_hh,
                'numero_de_habitaciones' => $requested->num_habitaciones,
                'numero_de_habitantes' => $requested->num_habitantes,
                'situacion_de_vivienda' => $requested->situacion_vivienda,
                'escala' => $requested->escala,
            ]
        ];
        $content = new ViewBasedComponent('homev2.familiares.datos_view', compact('data'));
        $page->content($content);
        return $page->render();
    }

    public function actualizar(Request $request)
    {
        $alumno = $request->session()->get('alumno');

        if ($alumno == null) {
            return redirect(route('principal'));
        }

        // Validar los datos del formulario
        $validated = $request->validate([
            'primer_nombre' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'required|string|max:255',
            'otros_nombres' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'pais' => 'nullable|string',
            'departamento' => 'nullable|string',
            'provincia' => 'nullable|string',
            'distrito' => 'nullable|string',
            'lengua_materna' => 'nullable|string',
            'estado_civil' => 'nullable|string',
            'religion' => 'nullable|string|max:255',
            'parroquia_bautizo' => 'nullable|string|max:255',
            'medio_transporte' => 'nullable|string|max:255',
            'tiempo_demora' => 'nullable|string|max:100',
            'material_vivienda' => 'nullable|string|max:255',
            'energia_electrica' => 'nullable|string|max:50',
            'agua_potable' => 'nullable|string|max:50',
            'desague' => 'nullable|string|max:50',
            'ss_hh' => 'nullable|string|max:50',
            'num_habitantes' => 'nullable|integer',
            'situacion_vivienda' => 'nullable|string|max:255',
            'escala' => 'nullable|string|max:10',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Actualizar los datos del alumno
        $alumno->primer_nombre = $validated['primer_nombre'];
        $alumno->apellido_paterno = $validated['apellido_paterno'];
        $alumno->apellido_materno = $validated['apellido_materno'];
        $alumno->otros_nombres = $validated['otros_nombres'] ?? null;
        $alumno->telefono = $validated['telefono'] ?? null;
        $alumno->direccion = $validated['direccion'] ?? null;
        $alumno->pais = $validated['pais'] ?? null;
        $alumno->departamento = $validated['departamento'] ?? null;
        $alumno->provincia = $validated['provincia'] ?? null;
        $alumno->distrito = $validated['distrito'] ?? null;
        $alumno->lengua_materna = $validated['lengua_materna'] ?? null;
        $alumno->estado_civil = $validated['estado_civil'] ?? null;
        $alumno->religion = $validated['religion'] ?? null;
        $alumno->parroquia_bautizo = $validated['parroquia_bautizo'] ?? null;
        $alumno->medio_transporte = $validated['medio_transporte'] ?? null;
        $alumno->tiempo_demora = $validated['tiempo_demora'] ?? null;
        $alumno->material_vivienda = $validated['material_vivienda'] ?? null;
        $alumno->energia_electrica = $validated['energia_electrica'] ?? null;
        $alumno->agua_potable = $validated['agua_potable'] ?? null;
        $alumno->desague = $validated['desague'] ?? null;
        $alumno->ss_hh = $validated['ss_hh'] ?? null;
        $alumno->num_habitantes = $validated['num_habitantes'] ?? null;
        $alumno->situacion_vivienda = $validated['situacion_vivienda'] ?? null;
        $alumno->escala = $validated['escala'] ?? null;

        // Procesar la foto si se ha subido una nueva
        if ($request->hasFile('foto')) {
            // Eliminar la foto anterior si existe
            if ($alumno->foto_url && Storage::exists($alumno->foto_url)) {
                Storage::delete($alumno->foto_url);
            }

            // Guardar la nueva foto
            $path = $request->file('foto')->store('fotos_alumnos', 'public');
            $alumno->foto_url = $path;
        }

        $alumno->save();

        // Actualizar el alumno en la sesión
        $request->session()->put('alumno', $alumno);

        return redirect()->route('familiar.alumno-datos.view')
            ->with('success', 'Datos actualizados correctamente');
    }
}
