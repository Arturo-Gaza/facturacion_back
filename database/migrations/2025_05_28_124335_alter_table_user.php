<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id_departamento')->nullable()->after('email'); // o el campo donde quieras insertarlo

            // Relación foránea (asegúrate que la tabla `departamentos` existe)
            $table->foreign('id_departamento')
                ->references('id')
                ->on('cat_departamentos')
                ->onDelete('set null'); // o 'cascade' si quieres eliminar en cascada
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_departamento']);
            $table->dropColumn('id_departamento');
        });
    }
};
