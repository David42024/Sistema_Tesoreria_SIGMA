<?php

namespace App\Http\Controllers;

use App\Helpers\Home\Familiar\FamiliarHeaderComponent;
use App\Helpers\Home\Familiar\FamiliarSidebarComponent;
use App\Helpers\Tables\ViewBasedComponent;
use App\Helpers\CRUDTablePage;
use App\Http\Controllers\Home\Utils;
use App\Models\Alumno;
use App\Models\SolicitudReubicacionEscala;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FamiliarAlumnoController extends Controller
{
    public function actualizarDatos(Request $request)
    {
        $alumno = $request->session()->get('alumno');

        if (!$alumno) {
            return redirect(route('principal'));
        }

        $alumno = Alumno::find($alumno->getKey());

        $header = Utils::crearHeaderConAlumnos($request);

        $ubigeo = json_decode(file_get_contents(resource_path('data/ubigeo_peru.json')), true);

        $estadosciviles = [
            ['id' => 'C', 'descripcion' => 'Casado'],
            ['id' => 'S', 'descripcion' => 'Soltero'],
            ['id' => 'V', 'descripcion' => 'Viudo'],
            ['id' => 'D', 'descripcion' => 'Divorciado']
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

        $escalas = [
            ['id' => 'A', 'descripcion' => 'Escala A'],
            ['id' => 'B', 'descripcion' => 'Escala B'],
            ['id' => 'C', 'descripcion' => 'Escala C'],
            ['id' => 'D', 'descripcion' => 'Escala D'],
            ['id' => 'E', 'descripcion' => 'Escala E'],
        ];

        $solicitudPendiente = SolicitudReubicacionEscala::where('id_alumno', $alumno->getKey())
            ->where('estado', 'pendiente')
            ->first();

        $content = new ViewBasedComponent('homev2.familiares.alumno_actualizar_datos', [
            'data' => [
                'return' => route('familiar_matricula_prematricula_create'),
                'alumno' => $alumno,
                'departamentos' => $ubigeo['departamentos'],
                'provincias' => $ubigeo['provincias'],
                'distritos' => $ubigeo['distritos'],
                'estadosciviles' => $estadosciviles,
                'lenguasmaternas' => $lenguasmaternas,
                'escalas' => $escalas,
                'solicitud_pendiente' => $solicitudPendiente,
            ]
        ]);

    return CRUDTablePage::new()
        ->title("Actualizar Datos del Alumno")
        ->header($header)
        ->sidebar(new FamiliarSidebarComponent())
        ->content($content)
        ->render();
    }

    public function guardarDatos(Request $request)
    {
        $alumno = $request->session()->get('alumno');

        if (!$alumno) {
            return redirect(route('principal'));
        }

        $request->validate([
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'apellido_paterno' => 'required|string|max:50',
            'apellido_materno' => 'required|string|max:50',
            'primer_nombre' => 'required|string|max:50',
            'otros_nombres' => 'nullable|string|max:50',
            'departamento' => 'required|string|max:40',
            'provincia' => 'required|string|max:40',
            'distrito' => 'required|string|max:40',
            'direccion' => 'required|string|max:255',
            'lengua_materna' => 'required|string|max:50',
            'estado_civil' => 'required|in:S,C,V,D',
            'religion' => 'nullable|string|max:50',
            'parroquia_bautizo' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'medio_transporte' => 'required|string|max:50',
            'tiempo_demora' => 'required|string|max:20',
            'material_vivienda' => 'required|string|max:100',
            'energia_electrica' => 'required|string|max:100',
            'agua_potable' => 'nullable|string|max:100',
            'desague' => 'nullable|string|max:100',
            'ss_hh' => 'nullable|string|max:100',
            'num_habitantes' => 'nullable|integer|min:1|max:20',
            'situacion_vivienda' => 'required|string|max:100',
        ], [
            'foto.image' => 'El archivo debe ser una imagen.',
            'foto.mimes' => 'La foto debe ser JPG, JPEG o PNG.',
            'foto.max' => 'La foto no debe superar los 2MB.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'apellido_materno.required' => 'El apellido materno es obligatorio.',
            'primer_nombre.required' => 'El primer nombre es obligatorio.',
            'departamento.required' => 'El departamento es obligatorio.',
            'provincia.required' => 'La provincia es obligatoria.',
            'distrito.required' => 'El distrito es obligatorio.',
            'direccion.required' => 'La dirección es obligatoria.',
            'lengua_materna.required' => 'La lengua materna es obligatoria.',
            'estado_civil.required' => 'El estado civil es obligatorio.',
            'medio_transporte.required' => 'El medio de transporte es obligatorio.',
            'tiempo_demora.required' => 'El tiempo de demora es obligatorio.',
            'material_vivienda.required' => 'El material de vivienda es obligatorio.',
            'energia_electrica.required' => 'La energía eléctrica es obligatoria.',
            'situacion_vivienda.required' => 'La situación de vivienda es obligatoria.',
        ]);

        $alumnoModel = Alumno::find($alumno->getKey());

        // Manejar la foto
        $nuevaFoto = $alumnoModel->foto; // Mantener foto actual por defecto

        if ($request->input('remove_photo') === '1' || $request->input('remove_photo') == 1) {
            // Eliminar foto si existe
            if ($alumnoModel->foto) {
                Storage::disk('public')->delete($alumnoModel->foto);
            }
            $nuevaFoto = null;
        } elseif ($request->hasFile('foto')) {
            // Eliminar foto anterior si existe
            if ($alumnoModel->foto) {
                Storage::disk('public')->delete($alumnoModel->foto);
            }

            // Guardar nueva foto
            $foto = $request->file('foto');
            $nombreFoto = 'alumno_' . $alumnoModel->getKey() . '_' . time() . '.' . $foto->getClientOriginalExtension();
            $nuevaFoto = $foto->storeAs('fotos/alumnos', $nombreFoto, 'public');
        }

        // Actualizar todos los campos incluyendo foto
        $alumnoModel->foto = $nuevaFoto;
        $alumnoModel->apellido_paterno = $request->input('apellido_paterno');
        $alumnoModel->apellido_materno = $request->input('apellido_materno');
        $alumnoModel->primer_nombre = $request->input('primer_nombre');
        $alumnoModel->otros_nombres = $request->input('otros_nombres');
        $alumnoModel->departamento = $request->input('departamento');
        $alumnoModel->provincia = $request->input('provincia');
        $alumnoModel->distrito = $request->input('distrito');
        $alumnoModel->direccion = $request->input('direccion');
        $alumnoModel->lengua_materna = $request->input('lengua_materna');
        $alumnoModel->estado_civil = $request->input('estado_civil');
        $alumnoModel->religion = $request->input('religion');
        $alumnoModel->parroquia_bautizo = $request->input('parroquia_bautizo');
        $alumnoModel->telefono = $request->input('telefono');
        $alumnoModel->medio_transporte = $request->input('medio_transporte');
        $alumnoModel->tiempo_demora = $request->input('tiempo_demora');
        $alumnoModel->material_vivienda = $request->input('material_vivienda');
        $alumnoModel->energia_electrica = $request->input('energia_electrica');
        $alumnoModel->agua_potable = $request->input('agua_potable');
        $alumnoModel->desague = $request->input('desague');
        $alumnoModel->ss_hh = $request->input('ss_hh');
        $alumnoModel->num_habitantes = $request->input('num_habitantes');
        $alumnoModel->situacion_vivienda = $request->input('situacion_vivienda');
        $alumnoModel->save();

        // Actualizar sesión con datos nuevos
        $request->session()->put('alumno', $alumnoModel->fresh());

        return redirect()->route('familiar_dato_view')
            ->with('success', 'Datos del alumno actualizados correctamente.');
    }

    public function solicitarReubicacionEscala(Request $request)
    {
        $alumno = $request->session()->get('alumno');

        if (!$alumno) {
            return redirect(route('principal'));
        }

        $request->validate([
            'archivo_sisfoh' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'escala_solicitada' => 'required|in:A,B,C,D,E',
            'justificacion' => 'required|string|max:500',
        ], [
            'archivo_sisfoh.required' => 'Debe adjuntar la clasificación socioeconómica SISFOH.',
            'archivo_sisfoh.mimes' => 'El archivo debe ser PDF, JPG o PNG.',
            'archivo_sisfoh.max' => 'El archivo no debe superar los 5MB.',
            'escala_solicitada.required' => 'Debe seleccionar la escala que solicita.',
            'justificacion.required' => 'Debe ingresar una justificación.',
        ]);

        // Verificar si ya tiene solicitud pendiente
        $solicitudExistente = SolicitudReubicacionEscala::where('id_alumno', $alumno->getKey())
            ->where('estado', 'pendiente')
            ->exists();

        if ($solicitudExistente) {
            return back()->withErrors(['archivo_sisfoh' => 'Ya tiene una solicitud de reubicación pendiente.']);
        }

        // Guardar archivo
        $archivo = $request->file('archivo_sisfoh');
        $nombreArchivo = 'sisfoh_' . $alumno->getKey() . '_' . time() . '.' . $archivo->getClientOriginalExtension();
        $rutaArchivo = $archivo->storeAs('solicitudes_escala', $nombreArchivo, 'public');

        // Crear solicitud
        SolicitudReubicacionEscala::create([
            'id_alumno' => $alumno->getKey(),
            'escala_actual' => $alumno->escala ?? 'A',
            'escala_solicitada' => $request->input('escala_solicitada'),
            'justificacion' => $request->input('justificacion'),
            'archivo_sisfoh' => $rutaArchivo,
            'estado' => 'pendiente',
        ]);

        return redirect()->route('familiar_matricula_prematricula_create')
            ->with('success', 'Solicitud de reubicación de escala enviada correctamente. Será revisada por la institución.');
    }
}
