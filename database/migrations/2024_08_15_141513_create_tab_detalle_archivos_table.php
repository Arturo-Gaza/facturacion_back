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
        Schema::create('tab_detalle_archivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_carga_cab')->constrained('tab_detalle_cargas');
            $table->foreignId('id_almacen')->constrained('cat_almacenes');
            $table->foreignId('id_cat_prod')->constrained('cat_productos');
            $table->foreignId('id_unid_med')->constrained('cat_unidad_medidas');
            $table->foreignId('id_gpo_familia')->constrained('cat_gpo_familias');
            $table->string('libre_utilizacion', 15);
            $table->string('en_control_calidad', 15);
            $table->string('bloqueado', 15);
            $table->string('valor_libre_util', 15);
            $table->string('valor_insp_cal', 15);
            $table->string('valor_stock_bloq', 15);
            $table->string('cantidad_total', 15);
            $table->string('importe_unitario', 15);
            $table->string('importe_total', 15);
            $table->boolean('habilitado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tab_detalle_archivos');
    }
};
