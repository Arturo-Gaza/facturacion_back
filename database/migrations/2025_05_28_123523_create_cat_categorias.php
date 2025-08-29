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
        Schema::create('cat_categorias', function (Blueprint $table) {
            $table->id();
              $table->text('descripcion_categoria');
            $table->integer('id_tipo')->nullable()->constrained('cat_tipos');;
             $table->boolean('habilitado')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_categoria');
    }
};
