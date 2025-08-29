<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
    {
        Schema::create('tab_departamentos_categorias', function (Blueprint $table) {
            $table->unsignedBigInteger('id_departamento');
            $table->unsignedBigInteger('id_categoria');

            // Llave primaria compuesta
            $table->primary(['id_departamento', 'id_categoria']);

            // Claves foráneas
            $table->foreign('id_departamento')
                  ->references('id')
                  ->on('cat_departamentos')
                  ->onDelete('cascade');

            $table->foreign('id_categoria')
                  ->references('id')
                  ->on('cat_categorias')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tab_departamentos_categorias');
    }
};
