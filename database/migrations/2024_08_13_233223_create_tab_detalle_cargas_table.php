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
        Schema::create('tab_detalle_cargas', function (Blueprint $table) {
            $table->id('id_carga');
            $table->string('cve_carga');
            $table->date('fecha_asignacion')->nullable();
            $table->date('fecha_inicio_conteo')->nullable();
            $table->date('fecha_fin_conteo')->nullable();
            $table->integer('conteo')->nullable();
            $table->string('nombre_archivo');
            $table->foreignId('id_usuario')->constrained('users'); 
            $table->integer('Reg_Archivo')->nullable();
            $table->integer('Reg_a_Contar')->nullable();
            $table->integer('reg_vobo')->nullable();
            $table->integer('reg_excluidos')->nullable();
            $table->integer('reg_incorpora')->nullable();
            $table->string('estatus')->nullable();
            $table->string('acciones')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tab_detalle_cargas');
    }
};
