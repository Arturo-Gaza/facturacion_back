<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usuario_regimenes_fiscales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_regimen')->constrained('cat_regimenes_fiscales', 'id_regimen');
            $table->boolean('predeterminado')->default(false);
            $table->timestamps();
            
            // Para evitar duplicados
            $table->unique(['id_usuario', 'id_regimen']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuario_regimenes_fiscales');
    }
};