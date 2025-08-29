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
        Schema::create('tab_solicitudes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario_solicitud')->nullable()->constrained('users');
            $table->text('descripcion');
            $table->text('justificacion');
            $table->integer('prioridad');
            $table->foreignId('id_estatus_solicitud')->nullable()->default(1)->constrained('cat_estatus_solicitud');
            $table->foreignId('id_categoria')->nullable()->constrained('cat_categorias');
            $table->foreignId('id_usuario_asignacion')->nullable()->constrained('users');
            $table->text('justificacion_prioridad')->nullable();
            $table->boolean('prioridadModificada')->default(false)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tab_solicitud');
    }
};
