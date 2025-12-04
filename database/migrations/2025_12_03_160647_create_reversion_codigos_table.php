<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reversion_tokens', function (Blueprint $table) {
            $table->id();

            // Vinculado a una solicitud
            $table->unsignedBigInteger('reversion_id')->index();
            $table->foreign('reversion_id')
                  ->references('id')
                  ->on('reversion_solicitudes')
                  ->onDelete('cascade');

            // Hash del token (no guardar el token plano)
            $table->string('token', 256);

            // Datos de control
            $table->timestamp('created_at')->nullable();
            $table->foreignId('created_por_admin')->constrained('users')->nullable();

            // Anti-abuso
            $table->boolean('used')->default(false);
            $table->timestamp('used_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reversion_tokens');
    }
};
