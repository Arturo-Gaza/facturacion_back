<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_clientes_fiscales_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('clientes_fiscales', function (Blueprint $table) {
            $table->id('id_fiscal');
            $table->foreignId('id_cliente')->constrained('clientes', 'id_cliente');
            $table->string('nombre_razon', 250);
            $table->string('nombre_comercial', 250)->nullable();
            $table->boolean('es_persona_moral')->default(false);
            $table->string('rfc', 13);
            $table->string('curp', 18)->nullable();
            $table->foreignId('id_regimen')->constrained('cat_regimenes_fiscales', 'id_regimen');
            $table->date('fecha_inicio_op');
            $table->foreignId('id_estatus_sat')->constrained('cat_estatuses_sat', 'id_estatus_sat');
            $table->json('datos_extra')->nullable();
            $table->timestamps();
            
            $table->unique('id_cliente'); // Relación 1 a 1
        });
    }

    public function down()
    {
        Schema::dropIfExists('clientes_fiscales');
    }
};