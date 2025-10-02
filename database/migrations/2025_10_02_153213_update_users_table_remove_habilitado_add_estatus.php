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
        //
        Schema::create('cat_estatus_usuario', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 20)->unique(); // 'activo', 'bloqueado', 'eliminado'
            $table->string('descripcion', 100);
            $table->boolean('habilitado')->default(true);
            $table->timestamps();
        });
        Schema::table('users', function (Blueprint $table) {
            // Eliminar el campo habilitado
            $table->dropColumn('habilitado');

            $table->unsignedBigInteger('id_estatus_usuario')
                ->default(1) // ← Aquí sí funciona
                ->after('idRol');

            // Agregar la foreign key por separado
            $table->foreign('id_estatus_usuario')
                ->references('id')
                ->on('cat_estatus_usuario')
                ->onDelete('restrict');

            // Agregar índice
            $table->index('id_estatus_usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('users', function (Blueprint $table) {
            // Revertir: eliminar el nuevo campo y restaurar el antiguo
            $table->dropForeign(['id_estatus_usuario']);
            $table->dropIndex(['id_estatus_usuario']);
            $table->dropColumn('id_estatus_usuario');

            // Restaurar el campo habilitado
            $table->boolean('habilitado')->default(true);
        });

        // Eliminar la tabla cat_estatus_usuario
        Schema::dropIfExists('cat_estatus_usuario');
    }
};
