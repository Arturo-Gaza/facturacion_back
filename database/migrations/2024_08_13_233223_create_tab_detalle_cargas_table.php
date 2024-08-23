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
            $table->id();
            $table->string('cve_carga');
            $table->integer('conteo')->nullable();
            $table->string('nombre_archivo');
            $table->integer('Reg_Archivo')->nullable();
            $table->integer('Reg_a_Contar')->nullable();
            $table->integer('reg_vobo')->nullable();
            $table->integer('reg_excluidos')->nullable();
            $table->integer('reg_incorpora')->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('habilitado');
            $table->foreignId('id_estatus')->nullable()->constrained('cat_estatuses');
            $table->foreignId('id_usuario')->nullable()->constrained('users');
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
