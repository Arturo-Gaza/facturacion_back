<?php

namespace Database\Seeders;

use App\Models\CatPlanes;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        // Plan Básico Personal Prepago
        CatPlanes::create([
            'nombre_plan' => 'Check in',
            'descripcion_plan' => "Comienza tu viaje sin complicaciones. Automatiza la Recuperación de tus Gastos de viajes en minutos y olvídate de perseguir facturas.",
            'tipo_plan' => 'personal',
            'tipo_pago' => 'prepago',
            'num_usuarios' => 1,
            'precio' => 0.00,
            'vigencia_inicio' => now(),
            'vigencia_fin' => null, // Sin fecha de fin (vigente indefinidamente)
        ]);

        // Plan Empresarial Prepago
        CatPlanes::create([
            'nombre_plan' => 'Vuelo',
            'descripcion_plan' => "Tu asistente de Recuperación de gastos inteligente. Ideal para profesionales que viajan frecuentemente y necesitan control total de sus comprobaciones.",
            'tipo_plan' => 'personal',
            'tipo_pago' => 'prepago',
            'precio' => 0.00,
            'vigencia_inicio' => now(),
            'vigencia_fin' => null, // Vigencia de 1 año
        ]);

        // 3 Planes Empresariales Mensuales (Postpago)
        CatPlanes::create([
            'nombre_plan' => 'Primera Clase',
            'descripcion_plan' => "La decisión empresarial que Genera. Automatiza la Recupera Gastos de todo tu equipo y deduce el 95% de tus Gastos de viáticos",
            'tipo_plan' => 'empresarial',
            'tipo_pago' => 'prepago',
            'precio' => 0,
            'vigencia_inicio' => now(),
            'vigencia_fin' => null, // 1 año de vigencia
        ]);

        CatPlanes::create([
            'nombre_plan' => 'Boarding Elite 25 usuarios',
            'descripcion_plan' => "Tu equipo se concentra en Generar, no en Recuperar Gastos. Olvídate de Perseguir Facturas",
            'tipo_plan' => 'empresarial',
            'tipo_pago' => 'postpago',
            'precio' => 5000.0,
            'num_usuarios' => 25,
            'num_facturas' => 5000,
            'vigencia_inicio' => now(),
            'vigencia_fin' => null, // 6 meses de vigencia
        ]);
        CatPlanes::create([
            'nombre_plan' => 'Boarding Elite 25 usuarios',
            'descripcion_plan' => "Tu equipo se concentra en Generar, no en Recuperar Gastos. Olvídate de Perseguir Facturas",
            'tipo_plan' => 'empresarial',
            'tipo_pago' => 'postpago',
            'precio' => 10000.0,
            'num_usuarios' => 50,
            'num_facturas' => 10000,
            'vigencia_inicio' => now(),
            'vigencia_fin' => null, // 6 meses de vigencia
        ]);
    }
}
