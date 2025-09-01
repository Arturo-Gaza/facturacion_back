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
        Schema::create('tab_facturas_solicitudes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_solicitud')->constrained('tab_solicitudes');
            $table->foreignId('id_usuario')->constrained('users');
            $table->string('nombre_facturas');
            $table->text('archivo_facturas');
            $table->boolean('recomendada')->nullable();
            $table->text('justificacion_general')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tab_facturas_solicitudes');
    }
};
