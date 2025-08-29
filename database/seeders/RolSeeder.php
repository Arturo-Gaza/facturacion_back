<?php

namespace Database\Seeders;

use App\Models\Catalogos\CatRoles;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $Rol1 = new CatRoles();
        $Rol1->nombre = "Administrador General";
        $Rol1->habilitado = true;
        $Rol1->save();

        $Rol2 = new CatRoles();
        $Rol2->nombre = "Administrador Compras";
        $Rol2->habilitado = true;
        $Rol2->save();

        $Rol3 = new CatRoles();
        $Rol3->nombre = "Usuario Compras";
        $Rol3->habilitado = true;
        $Rol3->save();

        $Rol4 = new CatRoles();
        $Rol4->nombre = "Usuario General";
        $Rol4->habilitado = true;
        $Rol4->save();
    }
}
