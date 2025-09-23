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
        Schema::create('regimen_uso_cfdi', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('id_regimen'); // Cambia esto
        $table->string('usoCFDI');
        $table->timestamps();

        // Referencia al id_regimen en lugar de la clave
        $table->foreign('id_regimen')
              ->references('id_regimen')
              ->on('cat_regimenes_fiscales')
              ->onDelete('cascade');

        $table->foreign('usoCFDI')
              ->references('usoCFDI')
              ->on('cat_usos_cfdi')
              ->onDelete('cascade');

        $table->unique(['id_regimen', 'usoCFDI']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regimen_uso_cfdi_pivot');
    }
};
