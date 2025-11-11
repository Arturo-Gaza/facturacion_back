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
        Schema::table('cat_planes', function (Blueprint $table) {
            $table->integer('dias_gratis')->default(0)->after('meses_vigencia');
            // puedes ajustar el "after" al campo que prefieras
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cat_planes', function (Blueprint $table) {
            $table->dropColumn('dias_gratis');
        });
    }
};
