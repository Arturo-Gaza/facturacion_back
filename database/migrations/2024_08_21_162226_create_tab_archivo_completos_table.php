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
            $table->decimal('libre_utilizacion', 10, 2);
            $table->decimal('en_control_calidad', 10, 2);
            $table->decimal('bloqueado', 10, 2);
            $table->decimal('valor_libre_util', 10, 2);
            $table->decimal('valor_insp_cal', 10, 2);
            $table->decimal('valor_stock_bloq', 10, 2);
            $table->decimal('cantidad_total', 10, 2);
            $table->decimal('importe_unitario', 10, 2);
            $table->decimal('importe_total', 10, 2);
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
