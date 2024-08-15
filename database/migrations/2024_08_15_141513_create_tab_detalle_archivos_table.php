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
            $table->integer('Libre_utilizacion')->nullable();
            $table->integer('En_control_calidad')->nullable();
            $table->integer('Bloqueado')->nullable();
            $table->integer('Valor_libre_util')->nullable();
            $table->integer('Valor_en_insp_cal')->nullable();
            $table->integer('Valor_stock_bloq')->nullable();
            $table->integer('Cantidad_total')->nullable();
            $table->integer('Importe_unitario')->nullable();
            $table->integer('Importe_total')->nullable();
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
