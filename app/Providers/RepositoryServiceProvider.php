<?php

namespace App\Providers;

use App\Interfaces\Catalogos\CatRolesRepositoryInterface;
use App\Interfaces\Usuario\UsuarioRepositoryInterface;
use App\Repositories\Catalogos\CatRolesRepository;
use App\Repositories\Usuario\UsuarioRepository;
use Illuminate\Support\ServiceProvider;
use App\Interfaces\Catalogos\CatUnidadMedidasRepositoryInterface;
use App\Repositories\Catalogos\CatUnidadMedidasRepository;
use App\Interfaces\Catalogos\CatAlmacenesRepositoryInterface;
use App\Interfaces\Catalogos\CatGpoFamiliaRepositoryInterface;
use App\Interfaces\Catalogos\CatProductosRepositoryInterface;
use App\Repositories\Catalogos\CatAlmacenesRepository;
use App\Repositories\Catalogos\CatGpoFamiliaRepository;
use App\Repositories\Catalogos\CatProductosRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UsuarioRepositoryInterface::class, UsuarioRepository::class);
        $this->app->bind(CatRolesRepositoryInterface::class, CatRolesRepository::class);
        $this->app->bind(CatUnidadMedidasRepositoryInterface::class, CatUnidadMedidasRepository::class);
        $this->app->bind(CatAlmacenesRepositoryInterface::class, CatAlmacenesRepository::class);
        $this->app->bind(CatGpoFamiliaRepositoryInterface::class, CatGpoFamiliaRepository::class);
        $this->app->bind(CatProductosRepositoryInterface::class, CatProductosRepository::class);

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
