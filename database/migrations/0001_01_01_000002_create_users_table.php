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
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('usuario', 100)->unique()->nullable();
    $table->string('password')->nullable();
    $table->foreignId('idRol')->constrained('cat_roles');
    $table->boolean('login_activo')->default(false);
    $table->integer('intentos')->default(0);
    $table->boolean('habilitado')->default(true);
    
    // Estas columnas se crearán pero SIN la restricción foreign key inicialmente
    $table->unsignedBigInteger('id_mail_principal')->nullable();
    $table->unsignedBigInteger('id_telefono_principal')->nullable();
    $table->unsignedBigInteger('usuario_padre')->nullable();
    $table->unsignedBigInteger('datos_fiscales_principal')->nullable();
    
    $table->rememberToken();
    $table->timestamps();
    
    $table->string('google_id')->nullable()->unique();
    $table->string('avatar')->nullable();
    // Índices
    $table->index('usuario');
    $table->index('habilitado');
    $table->index('login_activo');
});

        Schema::create('password_reset_tokens', function (Blueprint $table) {
           $table->id();
            $table->string('email')->index();
            $table->string('codigo');
            $table->boolean('used')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
