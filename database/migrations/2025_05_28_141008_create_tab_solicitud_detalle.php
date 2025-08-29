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
        Schema::create('tab_solicitud_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_producto')->nullable()->constrained('cat_productos');

            $table->foreignId('id_solicitud')->nullable()->constrained('tab_solicitudes');
            $table->text('descripcion');
            $table->text('marca')->nullable();
            $table->text('modelo')->nullable();
           // $table->string('nombre_cotizacion')->nullable();
            //$table->text('archivo_cotizacion')->nullable();
            $table->integer('cantidad');
            $table->boolean('cotizado')->nullable();
             $table->boolean('habilitado')->default(1);
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tab_solicitud_detalle');
    }
};
