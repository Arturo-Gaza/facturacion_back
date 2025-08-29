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
            $table->boolean('mandarCorreo')->default(false)->after('descripcion_estatus_solicitud');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cat_estatus_solicitud', function (Blueprint $table) {
            $table->dropColumn('mandarCorreo');
        });
    }
};
