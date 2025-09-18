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
Schema::create('user_phones', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->string('telefono');
    $table->boolean('verificado')->default(false);
    $table->timestamps();
});
        Schema::create('password_confirm_phone_tokens', function (Blueprint $table) {
           $table->id();
            $table->string('phone')->index();
            $table->string('codigo');
            $table->boolean('used')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_phone');
        Schema::dropIfExists('password_confirm_phone_tokens');
    }
};
