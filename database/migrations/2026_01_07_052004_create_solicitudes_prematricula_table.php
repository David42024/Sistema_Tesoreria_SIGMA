<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_prematricula', function (Blueprint $table) {
            $table->id('id_solicitud');
            
            // ========== DATOS DEL APODERADO (FAMILIAR) ==========
            $table->string('dni_apoderado', 8);
            $table->string('apellido_paterno_apoderado', 50);
            $table->string('apellido_materno_apoderado', 50)->nullable();
            $table->string('primer_nombre_apoderado', 50);
            $table->string('otros_nombres_apoderado', 100)->nullable();
            $table->string('numero_contacto', 20);
            $table->string('correo_electronico', 100)->nullable();
            $table->string('parentesco', 50);
            
            // ========== DATOS DEL ALUMNO ==========
            $table->string('dni_alumno', 8);
            $table->string('apellido_paterno_alumno', 50);
            $table->string('apellido_materno_alumno', 50)->nullable();
            $table->string('primer_nombre_alumno', 50);
            $table->string('otros_nombres_alumno', 100)->nullable();
            $table->char('sexo', 1);
            $table->date('fecha_nacimiento');
            $table->string('direccion_alumno', 255)->nullable();
            $table->string('colegio_procedencia', 100)->nullable();
            $table->string('foto_alumno', 255)->nullable();
            
            // Grado y Sección solicitados
            $table->unsignedInteger('id_grado');
            $table->char('nombreSeccion', 2)->nullable();
            
            // ========== ESTADO Y SEGUIMIENTO ==========
            $table->enum('estado', ['pendiente', 'en_revision', 'aprobado', 'rechazado'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->text('motivo_rechazo')->nullable();
            
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('id_grado')->references('id_grado')->on('grados');
            $table->foreign(['id_grado', 'nombreSeccion'])
                  ->references(['id_grado', 'nombreSeccion'])
                  ->on('secciones')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
            
            $table->unique('dni_alumno');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_prematricula');
    }
};