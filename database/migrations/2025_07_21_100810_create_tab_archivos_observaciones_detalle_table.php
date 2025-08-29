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
        Schema::create('tab_archivos_observaciones_detalle', function (Blueprint $table) {
            $table->id();
             $table->foreignId('id_observacion_detalle')->constrained('tab_observaciones_solicitud_detalle');
            $table->text('nombre');
            $table->text('archivo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tab_archivos_observaciones_detalle');
    }
};
