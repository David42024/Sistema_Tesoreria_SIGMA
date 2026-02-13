<?php

namespace Database\Seeders;

use App\Models\ComposicionFamiliar;
use App\Models\Familiar;
use App\Models\User;
use App\Observers\Traits\LogsActions;
use Illuminate\Database\Seeder;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Deshabilitamos temporalmente el registro de acciones, ya que estamos ejecutando un seeder.
        LogsActions::disable();

        if (!User::where('username', 'familiar')->exists()) {
            ComposicionFamiliar::factory([
                'id_familiar' => Familiar::factory([
                    'id_usuario' => User::factory([
                        'username' => 'familiar',
                        'password' => bcrypt("12345"),
                        'tipo' => 'Administrativo',
                    ])->create()->id_usuario
                ])->create()->id_familiar
            ]);
        }

        // Restablecemos el registro de acciones.
        LogsActions::enable();
    }
}
