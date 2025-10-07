<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('estatus_movimiento');
    }

    public function down()
    {
        // Recuperar la tabla eliminada (si necesitas rollback)
        Schema::create('estatus_movimiento', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });
    }
};