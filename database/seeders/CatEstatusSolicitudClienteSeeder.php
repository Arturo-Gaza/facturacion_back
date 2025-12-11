<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class CatEstatusSolicitudClienteSeeder extends Seeder
{
    public function run()
    {
        // ----  Definir IDs agrupados  ----
        $procesandoId = 5; // Procesando real
        $concluidoId  = 9; // Concluido real

        // ----  Estatus que caen en "Procesando"  ----
        $procesando = [
            'En Revisión',
            'Asignado',
            'Visualizado',
            'Procesando',
            'En espera de Archivos',
        ];

        // ----  Estatus que caen en "Concluido"  ----
        $concluido = [
            'Concluido',
            'Pendiente de corregir',
        ];

        // Actualizar Procesando
        DB::table('cat_estatus_solicitud')
            ->whereIn('descripcion_cliente', $procesando)
            ->update(['id_cliente' => $procesandoId]);

        // Actualizar Concluido
        DB::table('cat_estatus_solicitud')
            ->whereIn('descripcion_cliente', $concluido)
            ->update(['id_cliente' => $concluidoId]);

        // Cualquier otro estatus que no entre en grupos,
        // lo dejamos con su propio id

        DB::table('cat_estatus_solicitud')
            ->whereNull('id_cliente')
            ->update(['id_cliente' => DB::raw('id')]);
    }
}
