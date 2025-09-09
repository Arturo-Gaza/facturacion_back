<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_cat_estatuses_sat_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cat_estatuses_sat', function (Blueprint $table) {
            $table->id('id_estatus_sat');
            $table->string('clave', 50);
            $table->string('descripcion', 250);
            $table->boolean('habilitado')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cat_estatuses_sat');
    }
};