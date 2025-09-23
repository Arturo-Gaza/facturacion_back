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
                        // Agregar campo texto_json (json o text para almacenar datos estructurados)
            $table->json('texto_json')->nullable()->after('texto_ocr');
            // Agregar campo establecimiento (string)
            $table->string('establecimiento', 255)->nullable()->after('texto_json');
            
            // Agregar campo monto (decimal para valores monetarios)
            $table->decimal('monto', 10, 2)->nullable()->after('establecimiento');
            

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropColumn(['establecimiento', 'monto', 'texto_json']);
        });
    }
};