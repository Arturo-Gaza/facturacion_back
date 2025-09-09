<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_cat_tipos_direccion_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cat_tipos_direccion', function (Blueprint $table) {
            $table->id('id_tipo_direccion');
            $table->string('clave', 50);
            $table->string('descripcion', 250);
            $table->boolean('habilitado')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cat_tipos_direccion');
    }
};