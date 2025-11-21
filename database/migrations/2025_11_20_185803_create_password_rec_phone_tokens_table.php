<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        
        Schema::create('password_rec_phone_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->string('codigo');
            $table->boolean('used')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->timestamp('created_at')->nullable(); // porque no usas timestamps()
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_rec_phone_tokens');
    }
};
