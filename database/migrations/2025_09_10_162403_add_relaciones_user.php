<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // Agregar restricciones foráneas después de crear todas las tablas
        $table->foreign('id_mail_principal')->references('id')->on('user_emails');
        $table->foreign('id_telefono_principal')->references('id')->on('user_phones');
        $table->foreign('usuario_padre')->references('id')->on('users');
        $table->foreign('datos_fiscales_principal')->references('id')->on('datos_fiscales');
    });
      Schema::table('datos_fiscales', function (Blueprint $table) {
        $table->foreign('email_facturacion_id')
              ->references('id')
              ->on('user_emails')
              ->onDelete('set null');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['id_mail_principal']);
        $table->dropForeign(['id_telefono_principal']);
        $table->dropForeign(['usuario_padre']);
        $table->dropForeign(['datos_fiscales_principal']);
    });
       Schema::table('datos_fiscales', function (Blueprint $table) {
        $table->dropForeign(['email_facturacion_id']);
    });
}
};
