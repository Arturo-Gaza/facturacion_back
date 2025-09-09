<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_contactos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contactos', function (Blueprint $table) {
            $table->id('id_contacto');
            $table->foreignId('id_cliente')->constrained('clientes', 'id_cliente');
            $table->foreignId('id_tipo_contacto')->constrained('cat_tipos_contacto', 'id_tipo_contacto');
            $table->string('lada',5);
            $table->string('valor', 250);
            $table->boolean('principal')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contactos');
    }
};