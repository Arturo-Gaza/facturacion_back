<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movimientos_saldo', function (Blueprint $table) {
            $table->unsignedBigInteger('id_solicitud')->nullable()->after('id');
            $table->foreign('id_solicitud')->references('id')->on('solicitudes')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('movimiento_saldo', function (Blueprint $table) {
            $table->dropForeign(['id_solicitud']);
            $table->dropColumn('id_solicitud');
        });
    }
};
