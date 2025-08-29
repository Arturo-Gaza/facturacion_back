<?php

namespace Database\Seeders;

use App\Models\Catalogos\CatUnidadMedida;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class UnidadMedidaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = new CatUnidadMedida();
        $users->clave_unidad_medida = "SER";
        $users->descripcion_unidad_medida = "SERVICIO";
        $users->save();
        $users = new CatUnidadMedida();
        $users->clave_unidad_medida = "UN";
        $users->descripcion_unidad_medida = "UNIDAD";
        $users->save();
        $users = new CatUnidadMedida();
        $users->clave_unidad_medida = "G";
        $users->descripcion_unidad_medida = "GRAMO";
        $users->save();
        $users = new CatUnidadMedida();
        $users->clave_unidad_medida = "KG";
        $users->descripcion_unidad_medida = "KILOGRAMO";
        $users->save();
        $users = new CatUnidadMedida();
        $users->clave_unidad_medida = "L";
        $users->descripcion_unidad_medida = "LITRO";
        $users->save();
        $users = new CatUnidadMedida();
        $users->clave_unidad_medida = "ML";
        $users->descripcion_unidad_medida = "MILILITRO";
        $users->save();
        $users = new CatUnidadMedida();
        $users->clave_unidad_medida = "M";
        $users->descripcion_unidad_medida = "METRO";
        $users->save();
        $users = new CatUnidadMedida();
        $users->clave_unidad_medida = "CM";
        $users->descripcion_unidad_medida = "CENTÍMETRO";
        $users->save();
        $users = new CatUnidadMedida();
        $users->clave_unidad_medida = "MM";
        $users->descripcion_unidad_medida = "MILÍMETRO";
        $users->save();
        $users = new CatUnidadMedida();
        $users->clave_unidad_medida = "PI";
        $users->descripcion_unidad_medida = "PIEZA";
        $users->save();
        $users = new CatUnidadMedida();
        $users->clave_unidad_medida = "CJ";
        $users->descripcion_unidad_medida = "CAJA";
        $users->save();
        $users = new CatUnidadMedida();
        $users->clave_unidad_medida = "LT";
        $users->descripcion_unidad_medida = "LATA";
        $users->save();
        $users = new CatUnidadMedida();
        $users->clave_unidad_medida = "MT2";
        $users->descripcion_unidad_medida = "METRO CUADRADO";
        $users->save();
        $users = new CatUnidadMedida();
        $users->clave_unidad_medida = "MT3";
        $users->descripcion_unidad_medida = "METRO CÚBICO";
        $users->save();
        $users = new CatUnidadMedida();
        $users->clave_unidad_medida = "BID";
        $users->descripcion_unidad_medida = "BIDÓN"; 
        $users->save();
        $users = new CatUnidadMedida();
        $users->clave_unidad_medida = "BOL";
        $users->descripcion_unidad_medida = "BOLSA";
        $users->save();
        $users = new CatUnidadMedida();
        $users->clave_unidad_medida = "GLN";
        $users->descripcion_unidad_medida = "GALÓN";
        $users->save();
        $users = new CatUnidadMedida();
        $users->clave_unidad_medida = "PAA";
        $users->descripcion_unidad_medida = "PAQUETE";
        $users->save();
        $users = new CatUnidadMedida();
        $users->clave_unidad_medida = "ROL";
        $users->descripcion_unidad_medida = "ROLLO";
        $users->save();
        $users = new CatUnidadMedida();
        $users->clave_unidad_medida = "TS";
        $users->descripcion_unidad_medida = "TONELADA";
        $users->save();
    }
}
