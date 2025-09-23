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
        Schema::create('cat_usos_cfdi', function (Blueprint $table) {
       
        $table->string('usoCFDI', 10)->primary();
        $table->string('descripcion');
        $table->boolean('aplica_persona_fisica')->default(false);
        $table->boolean('aplica_persona_moral')->default(false);
        $table->date('fecha_inicio_vigencia');
        $table->date('fecha_fin_vigencia')->nullable();
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_usos_cfdi');
    }
};
