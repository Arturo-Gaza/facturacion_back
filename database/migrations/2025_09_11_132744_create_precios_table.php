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
        Schema::create('precios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servicio_id')->constrained()->onDelete('cascade');
            $table->decimal('precio', 10, 2);
            $table->date('vigencia_desde');
            $table->date('vigencia_hasta')->nullable()->comment('NULL indica precio actual');
            $table->timestamps();
            
            $table->index(['vigencia_desde', 'vigencia_hasta']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('precios');
    }
};
