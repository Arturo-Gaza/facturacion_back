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
         
        Schema::create('movimientos_saldo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->decimal('monto', 10, 2)->comment('Monto negativo para cobro, positivo para pago');
            $table->foreignId('estatus_movimiento_id')->constrained('cat_estatus_movimiento')->onDelete('cascade');
            $table->decimal('nuevo_monto', 10, 2);
            $table->string('descripcion', 255)->nullable();
            $table->timestamps();
        });
         
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_saldo');
    }
};
