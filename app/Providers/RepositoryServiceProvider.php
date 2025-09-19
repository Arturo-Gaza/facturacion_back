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
use App\Interfaces\Catalogos\CatEstatusSatRepositoryInterface;
use App\Interfaces\Catalogos\CatGpoFamiliaRepositoryInterface;
use App\Interfaces\Catalogos\CatProductosRepositoryInterface;
use App\Interfaces\Catalogos\CatRegimenesFiscaslesRepositoryInterface;
use App\Interfaces\Catalogos\CatTipoDireccionRepositoryInterface;
use App\Interfaces\Catalogos\CatTiposContactosRepositoryInterface;
use App\Interfaces\Catalogos\CatUbicacionesRepositoryInterface;
use App\Interfaces\SistemaFacturacion\TabClientesFiscalesRepositoryInterface;
use App\Interfaces\SistemaFacturacion\TabClientesRepositoryInterface;
use App\Interfaces\SistemaFacturacion\TabContactosRepositoryInterface;
use App\Interfaces\SistemaFacturacion\TabDireccionesRepositoryInterface;
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
use App\Repositories\Catalogos\CatEstatusSatRepository;
use App\Repositories\Catalogos\CatGpoFamiliaRepository;
use App\Repositories\Catalogos\CatProductosRepository;
use App\Repositories\Catalogos\CatRegimensFiscalesRepository;
use App\Repositories\Catalogos\CatTipoDireccionRepository;
use App\Repositories\Catalogos\CatTiposContactosRepository;
use App\Repositories\Catalogos\CatUbicacionesRepository;
use App\Repositories\SistemaFacturacion\TabClientesFiscalesRepository;
use App\Repositories\SistemaFacturacion\TabClientesRepository;
use App\Repositories\SistemaFacturacion\TabContactoRepository;
use App\Repositories\SistemaFacturacion\TabDireccionesRepository;
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
use App\Interfaces\SistemaFacturacion\EstatusMovimientoRepositoryInterface;
use App\Repositories\SistemaFacturacion\EstatusMovimientoRepository;
use App\Interfaces\SistemaFacturacion\EstadoSolicitudRepositoryInterface;
use App\Repositories\SistemaFacturacion\EstadoSolicitudRepository;
use App\Interfaces\SistemaFacturacion\FacturaRepositoryInterface;
use App\Repositories\SistemaFacturacion\FacturaRepository;
use App\Interfaces\SistemaFacturacion\MovimientoSaldoRepositoryInterface;
use App\Repositories\SistemaFacturacion\MovimientoSaldoRepository;
use App\Interfaces\SistemaFacturacion\PrecioRepositoryInterface;
use App\Repositories\SistemaFacturacion\PrecioRepository;
use App\Interfaces\SistemaFacturacion\ServicioRepositoryInterface;
use App\Repositories\SistemaFacturacion\ServicioRepository;
use App\Interfaces\SistemaFacturacion\SolicitudRepositoryInterface;
use App\Repositories\SistemaFacturacion\SolicitudRepository;
use App\Interfaces\DatosFiscalesRepositoryInterface;
use App\Repositories\DatosFiscalesRepository;
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

        $this->app->bind(TabObservacionesRepositoryInterface::class, TabObservacionesRepository::class);
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
        //Facturacion

        $this->app->bind(CatEstatusSatRepositoryInterface::class, CatEstatusSatRepository::class);
        $this->app->bind(CatRegimenesFiscaslesRepositoryInterface::class, CatRegimensFiscalesRepository::class);
        $this->app->bind(CatTiposContactosRepositoryInterface::class, CatTiposContactosRepository::class);
        $this->app->bind(CatTipoDireccionRepositoryInterface::class, CatTipoDireccionRepository::class);
        $this->app->bind(TabClientesRepositoryInterface::class, TabClientesRepository::class);
        $this->app->bind(TabClientesFiscalesRepositoryInterface::class, TabClientesFiscalesRepository::class);
        $this->app->bind(TabContactosRepositoryInterface::class, TabContactoRepository::class);
        $this->app->bind(TabDireccionesRepositoryInterface::class, TabDireccionesRepository::class);
   
        $this->app->bind(EstatusMovimientoRepositoryInterface::class, EstatusMovimientoRepository::class);
        $this->app->bind(EstadoSolicitudRepositoryInterface::class, EstadoSolicitudRepository::class);
        $this->app->bind(FacturaRepositoryInterface::class, FacturaRepository::class);
        $this->app->bind(MovimientoSaldoRepositoryInterface::class, MovimientoSaldoRepository::class);
        $this->app->bind(PrecioRepositoryInterface::class, PrecioRepository::class);
        $this->app->bind(ServicioRepositoryInterface::class, ServicioRepository::class);

        $this->app->bind(DatosFiscalesRepositoryInterface::class, DatosFiscalesRepository::class);
        $this->app->bind(SolicitudRepositoryInterface::class, SolicitudRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
