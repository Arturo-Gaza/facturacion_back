<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cat_planes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->integer('numero_perfiles');
            $table->decimal('precio_mensual', 10, 2);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cat_planes');
    }
};