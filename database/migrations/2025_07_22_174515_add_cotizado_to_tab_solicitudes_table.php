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
        Schema::table('tab_solicitudes', function (Blueprint $table) {
            //
                    Schema::table('tab_solicitudes', function (Blueprint $table) {
            $table->boolean('cotizadoGB')->nullable()->default(null)->after('prioridadModificada');
        });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tab_solicitudes', function (Blueprint $table) {
            //
              Schema::table('tab_solicitudes', function (Blueprint $table) {
            $table->dropColumn('cotizado');
        });
        });
    }
};
