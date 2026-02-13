<?php

namespace Database\Seeders;

use App\Models\Administrativo;
use App\Models\Alumno;
use App\Models\Catedra;
use App\Models\ComposicionFamiliar;
use App\Models\ConceptoPago;
use App\Models\Curso;
use App\Models\DepartamentoAcademico;
use App\Models\Deuda;
use App\Models\Familiar;
use App\Models\Grado;
use App\Models\Matricula;
use App\Models\NivelEducativo;
use App\Models\Personal;
use App\Models\Seccion;
use App\Models\User;
use App\Observers\Traits\LogsActions;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as FakerFactory;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Deshabilitamos temporalmente el registro de acciones, ya que estamos ejecutando un seeder.
        LogsActions::disable();

        if (!User::where('username', 'director')->exists()) {
            Administrativo::create([
                'id_usuario' => User::factory([
                    'username' => 'director',
                    'password' => bcrypt("12345"),
                    'tipo' => 'Administrativo',
                ])->create()->id_usuario,

                'apellido_paterno' => fake()->lastName(),
                'apellido_materno' => fake()->lastName(),
                'primer_nombre' => fake()->firstName(),
                'otros_nombres' => fake()->randomElement([fake()->name(), fake()->name() . " " . fake()->name()]),
                'dni' => fake()->numberBetween(10000000, 99999999),
                'direccion' => fake()->address(),
                'estado_civil' => fake()->randomElement(['S', 'C', 'V', 'D']),
                'telefono' => fake()->phoneNumber(),
                'seguro_social' => fake()->numberBetween('1000000000'),
                'fecha_ingreso' => fake()->dateTimeThisDecade(),
                'cargo' => 'Director',
                'sueldo' => fake()->numberBetween(5000, 10000),
                'estado' => '1'
            ]);
        }

        if (!User::where('username', 'secretaria')->exists()) {
            Administrativo::create([
                'id_usuario' => User::factory([
                    'username' => 'secretaria',
                    'password' => bcrypt("12345"),
                    'tipo' => 'Administrativo',
                ])->create()->id_usuario,
                
                'apellido_paterno' => fake()->lastName(),
                'apellido_materno' => fake()->lastName(),
                'primer_nombre' => fake()->firstName(),
                'otros_nombres' => fake()->randomElement([fake()->name(), fake()->name() . " " . fake()->name()]),
                'dni' => fake()->numberBetween(10000000, 99999999),
                'direccion' => fake()->address(),
                'estado_civil' => fake()->randomElement(['S', 'C', 'V', 'D']),
                'telefono' => fake()->phoneNumber(),
                'seguro_social' => fake()->numberBetween('1000000000'),
                'fecha_ingreso' => fake()->dateTimeThisDecade(),
                'cargo' => 'Secretaria',
                'sueldo' => fake()->numberBetween(1500, 3500),
                'estado' => '1'
            ]);
        }


        $anioActual = Carbon::now()->year;
        $fechaNacimiento = Carbon::now()->subYears(10)->format('Y-m-d');
        $fechaBautizo = Carbon::now()->subYears(9)->format('Y-m-d');

        


        // Restablecemos el registro de acciones.
        LogsActions::enable();
    }


    

}
