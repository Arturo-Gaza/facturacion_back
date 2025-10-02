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
        Schema::table('solicitudes', function (Blueprint $table) {
            // Si la llave primaria de cat_usos_cfdi es 'usoCFDI' y es string
            $table->string('usoCFDI', 10)->nullable();

            $table->index('usoCFDI');

            $table->foreign('usoCFDI')
                ->references('usoCFDI')  // Referencia la columna correcta
                ->on('cat_usos_cfdi')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropForeign(['usoCFDI']);
            $table->dropIndex(['usoCFDI']);
            $table->dropColumn('usoCFDI');
        });
    }
};
