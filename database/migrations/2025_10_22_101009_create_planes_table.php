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
        Schema::create('cat_planes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_plan');
            $table->enum('tipo_plan', ['personal', 'empresarial'])->default('personal');
            $table->enum('tipo_pago', ['prepago', 'postpago'])->default('prepago');
            $table->integer('num_usuarios')->nullable();
             $table->integer('num_facturas')->nullable();
            $table->date('vigencia_inicio');
            $table->date('vigencia_fin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planes');
    }
};
