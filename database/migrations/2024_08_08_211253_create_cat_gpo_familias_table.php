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
        Schema::create('cat_gpo_familias', function (Blueprint $table) {
            $table->id();
            $table->string('clave_gpo_familia');
            $table->string('descripcion_gpo_familia');
            $table->timestamps();
            $table->boolean('habilitado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_gpo_familias');
    }
};
