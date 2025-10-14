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
        $roles = [
            [
                'nombre' => 'Administrador General',
                'recupera_gastos' => true,
                'consola' => true
            ],
            [
                'nombre' => 'Usuario Cliente',
                'recupera_gastos' => true,
                'consola' => false
            ],
            [
                'nombre' => 'Usuario Colaborador',
                'recupera_gastos' => true,
                'consola' => false
            ],
            [
                'nombre' => 'Administrador de mesa de ayuda',
                'recupera_gastos' => false,
                'consola' => true
            ],
            [
                'nombre' => 'Mesa de ayuda',
                'recupera_gastos' => false,
                'consola' => true
            ]
        ];
        foreach ($roles as $rolData) {
            $rol = new CatRoles();
            $rol->nombre = $rolData['nombre'];
            $rol->habilitado = true;
            $rol->recupera_gastos = $rolData['recupera_gastos'];
            $rol->consola = $rolData['consola'];
            $rol->save();
        }
    }
}
