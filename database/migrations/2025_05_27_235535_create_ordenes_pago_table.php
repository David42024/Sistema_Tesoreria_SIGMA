<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes_pago', function (Blueprint $table) {
            $table->increments('id_orden');

            $table->string('codigo_orden', 50)->unique();

            $table->unsignedInteger('id_alumno');
            $table->unsignedInteger('id_matricula');
            // id_deuda eliminado - ahora cada detalle tiene su id_deuda

            $table->decimal('monto_total', 10, 2);

            $table->string('numero_cuenta', 50)->nullable();

            $table->date('fecha_orden_pago');
            $table->date('fecha_vencimiento');

            $table->enum('estado', ['pendiente', 'pagado', 'vencido', 'anulado'])->default('pendiente');

            $table->text('observaciones')->nullable();

            $table->foreign('id_alumno')
                  ->references('id_alumno')
                  ->on('alumnos')
                  ->onDelete('cascade');

            $table->foreign('id_matricula')
                  ->references('id_matricula')
                  ->on('matriculas')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('ordenes_pago');
    }
};
