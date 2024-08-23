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
        Schema::create('tab_asignacions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_carga')->nullable()->constrained('tab_detalle_cargas');
            $table->foreignId('id_usuario')->nullable()->constrained('users');
            $table->integer('conteo')->nullable();
            $table->date('fecha_asignacion')->nullable();
            $table->date('fecha_inicio_conteo')->nullable();
            $table->date('fecha_fin_conteo')->nullable();
            $table->foreignId('id_estatus')->nullable()->constrained('cat_estatuses');
            $table->boolean('habilitado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tab_asignacions');
    }
};
