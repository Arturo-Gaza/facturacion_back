<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cat_departamentos', function (Blueprint $table) {
            $table->id();
            $table->text('descripcion');
            $table->unsignedBigInteger('id_usuario_responsable_compras')->nullable(); // importante que sea nullable al principio
            $table->boolean('habilitado')->default(1);
            $table->timestamps();
        });

        DB::statement("
        ALTER TABLE cat_departamentos
        ADD CONSTRAINT cat_departamentos_id_usuario_responsable_compras_foreign
        FOREIGN KEY (id_usuario_responsable_compras)
        REFERENCES users(id)
        DEFERRABLE INITIALLY DEFERRED
    ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_departamentos');
    }
};
