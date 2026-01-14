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
        Schema::table('solicitudes_prematricula', function (Blueprint $table) {
            $table->char('escala', 1)->nullable()->after('id_grado')->comment('Escala de pago: A, B, C, D, E');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitudes_prematricula', function (Blueprint $table) {
            $table->dropColumn('escala');
        });
    }
};
