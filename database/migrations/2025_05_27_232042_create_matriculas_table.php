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
        Schema::create('matriculas', function (Blueprint $table) {
            $table->increments('id_matricula');
            $table->unsignedInteger('id_alumno');
            $table->unsignedInteger('id_periodo_academico');
            $table->date('fecha_matricula');
            $table->string('escala', 5)->nullable();
            $table->text('observaciones')->nullable();
            $table->unsignedInteger('id_grado');
            $table->char('nombreSeccion', 2);
            $table->string('tipo', 20)->default('regular');
            $table->foreign('id_alumno')
                  ->references('id_alumno')
                  ->on('alumnos')
                  ->onDelete('cascade');
            $table->foreign('id_periodo_academico')
                  ->references('id_periodo_academico')
                  ->on('periodos_academicos')
                  ->onDelete('restrict');
            $table->foreign(['id_grado', 'nombreSeccion'])
                  ->references(['id_grado', 'nombreSeccion']) 
                  ->on('secciones')                          
                  ->onDelete('cascade');
            $table->boolean("estado")->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matriculas');
    }
};
