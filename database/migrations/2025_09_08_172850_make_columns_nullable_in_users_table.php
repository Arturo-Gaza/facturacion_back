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
        Schema::table('users', function (Blueprint $table) {
            //
                        $table->text('apellidoP')->nullable()->change();
            $table->text('apellidoM')->nullable()->change();
            $table->text('password')->nullable()->change();
            $table->text('name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
                        $table->text('apellidoP')->nullable(false)->change();
            $table->text('apellidoM')->nullable(false)->change();
            $table->text('password')->nullable(false)->change();
            $table->text('name')->nullable(false)->change();
        });
    }
};
