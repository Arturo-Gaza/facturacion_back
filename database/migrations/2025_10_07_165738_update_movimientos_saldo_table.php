<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Primero eliminar la foreign key existente
        Schema::table('movimientos_saldo', function (Blueprint $table) {
            $table->dropForeign(['tipo_movimiento_id']);
            $table->dropColumn('tipo_movimiento_id');
        });

        // Agregar el nuevo campo ENUM
        Schema::table('movimientos_saldo', function (Blueprint $table) {
            $table->enum('tipo', ['cargo', 'abono'])->after('monto');
        });

        // Opcional: Renombrar 'nuevo_monto' a 'saldo_resultante' si quieres
        Schema::table('movimientos_saldo', function (Blueprint $table) {
            $table->renameColumn('nuevo_monto', 'saldo_resultante');
        });
    }

    public function down()
    {
        Schema::table('movimientos_saldo', function (Blueprint $table) {
            $table->dropColumn('tipo');
            $table->renameColumn('saldo_resultante', 'nuevo_monto');
        });

        // Restaurar la relación anterior (necesitarías recrear la tabla estatus_movimiento)
        Schema::table('movimientos_saldo', function (Blueprint $table) {
            $table->foreignId('tipo_movimiento_id')->constrained('estatus_movimiento');
        });
    }
};
