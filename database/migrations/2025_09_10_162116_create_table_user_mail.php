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
Schema::create('user_emails', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->string('email')->unique();
    $table->boolean('verificado')->default(false);
    $table->timestamps();
});

        Schema::create('password_confirm_mail_tokens', function (Blueprint $table) {
           $table->id();
            $table->string('email')->index();
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
        Schema::dropIfExists('user_emails');
         Schema::dropIfExists('password_confirm_mail_tokens');
    }
};
