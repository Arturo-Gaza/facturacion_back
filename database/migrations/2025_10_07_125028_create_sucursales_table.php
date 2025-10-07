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
        Schema::create('sucursales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')
                ->constrained('datos_fiscales')
                ->onDelete('cascade');

            $table->foreignId('administrador_id')
                ->constrained('users')
                ->onDelete('restrict');
            $table->foreignId('datos_sucursal_id')
                ->constrained('datos_fiscales')
                ->onDelete('cascade');
            $table->timestamps();
            $table->boolean('habilitado')->default(true);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sucursales');
    }
};
