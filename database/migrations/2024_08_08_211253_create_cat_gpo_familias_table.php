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
            $table->text('clave_gpo_familia');
            $table->text('descripcion_gpo_familia');
            $table->text('descripcion_gpo_familia_2');
            $table->timestamps();
            $table->boolean('habilitado')->default(1);
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
