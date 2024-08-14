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
        Schema::create('cat_productos', function (Blueprint $table) {
            $table->id();
            $table->string('clave_producto');
            $table->string('descripcion_producto_material');
            $table->foreignId('id_cat_almacenes')->constrained('cat_almacenes');
            $table->foreignId('id_unidad_medida')->constrained('cat_unidad_medidas');
            $table->foreignId('id_gpo_familia')->constrained('cat_gpo_familias');
            $table->timestamps();
            $table->boolean('habilitado');

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_productos');
    }
};
