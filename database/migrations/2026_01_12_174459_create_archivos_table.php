<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Crear tabla archivos
        Schema::create('archivos', function (Blueprint $table) {
            $table->id('idArchivo');
            $table->string('foto', 255);
            $table->timestamps();
        });

        // Migrar datos existentes de users.foto a archivos
        $users = DB::table('users')->whereNotNull('foto')->get();
        foreach ($users as $user) {
            $archivoId = DB::table('archivos')->insertGetId([
                'foto' => $user->foto,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('users')->where('id_usuario', $user->id_usuario)->update([
                'idArchivo' => $archivoId,
            ]);
        }

        // Agregar columna idArchivo a users
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('idArchivo')->nullable()->after('tipo');
            $table->foreign('idArchivo')->references('idArchivo')->on('archivos')->onDelete('set null');
        });

        // Eliminar columna foto de users
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('foto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurar columna foto en users
        Schema::table('users', function (Blueprint $table) {
            $table->string('foto', 255)->nullable()->after('tipo');
        });

        // Migrar datos de archivos de vuelta a users
        $users = DB::table('users')->whereNotNull('idArchivo')->get();
        foreach ($users as $user) {
            $archivo = DB::table('archivos')->where('idArchivo', $user->idArchivo)->first();
            if ($archivo) {
                DB::table('users')->where('id_usuario', $user->id_usuario)->update([
                    'foto' => $archivo->foto,
                ]);
            }
        }

        // Eliminar foreign key y columna idArchivo
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['idArchivo']);
            $table->dropColumn('idArchivo');
        });

        Schema::dropIfExists('archivos');
    }
};
