<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tab_bitacora_solicitud', function (Blueprint $table) {
            $table->string('pdf_url_anterior')->nullable()->after('id_usuario');
            $table->string('xml_url_anterior')->nullable()->after('pdf_url_anterior');
        });
    }

    
    public function down(): void
    {
        Schema::table('tab_bitacora_solicitud', function (Blueprint $table) {
            $table->dropColumn(['pdf_url_anterior', 'xml_url_anterior']);
        });
    }
};
