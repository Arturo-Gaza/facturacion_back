<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->unsignedBigInteger('id_receptor')->nullable();
            
            // Agregar índice
            $table->index('id_receptor');
            
            // Foreign key
            $table->foreign('id_receptor')
                  ->references('id')
                  ->on('datos_fiscales')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropForeign(['id_receptor']);
            $table->dropIndex(['id_receptor']);
            $table->dropColumn('id_receptor');
        });
    }
};
