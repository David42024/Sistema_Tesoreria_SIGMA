<?php

namespace Database\Seeders;

use App\Observers\Traits\LogsActions;
use Illuminate\Database\Seeder;
use App\Models\Alumno;
use App\Models\Familiar;
use App\Models\Matricula;
use App\Models\Grado;
use App\Models\User;
use App\Models\ComposicionFamiliar;
use Faker\Factory as Faker;
use Carbon\Carbon;
use App\Models\PeriodoAcademico;

class AlumnosSeeder extends Seeder
{
    public function run()
    {

        LogsActions::disable();

        $faker = Faker::create('es_PE');
        $anioActual = Carbon::now()->year;
        $secciones = ['A', 'B', 'C'];

        // Orden estricto: 3 grados Inicial, 6 Primaria, 5 Secundaria
        $nivelesGrados = [
            'Inicial' => ['3 AÑOS', '4 AÑOS', '5 AÑOS'],
            'Primaria' => ['PRIMERO', 'SEGUNDO', 'TERCERO', 'CUARTO', 'QUINTO', 'SEXTO'],
            'Secundaria' => ['PRIMERO', 'SEGUNDO', 'TERCERO', 'CUARTO', 'QUINTO']
        ];

        foreach ($nivelesGrados as $nivel => $grados) {
            foreach ($grados as $nombreGrado) {
                $grado = Grado::where('nombre_grado', $nombreGrado)
                    ->whereHas('nivelEducativo', function ($q) use ($nivel) {
                        $q->where('nombre_nivel', $nivel);
                    })->firstOrFail();

                foreach ($secciones as $nombreSeccion) {
                    // 20 alumnos por sección → 4 por escala
                    foreach (['A', 'B', 'C', 'D', 'E'] as $escala) {
                        for ($i = 0; $i < 4; $i++) {

                            // FECHAS
                            $fechaNacimiento = Carbon::now()->subYears(10)->subDays(rand(0, 365));
                            $fechaBautizo = $fechaNacimiento->copy()->addYear();

                            // UBICACIÓN aleatoria
                            $ubicaciones = [
                                ['departamento' => 'LIMA', 'provincia' => 'LIMA', 'distrito' => 'ATE'],
                                ['departamento' => 'LA LIBERTAD', 'provincia' => 'TRUJILLO', 'distrito' => 'FLORENCIA DE MORA'],
                                ['departamento' => 'CUSCO', 'provincia' => 'CUSCO', 'distrito' => 'SAN SEBASTIAN'],
                                ['departamento' => 'PIURA', 'provincia' => 'PIURA', 'distrito' => 'CASTILLA'],
                                ['departamento' => 'JUNIN', 'provincia' => 'HUANCAYO', 'distrito' => 'EL TAMBO'],
                                ['departamento' => 'AREQUIPA', 'provincia' => 'AREQUIPA', 'distrito' => 'CERRO COLORADO']
                            ];
                            $ubi = $faker->randomElement($ubicaciones);

                            // Crear ALUMNO
                            $alumno = Alumno::create([
                                'codigo_educando' => $faker->unique()->numerify('######'),
                                'codigo_modular' => $faker->unique()->numerify('######'),
                                'año_ingreso' => $anioActual,
                                'dni' => $faker->unique()->numerify('########'),
                                'apellido_paterno' => $faker->lastName,
                                'apellido_materno' => $faker->lastName,
                                'primer_nombre' => $faker->firstName,
                                'otros_nombres' => $faker->firstName,
                                'sexo' => $faker->randomElement(['M', 'F']),
                                'fecha_nacimiento' => $fechaNacimiento->format('Y-m-d'),
                                'pais' => 'PERÚ',
                                'departamento' => strtoupper($ubi['departamento']),
                                'provincia' => strtoupper($ubi['provincia']),
                                'distrito' => strtoupper($ubi['distrito']),
                                'lengua_materna' => 'CASTELLANO',
                                'estado_civil' => 'S',
                                'religion' => 'CATÓLICA',
                                'fecha_bautizo' => $fechaBautizo->format('Y-m-d'),
                                'parroquia_bautizo' => $faker->company,
                                'colegio_procedencia' => $faker->company,
                                'direccion' => strtoupper($faker->address),
                                'telefono' => $faker->numerify('+51 9########'),
                                'medio_transporte' => $faker->randomElement(['A PIE', 'MOTO', 'BICICLETA', 'OMNIBUS']),
                                'tiempo_demora' => $faker->numberBetween(5, 60) . ' MIN',
                                'material_vivienda' => $faker->randomElement(['ADOBE', 'LADRILLO/CEMENTO', 'MADERA']),
                                'energia_electrica' => 'INSTALACION DOMICILIARIA',
                                'agua_potable' => 'INSTALACION DOMICILIARIA',
                                'desague' => 'INSTALACION DOMICILIARIA',
                                'ss_hh' => 'INODORO CON AGUA CORRIENTE',
                                'num_habitaciones' => $faker->numberBetween(1, 6),
                                'num_habitantes' => $faker->numberBetween(3, 8),
                                'situacion_vivienda' => $faker->randomElement(['PROPIA', 'ALQUILADA', 'CEDIDA']),
                                'escala' => $escala,
                                'estado' => true,
                                'foto' => null
                            ]);

                            $dni_familiar = $faker->unique()->numerify('########');

                            // Crear FAMILIAR y su USER
                            $user = User::factory()->create([
                                'username' => $dni_familiar,
                                'password' => bcrypt('12345'),
                                'tipo' => 'Familiar',
                            ]);

                            $familiar = Familiar::create([
                                'id_usuario' => $user->id_usuario,
                                'dni' => $dni_familiar,
                                'apellido_paterno' => $faker->lastName,
                                'apellido_materno' => $faker->lastName,
                                'primer_nombre' => $faker->firstName,
                                'otros_nombres' => $faker->optional()->firstName,
                                'numero_contacto' => $faker->phoneNumber,
                                'correo_electronico' => $faker->unique()->safeEmail,
                                'estado' => true,
                            ]);

                            ComposicionFamiliar::create([
                                'id_alumno' => $alumno->id_alumno,
                                'id_familiar' => $familiar->idFamiliar,
                                'parentesco' => $faker->randomElement(['Padre', 'Madre', 'Tutor', 'Abuelo', 'Tía']),
                                'estado' => true
                            ]);

                            $periodoActual = PeriodoAcademico::actual();

                            // Matricula del alumno
                            $matricula = Matricula::create([
                                'id_alumno' => $alumno->id_alumno,
                                'id_periodo_academico' => $periodoActual?->id_periodo_academico,
                                'fecha_matricula' => now(),
                                'escala' => $escala,
                                'id_grado' => $grado->id_grado,
                                'nombreSeccion' => $nombreSeccion,
                                'estado' => true
                            ]);

                            $matricula->generarDeudas();
                        }
                    }
                }
            }
        }

        LogsActions::enable();

    }
}
