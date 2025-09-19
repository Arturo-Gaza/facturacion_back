<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

        public function up()
    {
        Schema::table('datos_fiscales', function (Blueprint $table) {
            $table->string('primer_apellido', 100)->nullable()->after('nombre_razon');
            $table->string('segundo_apellido', 100)->nullable()->after('primer_apellido');
        });
    }

    public function down()
    {
        Schema::table('datos_fiscales', function (Blueprint $table) {
            $table->dropColumn(['primer_apellido', 'segundo_apellido']);
        });
    }
};
