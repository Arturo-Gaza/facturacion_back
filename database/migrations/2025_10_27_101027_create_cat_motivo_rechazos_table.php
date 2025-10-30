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
        Schema::create('cat_motivo_rechazos', function (Blueprint $table) {
            $table->id();
            $table->text('descripcion')->nullable();
            $table->text('detalle')->nullable();
            $table->boolean('activo')->default(true);
            $table->boolean('validar_por_IA')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_motivo_rechazos');
    }
};
