<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_pago', function (Blueprint $table) {

            $table->increments('id_detalle');

            $table->unsignedInteger('id_pago');
            
            $table->string('nro_recibo', 20)->nullable();

            $table->dateTime('fecha_pago');

            $table->string('monto', 45);
            $table->text('observacion')->nullable();

            $table->foreign('id_pago')
                  ->references('id_pago')
                  ->on('pagos')
                  ->onDelete('cascade'); 

            $table->boolean("estado")->default(true);

            // Campos para pagos online/transferencias
            $table->string('metodo_pago', 50)->nullable();
            $table->string('voucher_path')->nullable();
            $table->text('voucher_texto')->nullable();

            $table->boolean('validado_por_ia')->default(false);
            $table->decimal('porcentaje_confianza', 5, 2)->nullable();
            $table->text('razon_ia')->nullable();
            $table->enum('estado_validacion', ['pendiente', 'validado', 'rechazado'])->default('pendiente');

            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('detalle_pago');
    }
};
