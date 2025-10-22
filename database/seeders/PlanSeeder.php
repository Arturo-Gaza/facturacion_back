<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        // Plan Básico Personal Prepago
        Plan::create([
            'nombre_plan' => 'Plan Básico Personal',
            'tipo_plan' => 'personal',
            'tipo_pago' => 'prepago',
            'vigencia_inicio' => now(),
            'vigencia_fin' => null, // Sin fecha de fin (vigente indefinidamente)
        ]);

        // Plan Empresarial Prepago
        Plan::create([
            'nombre_plan' => 'Plan Empresarial Prepago',
            'tipo_plan' => 'empresarial',
            'tipo_pago' => 'prepago',
            'vigencia_inicio' => now(),
            'vigencia_fin' => null, // Vigencia de 1 año
        ]);

        // 3 Planes Empresariales Mensuales (Postpago)
        Plan::create([
            'nombre_plan' => 'Plan Empresarial Básico Mensual',
            'tipo_plan' => 'empresarial',
            'tipo_pago' => 'postpago',
            'vigencia_inicio' => now(),
            'vigencia_fin' => null, // 1 año de vigencia
        ]);

        Plan::create([
            'nombre_plan' => 'Plan Empresarial Avanzado Mensual',
            'tipo_plan' => 'empresarial',
            'tipo_pago' => 'postpago',
            'vigencia_inicio' => now(),
            'vigencia_fin' => null, // 6 meses de vigencia
        ]);

        Plan::create([
            'nombre_plan' => 'Plan Empresarial Premium Mensual',
            'tipo_plan' => 'empresarial',
            'tipo_pago' => 'postpago',
            'vigencia_inicio' => now(),
            'vigencia_fin' => null, // 2 años de vigencia
        ]);
    }
}