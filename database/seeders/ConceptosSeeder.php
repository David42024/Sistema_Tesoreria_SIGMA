<?php

namespace Database\Seeders;

use App\Models\Catedra;
use App\Models\ConceptoAccion;
use App\Models\ConceptoPago;
use App\Models\Curso;
use App\Models\Curso_Grado;
use App\Models\DepartamentoAcademico;
use App\Models\Deuda;
use App\Models\Grado;
use App\Models\NivelEducativo;
use App\Models\Personal;
use App\Models\Seccion;
use App\Models\User;
use App\Observers\Traits\LogsActions;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Faker\Factory as FakerFactory;
class ConceptosSeeder extends Seeder
{

    public function run(): void
    {
        // Deshabilitamos temporalmente el registro de acciones, ya que estamos ejecutando un seeder.
        LogsActions::disable();

        ConceptoAccion::create(
            [
                'accion' => 'VER',
            ]);

        ConceptoAccion::create(
            [
                'accion' => 'EDITAR',
            ]);

        ConceptoAccion::create(
            [
                'accion' => 'ELIMINAR',
            ]);

        ConceptoAccion::create(
            [
                'accion' => 'RESTAURAR',
            ]);

        ConceptoAccion::create(
            [
                'accion' => 'CREAR',
            ]);

        $meses = [
                'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO',
                'JULIO', 'AGOSTO', 'SETIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'
                ];
        $anios = [2025, 2026];
        $escalas = [
            'A' => 500.00,
            'B' => 400.00,
            'C' => 300.00,
            'D' => 200.00,
            'E' => 100.00,
        ];

        foreach ($anios as $anio) {
            foreach ($meses as $mes) {
                foreach ($escalas as $escala => $monto) {
                    ConceptoPago::create([
                        'descripcion' => "$mes $anio",
                        'escala' => $escala,
                        'monto' => $monto,
                        'estado' => true,
                    ]);
                }
            }
        }

        $departamentos = ['Personal Docente Inicial','Personal Docente Primaria','Personal Docente Secundaria'];


        $datosdepartamentos = [];

        foreach($departamentos as $departamento){
            $nuevodepartamento = DepartamentoAcademico::create(
        ['nombre' => $departamento]
            );
            $datosdepartamentos[] = $nuevodepartamento;
        }

        $niveleseducativos = [
            [
                'nombre_nivel'=>'INICIAL',
                'descripcion'=> 'Educación Inicial',
            ],
            [
                'nombre_nivel'=>'PRIMARIA',
                'descripcion'=> 'Educación Primaria',
            ],
            [
                'nombre_nivel'=>'SECUNDARIA',
                'descripcion'=> 'Educación Secundaria',
            ],
        ];

        $datosniveleseducativos = [];

        foreach($niveleseducativos as $niveleducativo){
            $nuevoniveleducativo = NivelEducativo::create(
                [
                    'nombre_nivel'=> $niveleducativo['nombre_nivel'],
                    'descripcion' => $niveleducativo['descripcion']
                ]
            );
            $datosniveleseducativos[] = $nuevoniveleducativo;
        }

        $datosgrados = [];

        foreach ($datosniveleseducativos as $nivel) {

            if ($nivel->nombre_nivel === 'INICIAL') {
                $grados = [
                    '3 AÑOS',
                    '4 AÑOS',
                    '5 AÑOS'
                ];
            } elseif($nivel->nombre_nivel === 'PRIMARIA') {
                $grados = [
                    'PRIMERO',
                    'SEGUNDO',
                    'TERCERO',
                    'CUARTO',
                    'QUINTO',
                    'SEXTO'
                ];
            } else{
                $grados = [
                    'PRIMERO',
                    'SEGUNDO',
                    'TERCERO',
                    'CUARTO',
                    'QUINTO'
                ];
            }

            foreach ($grados as $grado) {
                $nuevoGrado = Grado::create([
                    'id_nivel' => $nivel['id_nivel'],
                    'nombre_grado' => $grado
                ]);
                $datosgrados[] = $nuevoGrado;
            }
        }

        $cursos = [
            [
                'nombre_curso' => 'MATEMÁTICA',
                'codigo_curso'=> 'MA',
                'nivel_inicial' => true,
            ],
            [
                'nombre_curso' => 'CIENCIAS',
                'codigo_curso'=> 'CI',
                'nivel_inicial' => true,
            ],
            [
                'nombre_curso' => 'LENGUAJE',
                'codigo_curso'=> 'LE',
                'nivel_inicial' => true,
            ],
            [
                'nombre_curso' => 'HISTORIA',
                'codigo_curso'=> 'HI',
                'nivel_inicial' => false,
            ],
            [
                'nombre_curso' => 'GEOGRAFÍA',
                'codigo_curso'=> 'GE',
                'nivel_inicial' => false,
            ],
            [
                'nombre_curso' => 'INGLÉS',
                'codigo_curso'=> 'IN',
                'nivel_inicial' => false,
            ],
            [
                'nombre_curso' => 'EDUCACIÓN FÍSICA',
                'codigo_curso'=> 'EF',
                'nivel_inicial' => true,
            ],
        ];


        $datoscursos = [];
        foreach ($datosniveleseducativos as $nivel) {
            foreach ($cursos as $curso) {
                if($nivel->nombre_nivel == "INICIAL"){
                    if(!$curso['nivel_inicial']){

                    }else{
                        $nuevocurso = Curso::create([
                            'codigo_curso' => $curso['codigo_curso'],
                            'nombre_curso' => $curso['nombre_curso'],
                            'id_nivel' => $nivel['id_nivel'],
                        ]);
                        $datoscursos[] = $nuevocurso;
                    }
                }
                else{
                        $nuevocurso = Curso::create([
                            'codigo_curso' => $curso['codigo_curso'],
                            'nombre_curso' => $curso['nombre_curso'],
                            'id_nivel' => $nivel['id_nivel']
                        ]);
                    $datoscursos[] = $nuevocurso;
                }
            }

        }

        $añosescolares = [
            2025,2026
        ];

        foreach ($datosgrados as $grado) {
        // Filtrar solo los cursos que pertenezcan al mismo nivel del grado
            $cursosDelMismoNivel = collect($datoscursos)->where('id_nivel', $grado->id_nivel);

            foreach ($cursosDelMismoNivel as $curso) {
                foreach ($añosescolares as $año) {
                    Curso_Grado::create([
                        'id_curso' => $curso->id_curso,
                        'id_grado' => $grado->id_grado,
                        'año_escolar' => $año
                    ]);
                }
            }
        }

        $datossecciones = [];

        $secciones = [
            'A','B','C','D','E'
        ];

        foreach($datosgrados as $grado){
            foreach($secciones as $seccion){
                $nuevaseccion = Seccion::create([
                    'id_grado' => $grado->id_grado,
                    'nombreSeccion' => $seccion
                ]);
                $datossecciones[] = $nuevaseccion;
            }
        }

        //Crear por cada seccion un profesor de categoria Titular, otro de categoria Asociado y otro de categoria Auxiliar
        //seccion depende de grado, grado depende de nivel educativo y si nivel educativo es Inicial, departamento academico tambien inicial, lo mismo con primaria y secundaria
        //el username que sea personal_$categoria_dni; puros nombres en español y apellidos igual, telefonos peruanos +51, fecha de ingreso todos del 2025
        $faker = FakerFactory::create('es_PE');

        $categorias = ['Titular', 'Asociado', 'Auxiliar'];


        $docentesTitularesPorSeccion = [];

        foreach ($datossecciones as $seccion) {

            // Obtener grado de esta sección
            $grado = collect($datosgrados)->firstWhere('id_grado', $seccion->id_grado);

            // Obtener nivel educativo del grado
            $nivel = collect($datosniveleseducativos)->firstWhere('id_nivel', $grado->id_nivel);


            // Determinar departamento según nivel
            switch ($nivel->nombre_nivel) {
                case 'INICIAL':
                    $departamento = collect($datosdepartamentos)->firstWhere('nombre', 'Personal Docente Inicial');
                    break;
                case 'PRIMARIA':
                    $departamento = collect($datosdepartamentos)->firstWhere('nombre', 'Personal Docente Primaria');
                    break;
                case 'SECUNDARIA':
                    $departamento = collect($datosdepartamentos)->firstWhere('nombre', 'Personal Docente Secundaria');
                    break;
            }

            foreach ($categorias as $categoria) {

                $dni = $faker->unique()->numberBetween(10000000, 99999999);

                // Crear usuario
                $usuario = User::create([
                    'username' => 'personal_' . $categoria . '_' . $dni,
                    'password' => bcrypt('12345'),
                    'tipo' => 'Personal',
                ]);

                // Crear personal
                $personal = Personal::create([
                    'id_usuario' => $usuario->id_usuario,
                    'codigo_personal' => $faker->unique()->numerify('P####'),
                    'apellido_paterno' => $faker->lastName(),
                    'apellido_materno' => $faker->lastName(),
                    'primer_nombre' => $faker->firstName(),
                    'otros_nombres' => $faker->optional()->firstName(),
                    'dni' => $dni,
                    'direccion' => $faker->address(),
                    'estado_civil' => $faker->randomElement(['S', 'C', 'V', 'D']),
                    'telefono' => '+51 ' . $faker->numerify('9########'),
                    'seguro_social' => $faker->numerify('##########'),
                    'fecha_ingreso' => '2025-01-01',
                    'departamento' => $departamento->nombre,
                    'categoria' => $categoria,
                    'id_departamento' => $departamento->id_departamento,
                    'estado' => true,
                ]);

                if ($categoria === 'Titular') {
                    $claveSeccion = $seccion->id_grado . '|' . $seccion->nombreSeccion;
                    $docentesTitularesPorSeccion[$claveSeccion] = $personal;
                }

            }
        }

        #Modificar de tal forma que cada docente Titular tenga una cátedra, es decir, por cada combinación de grado, curso y/o seccion asignar uno de los docentes titulares;
        #se supone que hay una cantidad igual de secciones y docentes, por lo que habría que asignar a un docente por seccion

        foreach ($datossecciones as $seccion) {

            $grado = collect($datosgrados)->firstWhere('id_grado', $seccion->id_grado);

            $nivel = collect($datosniveleseducativos)->firstWhere('id_nivel', $grado->id_nivel);

            $claveSeccion = $seccion->id_grado . '|' . $seccion->nombreSeccion;
            $titular = $docentesTitularesPorSeccion[$claveSeccion];

            foreach ($datoscursos as $curso) {

                if ($curso->id_nivel !== $nivel->id_nivel) {
                    continue;
                }

                foreach ($añosescolares as $año) {

                    Catedra::create([
                        'id_personal' => $titular->id_personal,
                        'id_curso' => $curso->id_curso,
                        'año_escolar' => $año,
                        'id_grado' => $grado->id_grado,
                        'secciones_nombreSeccion' => $seccion->nombreSeccion,
                    ]);
                }
            }
        }

        // Restablecemos el registro de acciones.
        LogsActions::enable();
    }
}
