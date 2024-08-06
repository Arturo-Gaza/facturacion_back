<?php

namespace App\Providers;

use App\Interfaces\Catalogos\Egresos\CatClasificacionAfectacionRepositoryInterface;
use App\Interfaces\Catalogos\CatGeneralRepositoryInterface;
use App\Interfaces\Catalogos\Egresos\CatActividadInstitucionalRepositoryInterface;
use App\Interfaces\Catalogos\Egresos\CatCapitulosRepositoryInterface;
use App\Interfaces\Catalogos\Egresos\CatConceptosRepositoryInterface;
use App\Interfaces\Catalogos\Egresos\CatFinalidadRepositoryInterface;
use App\Interfaces\Catalogos\Egresos\CatFuentesFinanciamientoRepositoryInterface;
use App\Interfaces\Catalogos\Egresos\CatFuncionesRepositoryInterface;
use App\Interfaces\Catalogos\Egresos\CatObjetoGastosRepositoryInterface;
use App\Interfaces\Catalogos\Egresos\CatProgramasPresupuestarioRepositoryInterface;
use App\Interfaces\Catalogos\Egresos\CatRamosRepositoryInterface;
use App\Interfaces\Catalogos\Egresos\CatSubfuncionesRepositoryInterface;
use App\Interfaces\Catalogos\Egresos\CatTipoAfectacionRepositoryInterface;
use App\Interfaces\Catalogos\Egresos\CatTipoGastosRepositoryInterface;
use App\Interfaces\Catalogos\Egresos\CatTipoMovimientoRepositoryInterface;
use App\Interfaces\Catalogos\Egresos\CatTipoRamosRepositoryInterface;
use App\Interfaces\Catalogos\Usuarios\CatAreasRepositoryInterface;
use App\Interfaces\Catalogos\Usuarios\CatEntidadesFederativasRepositoryInterface;
use App\Interfaces\Catalogos\Usuarios\CatUnidadesResponsablesRepositoryInterface;
use App\Interfaces\Usuario\UsuarioRepositoryInterface;
use App\Interfaces\Permiso\PermisoRepositoryInterface;
use App\Interfaces\Rol\RolRepositoryInterface;
use App\Interfaces\Parametro\ParametroRepositoryInterface;
use App\Interfaces\UsuarioRol\UsuarioRolRepositoryInterface;
use App\Repositories\Usuario\UsuarioRepository;
use App\Interfaces\CatSistema\CatSistemaRepositoryInterface;
use App\Interfaces\UsuarioSistema\UsuarioSistemaRepositoryInterface;
use App\Repositories\Catalogos\Egresos\CatClasificacionAfectacionRepository;
use App\Repositories\Catalogos\Egresos\CatActividadInstitucionalRepository;
use App\Repositories\Catalogos\Egresos\CatCapitulosRepository;
use App\Repositories\Catalogos\Egresos\CatConceptosRepository;
use App\Repositories\Catalogos\Egresos\CatEstadoAfectacionRepository;
use App\Repositories\Catalogos\Egresos\CatFinalidadRepository;
use App\Repositories\Catalogos\Egresos\CatFuentesFinanciamientoRepository;
use App\Repositories\Catalogos\Egresos\CatFuncionesRepository;
use App\Repositories\Catalogos\Egresos\CatObjetoGastosRepository;
use App\Repositories\Catalogos\Egresos\CatProgramasPresupuestariosRepository;
use App\Repositories\Catalogos\Egresos\CatRamosRepository;
use App\Repositories\Catalogos\Egresos\CatSubfuncionesRepository;
use App\Repositories\Catalogos\Egresos\CatTipoAfectacionReposistory;
use App\Repositories\Catalogos\Egresos\CatTipoGastosRepository;
use App\Repositories\Catalogos\Egresos\CatTipoMovimientoRepository;
use App\Repositories\Catalogos\Egresos\CatTipoRamosRepository;
use App\Repositories\Catalogos\Usuarios\CatAreasRepository;
use App\Repositories\Catalogos\Usuarios\CatEntidadesFederativasRepository;
use App\Repositories\Catalogos\Usuarios\CatUnidadesResponsablesRepository;
use App\Repositories\CatSistema\CatSistemaRepository;
use App\Repositories\UsuarioSistema\UsuarioSistemaRepository;
use App\Repositories\Parametro\ParametroRepository;
use App\Repositories\UsuarioRol\UsuarioRolRepository;
use App\Repositories\Permiso\PermisoRepository;
use App\Repositories\Rol\RolRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UsuarioRepositoryInterface::class, UsuarioRepository::class);
        $this->app->bind(ParametroRepositoryInterface::class, ParametroRepository::class);
        $this->app->bind(UsuarioSistemaRepositoryInterface::class, UsuarioSistemaRepository::class);
        $this->app->bind(CatSistemaRepositoryInterface::class, CatSistemaRepository::class);
        $this->app->bind(CatClasificacionAfectacionRepositoryInterface::class, CatClasificacionAfectacionRepository::class);
        $this->app->bind(CatGeneralRepositoryInterface::class, CatEstadoAfectacionRepository::class);
        $this->app->bind(CatTipoAfectacionRepositoryInterface::class, CatTipoAfectacionReposistory::class);
        $this->app->bind(CatTipoMovimientoRepositoryInterface::class, CatTipoMovimientoRepository::class);
        $this->app->bind(CatFinalidadRepositoryInterface::class, CatFinalidadRepository::class);
        $this->app->bind(CatFuncionesRepositoryInterface::class, CatFuncionesRepository::class);
        $this->app->bind(CatSubfuncionesRepositoryInterface::class, CatSubfuncionesRepository::class);
        $this->app->bind(CatFuentesFinanciamientoRepositoryInterface::class, CatFuentesFinanciamientoRepository::class);
        $this->app->bind(PermisoRepositoryInterface::class, PermisoRepository::class);
        $this->app->bind(CatCapitulosRepositoryInterface::class, CatCapitulosRepository::class);
        $this->app->bind(CatConceptosRepositoryInterface::class, CatConceptosRepository::class);
        $this->app->bind(CatObjetoGastosRepositoryInterface::class, CatObjetoGastosRepository::class);
        $this->app->bind(CatProgramasPresupuestarioRepositoryInterface::class, CatProgramasPresupuestariosRepository::class);
        $this->app->bind(CatTipoRamosRepositoryInterface::class, CatTipoRamosRepository::class);
        $this->app->bind(CatRamosRepositoryInterface::class, CatRamosRepository::class);
        $this->app->bind(CatTipoGastosRepositoryInterface::class, CatTipoGastosRepository::class);
        $this->app->bind(CatActividadInstitucionalRepositoryInterface::class, CatActividadInstitucionalRepository::class);

        //Catalogos usuarios
        $this->app->bind(CatUnidadesResponsablesRepositoryInterface::class, CatUnidadesResponsablesRepository::class);
        $this->app->bind(CatEntidadesFederativasRepositoryInterface::class, CatEntidadesFederativasRepository::class);
        $this->app->bind(CatAreasRepositoryInterface::class, CatAreasRepository::class);

        $this->app->bind(RolRepositoryInterface::class, RolRepository::class);

        $this->app->bind(UsuarioRolRepositoryInterface::class, UsuarioRolRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
