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
        Schema::table('solicitudes', function (Blueprint $table) {
            // Si la llave primaria de cat_regimenes_fiscales es 'id_regimen' y es bigInteger
            $table->unsignedBigInteger('id_regimen')->nullable();
            
            $table->index('id_regimen');
            
            $table->foreign('id_regimen')
                  ->references('id_regimen')  // Referencia la columna correcta
                  ->on('cat_regimenes_fiscales')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropForeign(['id_regimen']);
            $table->dropIndex(['id_regimen']);
            $table->dropColumn('id_regimen');
        });
    }
};