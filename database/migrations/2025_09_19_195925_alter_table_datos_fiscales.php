<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('datos_fiscales', function (Blueprint $table) {
            // Primero eliminar la constraint y columna existente
            $table->dropForeign(['id_regimen']);
            $table->dropColumn('id_regimen');
            
            // Agregar la nueva columna para el régimen predeterminado
            $table->foreignId('id_regimen_predeterminado')
                  ->nullable()
                  ->constrained('usuario_regimenes_fiscales')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('datos_fiscales', function (Blueprint $table) {
            // Revertir los cambios
            $table->dropForeign(['id_regimen_predeterminado']);
            $table->dropColumn('id_regimen_predeterminado');
            
            // Restaurar la columna original
            $table->foreignId('id_regimen')->constrained('cat_regimenes_fiscales', 'id_regimen');
        });
    }
};