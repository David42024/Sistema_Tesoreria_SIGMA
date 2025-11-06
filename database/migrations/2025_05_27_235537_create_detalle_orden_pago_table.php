<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_orden_pago', function (Blueprint $table) {
            $table->increments('id_detalle');

            $table->unsignedInteger('id_orden');
            $table->unsignedInteger('id_deuda')->nullable(); // Referencia a la deuda específica
            $table->unsignedInteger('id_concepto');
            $table->unsignedInteger('id_politica')->nullable();

            $table->decimal('monto_base', 10, 2);
            $table->decimal('monto_ajuste', 10, 2)->default(0);
            $table->decimal('monto_subtotal', 10, 2);

            $table->text('descripcion_ajuste')->nullable();

            $table->foreign('id_orden')
                  ->references('id_orden')
                  ->on('ordenes_pago')
                  ->onDelete('cascade');
            
            $table->foreign('id_deuda')
                  ->references('id_deuda')
                  ->on('deudas')
                  ->onDelete('cascade');

            $table->foreign('id_concepto')
                  ->references('id_concepto')
                  ->on('conceptos_pago')
                  ->onDelete('cascade');

            $table->foreign('id_politica')
                  ->references('id_politica')
                  ->on('politica')
                  ->onDelete('set null');

            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('detalle_orden_pago');
    }
};
