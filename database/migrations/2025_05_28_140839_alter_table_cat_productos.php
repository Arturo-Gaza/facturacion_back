<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
    {
        Schema::table('cat_productos', function (Blueprint $table) {
            $table->unsignedBigInteger('id_categoria')->nullable(); // o el campo donde quieras insertarlo

            // Relación foránea (asegúrate que la tabla `departamentos` existe)
            $table->foreign('id_categoria')
                  ->references('id')
                  ->on('cat_categorias')
                  ->onDelete('set null'); // o 'cascade' si quieres eliminar en cascada
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_categoria']);
            $table->dropColumn('id_categoria');
        });
    }
};
