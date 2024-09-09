<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = new User();

        $users->idRol = 1;
        $users->name = "Admin";
        $users->apellidoP = "Admin";
        $users->apellidoM = "Admin";
        $users->email = "Admin@gmail.com";
        $users->password = '$2y$12$bHqfPcMYy3GLmbxM5iF54eOTGofmXHHyzuSYgP4MCS1EWUF2wNQX6'; //P@ssword1
        $users->user = 'Admin';
        $users->habilitado = true;
        $users->intentos = 0;
        $users->login_activo = false;

        $users->save();
    }
}
