<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->increments('id_pago');
            $table->unsignedInteger('id_deuda')->nullable();
            $table->unsignedInteger('id_orden')->nullable();
            $table->enum('tipo_pago', ['orden_completa', 'orden_parcial', 'deuda_individual'])
                  ->default('deuda_individual');
            $table->dateTime('fecha_pago');
            $table->decimal('monto', 10, 2);
            $table->text('observaciones')->nullable();
            $table->boolean("estado")->default(true);
            $table->timestamps();

            $table->foreign('id_deuda')
                ->references('id_deuda') 
                ->on('deudas')
                ->onDelete('cascade');

            $table->foreign('id_orden')
                ->references('id_orden')
                ->on('ordenes_pago')
                ->onDelete('cascade');

        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
