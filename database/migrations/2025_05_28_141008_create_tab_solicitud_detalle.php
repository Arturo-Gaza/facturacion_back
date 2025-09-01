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
  
            $table->foreignId('id_solicitud')->nullable()->constrained('tab_solicitudes');
            $table->text('descripcion');
 
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
