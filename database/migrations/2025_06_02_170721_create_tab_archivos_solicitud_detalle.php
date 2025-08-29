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
        Schema::create('tab_archivos_solicitud_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_solicitud_detalle')->constrained('tab_solicitud_detalle');
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
        Schema::dropIfExists('tab_archivos_solicitud_detalle');
    }
};
