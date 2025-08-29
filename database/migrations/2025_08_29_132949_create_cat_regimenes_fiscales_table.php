<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_cat_regimenes_fiscales_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cat_regimenes_fiscales', function (Blueprint $table) {
            $table->id('id_regimen');
            $table->string('clave', 10);
            $table->string('descripcion', 250);
            $table->boolean('aplica_pf')->default(false);
            $table->boolean('aplica_pm')->default(false);
            $table->boolean('vigente')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cat_regimenes_fiscales');
    }
};