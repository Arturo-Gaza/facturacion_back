<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cat_estatus_solicitud', function (Blueprint $table) {
            $table->unsignedBigInteger('id_cliente')
                ->nullable()
                ->after('id'); // lo pones donde prefieras
        });
    }

    public function down()
    {
        Schema::table('cat_estatus_solicitud', function (Blueprint $table) {
            $table->dropColumn('id_cliente');
        });
    }
};
