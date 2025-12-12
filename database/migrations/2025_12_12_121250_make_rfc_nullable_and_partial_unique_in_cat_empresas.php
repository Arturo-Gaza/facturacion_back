<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cat_empresas', function (Blueprint $table) {
            $table->string('rfc', 20)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('cat_empresas', function (Blueprint $table) {
            $table->string('rfc', 20)->nullable(false)->change();
        });
    }
};
