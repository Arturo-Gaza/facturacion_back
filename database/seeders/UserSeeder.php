<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\UserEmail;
use Illuminate\Support\Facades\DB;
use App\Models\SistemaTickets\CatDepartamentos;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {  DB::transaction(function () {
        // Primero crear el usuario
        $user = User::create([
    'idRol' => 1,
    'password' => '$2y$12$bHqfPcMYy3GLmbxM5iF54eOTGofmXHHyzuSYgP4MCS1EWUF2wNQX6',
    'habilitado' => true,
    'intentos' => 0,
    'login_activo' => false,
    'id_mail_principal' => null,
        ]);

        // Luego crear el email con el user_id correcto
        $emailAdmin = UserEmail::create([
            'user_id' => $user->id,
            'email' => 'admin@hopewellsystem.com',
            'verificado' => true,
        ]);

        // Finalmente actualizar el usuario con el id_mail_principal
        $user->id_mail_principal = $emailAdmin->id;
        $user->save();
    });
    }
}
