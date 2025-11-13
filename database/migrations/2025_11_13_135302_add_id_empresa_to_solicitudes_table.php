<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdEmpresaToSolicitudesTable extends Migration
{
    public function up()
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            // usa unsignedBigInteger si la PK de cat_empresas es bigIncrements (id)
            $table->unsignedBigInteger('id_empresa')->nullable()->after('id')->index();

            // crea FK si quieres integridad referencial. Si tu DB no usa FK, omite la siguiente línea.
            $table->foreign('id_empresa')
                  ->references('id')
                  ->on('cat_empresas')
                  ->nullOnDelete()
                  ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            // drop FK primero (si la creaste)
            $table->dropForeign(['id_empresa']);
            $table->dropColumn('id_empresa');
        });
    }
}
