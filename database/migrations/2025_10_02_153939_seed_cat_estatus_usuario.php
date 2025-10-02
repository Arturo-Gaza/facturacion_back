<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insertar los estatus por defecto
        DB::table('cat_estatus_usuario')->insert([
            [
                'clave' => 'activo',
                'descripcion' => 'Usuario activo y habilitado',
                'habilitado' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'clave' => 'bloqueado', 
                'descripcion' => 'Usuario bloqueado temporalmente',
                'habilitado' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'clave' => 'eliminado',
                'descripcion' => 'Usuario eliminado del sistema',
                'habilitado' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Actualizar los usuarios existentes para que tengan estatus 'activo'
        DB::table('users')->update([
            'id_estatus_usuario' => 1 // ID del estatus 'activo'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar los datos insertados
        DB::table('cat_estatus_usuario')->whereIn('clave', ['activo', 'bloqueado', 'eliminado'])->delete();
        
        // Restaurar los usuarios a estado por defecto
        DB::table('users')->update(['id_estatus_usuario' => null]);
    }
};