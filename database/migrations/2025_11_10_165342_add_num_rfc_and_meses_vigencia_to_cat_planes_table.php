<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cat_planes', function (Blueprint $table) {
            $table->string('num_rfc')->nullable()->after('nombre'); // o ajusta la posición según tu estructura
            $table->integer('meses_vigencia')->default(0)->after('num_rfc');
        });
    }

    public function down(): void
    {
        Schema::table('cat_planes', function (Blueprint $table) {
            $table->dropColumn(['num_rfc', 'meses_vigencia']);
        });
    }
};
