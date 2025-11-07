<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MontosPrepagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $planes = [
            // Plan Check In (id_plan = 1)
            [
                'id_plan' => 1,
                'nombre' => 'Check in 150 ',
                'descripcion' => "Comienza tu viaje sin complicaciones. Automatiza la
Recuperación de tus Gastos de viajes en minutos y
olvídate de perseguir facturas. Ideal para persona que tienen menos de 50 Gastos a
Recuperar al año.",
                'monto' => 150.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_plan' => 1,
                'nombre' => 'Check in 300 ',
                'descripcion' => "Comienza tu viaje sin complicaciones. Automatiza la
Recuperación de tus Gastos de viajes en minutos y
olvídate de perseguir facturas. Ideal para persona que tienen menos de 50 Gastos a
Recuperar al año.",
                'monto' => 300.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_plan' => 1,
                'nombre' => 'Check in 450 ',
                'descripcion' => "Comienza tu viaje sin complicaciones. Automatiza la
Recuperación de tus Gastos de viajes en minutos y
olvídate de perseguir facturas. Ideal para persona que tienen menos de 50 Gastos a
Recuperar al año.",
                'monto' => 450.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_plan' => 1,
                'nombre' => 'Check in 600 ',
                'descripcion' => "Comienza tu viaje sin complicaciones. Automatiza la
Recuperación de tus Gastos de viajes en minutos y
olvídate de perseguir facturas. Ideal para persona que tienen menos de 50 Gastos a
Recuperar al año.",
                'monto' => 600.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_plan' => 1,
                'nombre' => 'Check in 750 ',
                'descripcion' => "Comienza tu viaje sin complicaciones. Automatiza la
Recuperación de tus Gastos de viajes en minutos y
olvídate de perseguir facturas. Ideal para persona que tienen menos de 50 Gastos a
Recuperar al año.",
                'monto' => 750.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_plan' => 1,
                'nombre' => 'Check in 900 ',
                'descripcion' => "Comienza tu viaje sin complicaciones. Automatiza la
Recuperación de tus Gastos de viajes en minutos y
olvídate de perseguir facturas. Ideal para persona que tienen menos de 50 Gastos a
Recuperar al año.",
                'monto' => 900.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Plan Vuelo (id_plan = 2)
            [
                'id_plan' => 2,
                'nombre' => 'Vuelo 400 ',
                'descripcion' => "Tu asistente de Recuperación de gastos inteligente. Ideal
para profesionales que viajan frecuentemente y
necesitan control total de sus comprobaciones.
Ideal para persona que tienen más de 20 Gastos a recuperar al mes, y  para equipos de 1 a 5 personas",
                'monto' => 400.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_plan' => 2,
                'nombre' => 'Vuelo 550 ',
                'descripcion' => "Tu asistente de Recuperación de gastos inteligente. Ideal
para profesionales que viajan frecuentemente y
necesitan control total de sus comprobaciones.
Ideal para persona que tienen más de 20 Gastos a recuperar al mes, y  para equipos de 1 a 5 personas",
                'monto' => 550.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_plan' => 2,
                'nombre' => 'Vuelo 700 ',
                'descripcion' => "Tu asistente de Recuperación de gastos inteligente. Ideal
para profesionales que viajan frecuentemente y
necesitan control total de sus comprobaciones.
Ideal para persona que tienen más de 20 Gastos a recuperar al mes, y  para equipos de 1 a 5 personas",
                'monto' => 700.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_plan' => 2,
                'nombre' => 'Vuelo 850 ',
                'descripcion' => "Tu asistente de Recuperación de gastos inteligente. Ideal
para profesionales que viajan frecuentemente y
necesitan control total de sus comprobaciones.
Ideal para persona que tienen más de 20 Gastos a recuperar al mes, y  para equipos de 1 a 5 personas",
                'monto' => 850.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_plan' => 2,
                'nombre' => 'Vuelo 1000 ',
                'descripcion' => "Tu asistente de Recuperación de gastos inteligente. Ideal
para profesionales que viajan frecuentemente y
necesitan control total de sus comprobaciones.
Ideal para persona que tienen más de 20 Gastos a recuperar al mes, y  para equipos de 1 a 5 personas",
                'monto' => 1000.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_plan' => 2,
                'nombre' => 'Vuelo 1150 ',
                'descripcion' => "Tu asistente de Recuperación de gastos inteligente. Ideal
para profesionales que viajan frecuentemente y
necesitan control total de sus comprobaciones.
Ideal para persona que tienen más de 20 Gastos a recuperar al mes, y  para equipos de 1 a 5 personas",
                'monto' => 1150.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Primera Clase (id_plan = 3)
            [
                'id_plan' => 3,
                'nombre' => 'Primera Clase 1000 ',
                'descripcion' => "La decisión empresarial que Genera. Automatiza la
Recupera Gastos de todo tu equipo y deduce el 95% de tus
Gastos de viáticos.
Ideal para empresas que quieren pagar por lo que consumen",
                'monto' => 1000.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_plan' => 3,
                'nombre' => 'Primera Clase 2000 ',
                'descripcion' => "La decisión empresarial que Genera. Automatiza la
Recupera Gastos de todo tu equipo y deduce el 95% de tus
Gastos de viáticos.
Ideal para empresas que quieren pagar por lo que consumen",
                'monto' => 2000.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_plan' => 3,
                'nombre' => 'Primera Clase 3000 ',
                'descripcion' => "La decisión empresarial que Genera. Automatiza la
Recupera Gastos de todo tu equipo y deduce el 95% de tus
Gastos de viáticos.
Ideal para empresas que quieren pagar por lo que consumen",
                'monto' => 3000.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_plan' => 3,
                'nombre' => 'Primera Clase 4000 ',
                'descripcion' => "La decisión empresarial que Genera. Automatiza la
Recupera Gastos de todo tu equipo y deduce el 95% de tus
Gastos de viáticos.
Ideal para empresas que quieren pagar por lo que consumen",
                'monto' => 4000.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_plan' => 3,
                'nombre' => 'Primera Clase 5000 ',
                'descripcion' => "La decisión empresarial que Genera. Automatiza la
Recupera Gastos de todo tu equipo y deduce el 95% de tus
Gastos de viáticos.
Ideal para empresas que quieren pagar por lo que consumen",
                'monto' => 5000.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Boarding Elite (id_plan = 4)
            [
                'id_plan' => 4,
                'nombre' => 'Boarding Elite 5000 ',
                'descripcion' => "Tu equipo se concentra en Generar, no en Recuperar
Gastos. Olvídate de Perseguir Facturas.
Ideal para empresas que prefieren un Gasto con presupuesto
mensual controlado",
                'monto' => 5000.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_plan' => 4,
                'nombre' => 'Boarding Elite 10000 ',
                'descripcion' => "Tu equipo se concentra en Generar, no en Recuperar
Gastos. Olvídate de Perseguir Facturas.
Ideal para empresas que prefieren un Gasto con presupuesto
mensual controlado",
                'monto' => 10000.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_plan' => 4,
                'nombre' => 'Boarding Elite 20000 ',
                'descripcion' => "Tu equipo se concentra en Generar, no en Recuperar
Gastos. Olvídate de Perseguir Facturas.
Ideal para empresas que prefieren un Gasto con presupuesto
mensual controlado",
                'monto' => 20000.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_plan' => 4,
                'nombre' => 'Boarding Elite 30000 ',
                'descripcion' => "Tu equipo se concentra en Generar, no en Recuperar
Gastos. Olvídate de Perseguir Facturas.
Ideal para empresas que prefieren un Gasto con presupuesto
mensual controlado",
                'monto' => 30000.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_plan' => 4,
                'nombre' => 'Boarding Elite 40000 ',
                'descripcion' => "Tu equipo se concentra en Generar, no en Recuperar
Gastos. Olvídate de Perseguir Facturas.
Ideal para empresas que prefieren un Gasto con presupuesto
mensual controlado",
                'monto' => 40000.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('cat_montos_prepago')->insert($planes);
    }
}

