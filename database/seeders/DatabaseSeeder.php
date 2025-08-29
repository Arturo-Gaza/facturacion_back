<?php

namespace Database\Seeders;

use App\Models\SistemaTickets\EstatusSolicitud;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        #$this->call(EstatusSeeder::class);
        $this->call(RolSeeder::class);
        $this->call(DepartamentosSeeder::class);
        $this->call(TiposSeeder::class);
        $this->call(CategoriaSeeder::class);
        $this->call(UserSeeder::class);
        # $this->call(UbicacionSeeder::class);
        $this->call(EstatusSolicitudSeeder::class);
        $this->call(MonedaSeeder::class);
        $this->call(UnidadMedidaSeeder::class);
        $this->call(GpoFamiliaSeeder::class);
        #$this->call(ProductosSeeder::class);
        $this->call(TabDepartamentosCategoriasSeeder::class);
$this->call(UsuariosSoliSeeder::class);

        //Department::factory(6)->create();
        //Employee::factory(25)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);


    }
}
