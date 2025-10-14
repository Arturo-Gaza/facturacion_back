<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cat_roles', function (Blueprint $table) {
            $table->id();
            $table->text('nombre');
            $table->timestamps();
            $table->boolean('habilitado');
            $table->boolean('recupera_gastos');
            $table->boolean('consola');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_roles');
    }
};
