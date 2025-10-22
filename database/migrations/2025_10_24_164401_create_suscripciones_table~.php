<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suscripciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_plan')->constrained('cat_planes')->onDelete('cascade');
            $table->date('fecha_inicio');
            $table->date('fecha_vencimiento')->nullable();
            $table->enum('estado', ['pendiente','activa', 'vencida', 'cancelada'])->default('pendiente');
            $table->integer('perfiles_utilizados')->default(0);
            $table->integer('facturas_realizadas')->default(0);
$table->string('stripe_session_id')->nullable()->unique();
            $table->string('stripe_payment_intent_id')->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suscripciones');
    }
};
