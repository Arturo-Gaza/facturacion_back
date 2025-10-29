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
        Schema::table('cat_estatus_solicitud', function (Blueprint $table) {
            //
            $table->string('descripcion_cliente')->nullable()->after('descripcion_estatus_solicitud');
            $table->string('color', 20)->nullable()->after('mandarCorreo');
            $table->string('color_cliente', 20)->nullable()->after('color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cat_estatus_solicitud', function (Blueprint $table) {
            //
            Schema::table('cat_estatus_solicitud', function (Blueprint $table) {
                $table->dropColumn(['descripcion_cliente', 'color', 'color_cliente']);
            });
        });
    }
};
