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
        Schema::create('secciones', function (Blueprint $table) {
            $table->unsignedInteger('id_grado');
            $table->char('nombreSeccion', 2);
            $table->unsignedTinyInteger('capacidad_maxima')->default(30);
            $table->primary(['id_grado', 'nombreSeccion']);

            $table->foreign('id_grado')
                  ->references('id_grado') 
                  ->on('grados')        
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
        Schema::dropIfExists('secciones');
    }
};
