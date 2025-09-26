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
        Schema::create('datos_fiscales_regimen_usos_cfdi', function (Blueprint $table) {
            $table->id();

            // Relación con datos_fiscales_regimenes_fiscales
            $table->foreignId('id_dato_fiscal_regimen')
                ->constrained('datos_fiscales_regimenes_fiscales')
                ->onDelete('cascade');

            // Relación con cat_usos_cfdi
            $table->string('uso_cfdi', 10);
            $table->foreign('uso_cfdi')
                ->references('usoCFDI')
                ->on('cat_usos_cfdi')
                ->onDelete('restrict');

            // Campo para marcar el uso CFDI predeterminado
            $table->boolean('predeterminado')->default(false);

            $table->timestamps();

     });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datos_fiscales_regimen_usos_cfdi');
    }
};
