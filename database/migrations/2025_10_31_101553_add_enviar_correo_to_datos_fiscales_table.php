<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('datos_fiscales', function (Blueprint $table) {
            $table->boolean('enviar_correo')->nullable()->after('email_facturacion_text');
        });
    }

    public function down()
    {
        Schema::table('datos_fiscales', function (Blueprint $table) {
            $table->dropColumn('enviar_correo');
        });
    }
};