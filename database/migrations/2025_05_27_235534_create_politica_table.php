<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('politica', function (Blueprint $table) {
            $table->increments('id_politica');

            $table->unsignedInteger('id_concepto')->nullable();

            $table->string('nombre', 100);

            $table->enum('tipo', ['descuento', 'mora']);

            $table->decimal('porcentaje', 5, 2);

            $table->integer('dias_minimo')->nullable();
            $table->integer('dias_maximo')->nullable();

            $table->text('condiciones')->nullable();

            $table->foreign('id_concepto')
                  ->references('id_concepto')
                  ->on('conceptos_pago')
                  ->onDelete('cascade');

            $table->boolean("estado")->default(true);
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('politica');
    }
};
