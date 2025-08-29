<?php

namespace App\Providers;

use App\Interfaces\ArchivoCarga\TabArchivoCargaRepositoryInterface;
use App\Interfaces\ArchivoCarga\TabArchivoDetalleRepositoryInterface;
use App\Interfaces\ArchivoCarga\TabObservacionesRepositoryInterface;
use App\Interfaces\ArchivoConteo\TabConteoRepositoryInterface;
use App\Interfaces\AsignacionCarga\TabAsignacionInterface;
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
use App\Interfaces\Catalogos\CatUbicacionesRepositoryInterface;
use App\Interfaces\SistemaTickets\CatCategoriasRepositoryInterface;
use App\Interfaces\SistemaTickets\CatCentroRepositoryInterface;
use App\Interfaces\SistemaTickets\CatDepartamentosRepositoryInterface;
use App\Interfaces\SistemaTickets\CatMonedaRepositoryInterface;
use App\Interfaces\SistemaTickets\CatTiposRepositoryInterface;
use App\Interfaces\SistemaTickets\TabArchivosObservacionesDetalleRepositoryInterface;
use App\Interfaces\SistemaTickets\TabArchivosObservacionesSolicitudReqInfoRepositoryInterface;
use App\Interfaces\SistemaTickets\TabCotizacionesDetalleRepositoryInterface;
use App\Interfaces\SistemaTickets\TabCotizacionesSolicitudRepositoryInterface;
use App\Interfaces\SistemaTickets\TabObesrvacionesDetalleRepositoryInterface;
use App\Interfaces\SistemaTickets\TabObesrvacionesSolicitudRepositoryInterface;
use App\Interfaces\SistemaTickets\TabObservacionesSolicitudReqInfoRepositoryInterface;
use App\Interfaces\TabArchivoSolicitudesDetalleRepositoryInterface;
use App\Interfaces\TabDepartamentosCategoriasRepositoryInterface;
use App\Interfaces\TabSolicitudesDetalleRepositoryInterface;
use App\Interfaces\TabSolicitudesRepositoryInterface;
use App\Repositories\ArchivoCarga\TabArchivoDetalleRepository;
use App\Repositories\ArchivoCarga\TabDetalleCargasRerpository;
use App\Repositories\ArchivoCarga\TabObservacionesRepository;
use App\Repositories\ArchivoConteo\TabConteoRepository;
use App\Repositories\SistemaTickets\TabArchivosObservacionesDetalleRepository;
use App\Repositories\AsignacionCarga\TabAsigancionRepository;
use App\Repositories\Catalogos\CatAlmacenesRepository;
use App\Repositories\Catalogos\CatGpoFamiliaRepository;
use App\Repositories\Catalogos\CatProductosRepository;
use App\Repositories\Catalogos\CatUbicacionesRepository;
use App\Repositories\SistemaTickets\CatCategoriasRepository;
use App\Repositories\SistemaTickets\CatCentroRepository;
use App\Repositories\SistemaTickets\CatDepartamentosRepository;
use App\Repositories\SistemaTickets\CatMonedaRepository;
use App\Repositories\SistemaTickets\CatTiposRepository;
use App\Repositories\SistemaTickets\TabArchivosObservacionesSolicitudReqInfoRepository;
use App\Repositories\SistemaTickets\TabCotizacionesDetalleRepository;
use App\Repositories\SistemaTickets\TabCotizacionesSolicitudRepository;
use App\Repositories\SistemaTickets\TabObesrvacionesDetalleRepository;
use App\Repositories\SistemaTickets\TabObservacionesSolicitudRepository;
use App\Repositories\SistemaTickets\TabObservacionesSolicitudReqInfoRepository;
use App\Repositories\TabArchivoSolicitudesDetalleRepository;
use App\Repositories\TabDepartamentosCategoriasRepository;
use App\Repositories\TabSolicitudesDetalleRepository;
use App\Repositories\TabSolicitudesRepository;

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
        $this->app->bind(CatGpoFamiliaRepositoryInterface::class, CatGpoFamiliaRepository::class);
        $this->app->bind(CatProductosRepositoryInterface::class, CatProductosRepository::class);

        $this->app->bind(TabObservacionesRepositoryInterface::class, TabObservacionesRepository::class);
        $this->app->bind(CatUbicacionesRepositoryInterface::class, CatUbicacionesRepository::class);
        $this->app->bind(CatMonedaRepositoryInterface::class, CatMonedaRepository::class);
        $this->app->bind(CatCentroRepositoryInterface::class, CatCentroRepository::class);
        $this->app->bind(TabSolicitudesRepositoryInterface::class, TabSolicitudesRepository::class);

        $this->app->bind(TabSolicitudesDetalleRepositoryInterface::class, TabSolicitudesDetalleRepository::class);
        $this->app->bind(CatDepartamentosRepositoryInterface::class, CatDepartamentosRepository::class);
        $this->app->bind(CatTiposRepositoryInterface::class, CatTiposRepository::class);
        $this->app->bind(TabArchivoSolicitudesDetalleRepositoryInterface::class, TabArchivoSolicitudesDetalleRepository::class);
        $this->app->bind(TabDepartamentosCategoriasRepositoryInterface::class, TabDepartamentosCategoriasRepository::class);
        $this->app->bind(CatCategoriasRepositoryInterface::class, CatCategoriasRepository::class);
        $this->app->bind(TabObesrvacionesSolicitudRepositoryInterface::class, TabObservacionesSolicitudRepository::class);
        $this->app->bind(TabObesrvacionesDetalleRepositoryInterface::class, TabObesrvacionesDetalleRepository::class);

        $this->app->bind(TabCotizacionesDetalleRepositoryInterface::class, TabCotizacionesDetalleRepository::class);
        $this->app->bind(TabCotizacionesSolicitudRepositoryInterface::class, TabCotizacionesSolicitudRepository::class);

        $this->app->bind(TabArchivosObservacionesDetalleRepositoryInterface::class, TabArchivosObservacionesDetalleRepository::class);

        $this->app->bind(TabObservacionesSolicitudReqInfoRepositoryInterface::class, TabObservacionesSolicitudReqInfoRepository::class);

        $this->app->bind(TabArchivosObservacionesSolicitudReqInfoRepositoryInterface::class, TabArchivosObservacionesSolicitudReqInfoRepository::class);

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
