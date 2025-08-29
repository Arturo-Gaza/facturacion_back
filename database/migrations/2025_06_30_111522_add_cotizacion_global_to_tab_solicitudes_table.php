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
              $table->boolean('cotizacion_global')->default(false)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tab_solicitudes', function (Blueprint $table) {
            $table->dropColumn('cotizacion_global');
        });
    }
};
