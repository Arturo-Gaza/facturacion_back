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
        Schema::create('tab_archivo_completos', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('id_detalle_carga')->constrained('tab_detalle_cargas');
            $table->string('almacen');
            $table->string('material');
            $table->string('texto_breve_material');
            $table->string('ume');
            $table->string('grupo_articulos');
            $table->string('libre_utilizacion', 15);
            $table->string('en_control_calidad', 15);
            $table->string('bloqueado', 15);
            $table->string('valor_libre_util', 15);
            $table->string('valor_insp_cal', 15);
            $table->string('valor_stock_bloq', 15);
            $table->string('cantidad_total', 15);
            $table->string('importe_unitario', 15);
            $table->string('importe_total', 15);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tab_archivo_completos');
    }
};
