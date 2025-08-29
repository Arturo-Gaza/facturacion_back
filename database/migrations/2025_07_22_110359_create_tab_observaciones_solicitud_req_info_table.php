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
        Schema::create('tab_observaciones_solicitud_req_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_solicitud')->constrained('tab_solicitudes');
            $table->foreignId('id_usuario')->constrained('users');
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tab_observaciones_solicitud_req_info');
    }
};
