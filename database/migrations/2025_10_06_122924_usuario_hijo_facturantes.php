<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usuario_hijo_facturantes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario_hijo');
            $table->unsignedBigInteger('id_dato_fiscal');
            $table->boolean('predeterminado')->default(false);
            $table->timestamps();

            $table->foreign('id_usuario_hijo')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_dato_fiscal')->references('id')->on('datos_fiscales')->onDelete('cascade');
            
            $table->unique(['id_usuario_hijo', 'id_dato_fiscal']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuario_hijo_facturantes');
    }
};