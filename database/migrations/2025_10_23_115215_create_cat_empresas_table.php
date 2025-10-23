<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cat_empresas', function (Blueprint $table) {
            $table->id();
            $table->string('rfc', 20)->unique();
            $table->string('nombre_empresa', 255);
            $table->string('pagina_web', 255)->nullable();
            $table->foreignId('id_giro')->constrained('cat_giros');
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            // Índices
            $table->index('rfc');
            $table->index('nombre_empresa');
            $table->index('id_giro');
            $table->index('activo');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cat_empresas');
    }
};