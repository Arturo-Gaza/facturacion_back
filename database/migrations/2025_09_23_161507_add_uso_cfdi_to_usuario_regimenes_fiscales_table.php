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
        Schema::table('usuario_regimenes_fiscales', function (Blueprint $table) {
            // Agregar el nuevo campo
            $table->string('uso_cfdi', 10)->nullable()->after('id_regimen');
            
            // Agregar la clave foránea
            $table->foreign('uso_cfdi')
                  ->references('usoCFDI')
                  ->on('cat_usos_cfdi')
                  ->onDelete('set null');
            
            // Eliminar la restricción única existente
            $table->dropUnique(['id_usuario', 'id_regimen']);
            
            // Agregar nueva restricción única que incluya el nuevo campo
            $table->unique(['id_usuario', 'id_regimen', 'uso_cfdi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuario_regimenes_fiscales', function (Blueprint $table) {
            // Revertir los cambios en orden inverso
            
            // Eliminar la nueva restricción única
            $table->dropUnique(['id_usuario', 'id_regimen', 'uso_cfdi']);
            
            // Restaurar la restricción única original
            $table->unique(['id_usuario', 'id_regimen']);
            
            // Eliminar la clave foránea
            $table->dropForeign(['uso_cfdi']);
            
            // Eliminar el campo
            $table->dropColumn('uso_cfdi');
        });
    }
};