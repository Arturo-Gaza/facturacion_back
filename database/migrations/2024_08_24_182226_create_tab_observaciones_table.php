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
        Schema::create('tab_observaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_detalle_carga')->constrained('tab_detalle_cargas');
            $table->foreignId('id_usuario')->constrained('users');
            $table->text('observacion')->nullable();
            $table->boolean('habilitado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tab_observaciones');
    }
};
