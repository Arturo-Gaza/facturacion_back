<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_direcciones_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('direcciones', function (Blueprint $table) {
            $table->id('id_direccion');
            $table->foreignId('id_cliente')->constrained('clientes', 'id_cliente');
            $table->foreignId('id_tipo_direccion')->constrained('cat_tipos_direccion', 'id_tipo_direccion');
            $table->string('calle', 250);
            $table->string('num_exterior', 50);
            $table->string('num_interior', 50)->nullable();
            $table->string('colonia', 150);
            $table->string('localidad', 150)->nullable();
            $table->string('municipio', 150);
            $table->string('estado', 150);
            $table->string('codigo_postal', 10);
            $table->string('pais', 100)->default('México');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('direcciones');
    }
};