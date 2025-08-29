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
        Schema::create('cat_unidad_medidas', function (Blueprint $table) {
            $table->id();
            $table->text('clave_unidad_medida');
            $table->text('descripcion_unidad_medida');
            $table->timestamps();
            $table->boolean('habilitado')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_unidad_medidas');
    }
};
