<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {

        // Agregar el nuevo campo ENUM
        Schema::table('movimientos_saldo', function (Blueprint $table) {
            $table->enum('tipo', ['cargo', 'abono','suscripción'])->after('monto');
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


    }
};
