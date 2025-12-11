<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reversion_solicitudes', function (Blueprint $table) {
            $table->id();
            // Quién solicita (mesa de ayuda)
            $table->unsignedBigInteger('id_emplado')->constrained('users');

            // Qué acción quieren revertir (puede ser ID de otra tabla)
            $table->foreignId('id_solicitud')->constrained('solicitudes');

            // Estado general de la solicitud
            $table->enum('estado', [
                'PENDIENTE',
                'TOKEN_GENERADO',
                'EJECUTADO',
                'RECHAZADO'
            ])->default('PENDIENTE');

            // Auditoría
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reversion_solicitudes');
    }
};
