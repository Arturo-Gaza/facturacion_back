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
        Schema::create('tab_conteo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_carga')->constrained('tab_detalle_cargas');
            $table->foreignId('id_usuario')->nullable()->constrained('users');
            $table->foreignId('id_producto')->nullable()->constrained('cat_productos');
            $table->string('codigo');
            $table->string('descripcion');
            $table->string('ume');
            $table->integer('cantidad');
            $table->string('ubicacion');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tab_conteo');
    }
};
