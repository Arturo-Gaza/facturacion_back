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
        Schema::create('datos_fiscales_regimenes_fiscales', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_inicio_regimen')->nullable();
            // Relación con datos_fiscales
            $table->foreignId('id_dato_fiscal')
                ->constrained('datos_fiscales')
                ->onDelete('cascade');

            // Relación con cat_regimenes_fiscales
            $table->unsignedBigInteger('id_regimen');

            // ✅ LUEGO crear la llave foránea
            $table->foreign('id_regimen')
                ->references('id_regimen')
                ->on('cat_regimenes_fiscales')
                ->onDelete('cascade');

            // Campo para marcar el régimen predeterminado
            $table->boolean('predeterminado')->default(false);

            $table->timestamps();

            // Asegurar que solo haya un predeterminado por dato_fiscal
            $table->unique(['id_dato_fiscal', 'predeterminado']);

            // Evitar duplicados de régimen por dato fiscal
            $table->unique(['id_dato_fiscal', 'id_regimen']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_datos_fiscales_regimenes_fiscales');
    }
};
