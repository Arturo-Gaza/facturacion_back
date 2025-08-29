<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_clientes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id('id_cliente');
            $table->string('usuario', 150)->unique();
            $table->string('password');
            $table->string('email', 250)->unique();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes(); // Para eliminación lógica
        });
    }

    public function down()
    {
        Schema::dropIfExists('clientes');
    }
};