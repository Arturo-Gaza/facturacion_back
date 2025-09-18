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
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->string('imagen_url', 255);
            $table->text('texto_ocr')->nullable()->comment('Texto extraído por OCR');
            $table->foreignId('empleado_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('estado_id')->constrained('cat_estatus_solicitud')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
