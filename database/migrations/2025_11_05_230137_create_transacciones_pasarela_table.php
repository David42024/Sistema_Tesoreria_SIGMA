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
        Schema::create('transacciones_pasarela', function (Blueprint $table) {
            $table->id('id_transaccion');

            // Relación con orden de pago
            $table->unsignedInteger('id_orden');

            // Método de pago utilizado
            $table->enum('metodo_pago', ['yape', 'plin', 'transferencia', 'tarjeta', 'paypal'])
                  ->comment('Método de pago utilizado');

            // Número de operación generado por la pasarela
            $table->string('numero_operacion', 20)
                  ->unique()
                  ->comment('Número de operación de la transacción');

            // Fecha y hora de la transacción
            $table->dateTime('fecha_transaccion');

            // Monto de la transacción
            $table->decimal('monto', 10, 2)
                  ->comment('Monto pagado por el usuario');

            // Datos adicionales del método de pago (JSON)
            $table->json('datos_adicionales')
                  ->nullable()
                  ->comment('Datos adicionales: celular, banco, tarjeta, email, IP, etc.');

            // Estado de la transacción
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado', 'cancelado'])
                  ->default('pendiente')
                  ->comment('Estado de validación de la transacción');

            // Voucher generado por el sistema
            $table->string('voucher_path', 255)
                  ->nullable()
                  ->comment('Ruta del voucher generado');

            // Usuario que validó (si aplica)
            $table->unsignedBigInteger('validado_por')
                  ->nullable()
                  ->comment('ID del usuario que validó la transacción');

            // Fecha de validación
            $table->dateTime('fecha_validacion')
                  ->nullable();

            // ID del pago generado (una vez aprobado)
            $table->unsignedInteger('id_pago_generado')
                  ->nullable()
                  ->comment('ID del registro en tabla pagos una vez aprobado');

            // Observaciones
            $table->text('observaciones')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('id_orden')
                  ->references('id_orden')
                  ->on('ordenes_pago')
                  ->onDelete('cascade');

            // Nota: la tabla `users` define la PK como `id_usuario`.
            $table->foreign('validado_por')
                  ->references('id_usuario')
                  ->on('users')
                  ->onDelete('set null');

            $table->foreign('id_pago_generado')
                  ->references('id_pago')
                  ->on('pagos')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacciones_pasarela');
    }
};
