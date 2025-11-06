<?php

namespace Database\Seeders;

use App\Models\Administrativo;
use App\Models\Curso_Grado;
use App\Models\User;
use App\Models\Alumno;
use App\Models\Catedra;
use App\Models\ComposicionFamiliar;
use App\Models\ConceptoAccion;
use App\Models\ConceptoPago;
use App\Models\Curso;
use App\Models\DepartamentoAcademico;
use App\Models\DetallePago;
use App\Models\Deuda;
use App\Models\Familiar;
use App\Models\Grado;
use App\Models\Matricula;
use App\Models\NivelEducativo;
use App\Models\Pago;
use App\Models\Personal;
use App\Models\Seccion;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Observers\Traits\LogsActions;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Deshabilitamos temporalmente el registro de acciones, ya que estamos ejecutando un seeder.
        LogsActions::disable();

        // Ejecutar seeders en el orden correcto
        // IMPORTANTE: ConceptosSeeder debe ejecutarse PRIMERO porque crea la estructura base
        $this->call([
            ConceptosSeeder::class,    // 1. Crea niveles, grados, secciones, conceptos de pago, etc.
            UsuariosSeeder::class,     // 2. Crea usuarios del sistema
            TestUsersSeeder::class,    // 3. Crea usuarios de prueba
            PoliticaSeeder::class,     // 4. Crea políticas
            AlumnosSeeder::class,      // 5. Crea alumnos (depende de ConceptosSeeder)
        ]);

        // Restablecemos el registro de acciones.
        LogsActions::enable();
    }
}
