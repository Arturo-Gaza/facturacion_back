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
            $table->boolean('aplica_persona_fisica')->default(false);
            $table->boolean('aplica_persona_moral')->default(false);
            $table->date('fecha_inicio_vigencia');
            $table->date('fecha_fin_vigencia')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cat_regimenes_fiscales');
    }
};
