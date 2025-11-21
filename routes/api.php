<?php


use App\Http\Controllers\ArchivoCarga\TabObservacionesController;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Catalogos\CatAlmacenesController;
use App\Http\Controllers\Catalogos\CatEstatusSatController;
use App\Http\Controllers\Catalogos\CatGpoFamiliaController;
use App\Http\Controllers\Catalogos\CatProductosController;
use App\Http\Controllers\Catalogos\CatRegimenesFiscalesController;
use App\Http\Controllers\Catalogos\CatRolesController;
use App\Http\Controllers\Catalogos\CatTipoContactoController;
use App\Http\Controllers\Catalogos\CatTipoDireccionController;
use App\Http\Controllers\Catalogos\CatUbicaionesController;
use App\Http\Controllers\Catalogos\CatUnidadMedidasController;
use App\Http\Controllers\CatMontosPrepagoController;
use App\Http\Controllers\CatPlanesController;
use App\Http\Controllers\SistemaFacturacion\TabClientesController;
use App\Http\Controllers\SistemaFacturacion\TabClientesFiscalesController;
use App\Http\Controllers\SistemaFacturacion\TabContactoController;
use App\Http\Controllers\SistemaFacturacion\TabDireccionesController;
use App\Http\Controllers\SistemaTickets\CatCategoriasController;
use App\Http\Controllers\SistemaTickets\CatCentroController;
use App\Http\Controllers\SistemaTickets\CatDepartamentosController;
use App\Http\Controllers\SistemaTickets\CatMonedaController;
use App\Http\Controllers\SistemaTickets\CatTiposController;
use App\Http\Controllers\SistemaTickets\TabArchivosObservacionesDetalleController;
use App\Http\Controllers\SistemaTickets\TabArchivosObservacionesSolicitudReqInfoController;
use App\Http\Controllers\SistemaTickets\TabCotizacionesSolicitudesController;
use App\Http\Controllers\SistemaTickets\TabObesrvacionesDetalleController;
use App\Http\Controllers\SistemaTickets\TabObservacionesSolicitudController;
use App\Http\Controllers\SistemaTickets\TabObservacionesSolicitudReqInfoController;
use App\Http\Controllers\TabArchivoSolicitudesDetalleController;
use App\Http\Controllers\TabDepartamentosCategoriasController;
use App\Http\Controllers\TabSolicitudesController;
use App\Http\Controllers\TabSolicitudesDetalleController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\EstatusMovimientoController;
use App\Http\Controllers\EstadoSolicitudController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\MovimientoSaldoController;
use App\Http\Controllers\PrecioController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\DatosFiscalesController;
use App\Http\Controllers\MotivoRechazoController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\SuscripcionController;
use App\Http\Middleware\AcceptJsonMiddleware;
use App\Models\Suscripciones;
use Illuminate\Support\Facades\Route;

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('usuario/enviarCorreoRec', [UsuarioController::class, 'enviarCorreoRec']);
Route::post('usuario/enviarCorreoConf', [UsuarioController::class, 'enviarCorreoConf']);
Route::post('usuario/enviarCorreoValReceptor', [UsuarioController::class, 'enviarCorreoValReceptor']);

Route::post('usuario/enviarSMSValReceptor', [UsuarioController::class, 'enviarSMSValReceptor']);

Route::post('usuario/enviarCorreoCambiarCorreo', [UsuarioController::class, 'enviarCorreoCambiarCorreo']);


Route::post('usuario/validarCorreoRec', [UsuarioController::class, 'validarCorreoRec']);
Route::post('usuario/enviarSMSConf', [UsuarioController::class, 'enviarSMSConf']);
Route::post('usuario/enviarSMSRec', [UsuarioController::class, 'enviarSMSRec']);


Route::post('usuario/validarSMSConf', [UsuarioController::class, 'validarSMSConf']);
Route::post('usuario/validarSMSRec', [UsuarioController::class, 'validarSMSRec']);


Route::post('usuario/validarCorreoConf', [UsuarioController::class, 'validarCorreoConf']);
Route::post('usuario/validarCorreoValReceptor', [UsuarioController::class, 'validarCorreoValReceptor']);

Route::post('usuario/validarSMSValReceptor', [UsuarioController::class, 'validarSMSValReceptor']);

Route::post('usuario/enviarCorreoInhabilitar', [UsuarioController::class, 'enviarCorreoInhabilitar']);
Route::post('usuario/validarCorreoInhabilitar', [UsuarioController::class, 'validarCorreoInhabilitar']);

Route::post('usuario/validarCorreoCambiarCorreo', [UsuarioController::class, 'validarCorreoCambiarCorreo']);


Route::post('usuario/enviarCorreoEliminar', [UsuarioController::class, 'enviarCorreoEliminar']);
Route::post('usuario/validarCorreoEliminar', [UsuarioController::class, 'validarCorreoEliminar']);


Route::post('usuario/recPass', [UsuarioController::class, 'recPass']);

Route::post('usuario/habilitar', [UsuarioController::class, 'habilitar']);
Route::post('usuario/desHabilitar', [UsuarioController::class, 'deshabilitar']);
Route::post('usuario/eliminarPorAdmin', [UsuarioController::class, 'eliminarPorAdmin']);
Route::post('usuario/habilitarPorAdmin', [UsuarioController::class, 'habilitarPorAdmin']);
Route::post('usuario/desHabilitarPorAdmin', [UsuarioController::class, 'desHabilitarPorAdmin']);
Route::post('usuario/eliminar', [UsuarioController::class, 'eliminar']);
Route::get('usuario/getColaboradores/{id}', [UsuarioController::class, 'getColaboradores']);


Route::get('/test', function () {
    return 'ok v4.0';
});

Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/loginEmpleados', [AuthController::class, 'loginEmpleados']);
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/registerCliente', [AuthController::class, 'registerCliente']);
Route::post('auth/registerHijo', [AuthController::class, 'registerHijo']);

Route::post('auth/completarHijo', [AuthController::class, 'completarHijo']);
Route::post('auth/updateHijo', [AuthController::class, 'updateHijo']);


Route::get('usuario/getDatos/{id}', [UsuarioController::class, 'getDatos']);
Route::put('usuario/editarDatos/{id}', [UsuarioController::class, 'editarDatos']);
Route::get('usuario/getMesaAyuda', [UsuarioController::class, 'getMesaAyuda']);
//Route::post('catProductos/register', [CatProductosController::class, 'store']);

Route::middleware(['auth:sanctum', "response.time", AcceptJsonMiddleware::class])->group(function () {

    //Cierre de sesión
    Route::get('auth/logout/{id}', [AuthController::class, 'logout']);

    Route::get('usuario/getAll', [UsuarioController::class, 'getAll']);
    Route::put('usuario/update/{id}', [UsuarioController::class, 'update']);
    Route::put('usuario/deleteUser/{id}', [UsuarioController::class, 'deleteUser']);
    Route::get('usuario/getById/{id}', [UsuarioController::class, 'getById']);

    //Ruta para tabla Solicitudes
    //Route::get('tabSolicitudes/getAll', [TabSolicitudesController::class, 'getAll']);
    //RRoute::get('tabSolicitudes/getById/{id}', [TabSolicitudesController::class, 'getByID']);
    //Route::get('tabSolicitudes/getByIdSolicitud/{id}', [TabSolicitudesController::class, 'getByIDSolicitud']);
    //Route::post('tabSolicitudes/register', [TabSolicitudesController::class, 'store']);
    //Route::put('tabSolicitudes/update/{id}', [TabSolicitudesController::class, 'update']);
    //Route::post('tabSolicitudes/asignar', [TabSolicitudesController::class, 'asignar']);
    // Route::post('tabSolicitudes/reasignar', [TabSolicitudesController::class, 'reasignar']);
    //Route::post('tabSolicitudes/cambiarEstatus', [TabSolicitudesController::class, 'cambiarEstatus']);
    //Route::post('tabSolicitudes/reporte', [TabSolicitudesController::class, 'reporte']);
    //Route::post('tabSolicitudes/formatearSolicitud/{id}', [TabSolicitudesController::class, 'formatearSolicitud']);
    //Route::get('tabSolicitudes/getCotizaciones/{id}', [TabSolicitudesController::class, 'getCotizaciones']);

    //Ruta para tabla SolicitudesDetalle
    Route::get('tabSolicitudesDetalle/getAll', [TabSolicitudesDetalleController::class, 'getAll']);
    Route::get('tabSolicitudesDetalle/getById/{id}', [TabSolicitudesDetalleController::class, 'getById']);
    Route::get('tabSolicitudesDetalle/getByIDSolicitud/{id}', [TabSolicitudesDetalleController::class, 'getByIDSolicitud']);
    Route::post('tabSolicitudesDetalle/register', [TabSolicitudesDetalleController::class, 'store']);
    Route::put('tabSolicitudesDetalle/update/{id}', [TabSolicitudesDetalleController::class, 'update']);
    Route::put('tabSolicitudesDetalle/deleteByDetalle/{id}', [TabSolicitudesDetalleController::class, 'deleteByDetalle']);
    Route::put('tabSolicitudesDetalle/deleteBySolicitud/{id}', [TabSolicitudesDetalleController::class, 'deleteBySolicitud']);
    //Ruta para tabla ArchivoSolicitudesDetalle
    Route::get('tabArchivoSolicitudesDetalle/getAll', [TabArchivoSolicitudesDetalleController::class, 'getAll']);
    Route::get('tabArchivoSolicitudesDetalle/getById/{id}', [TabArchivoSolicitudesDetalleController::class, 'getById']);
    Route::get('tabArchivoSolicitudesDetalle/getByIDSolicitudDeta/{id}', [TabArchivoSolicitudesDetalleController::class, 'getByIDSolicitudDeta']);
    Route::post('tabArchivoSolicitudesDetalle/register', [TabArchivoSolicitudesDetalleController::class, 'store']);
    Route::put('tabArchivoSolicitudesDetalle/update/{id}', [TabArchivoSolicitudesDetalleController::class, 'update']);
    Route::post('tabArchivoSolicitudesDetalle/delete', [TabArchivoSolicitudesDetalleController::class, 'delete']);
    //Ruta para tabla DepartamentosCategorias
    Route::get('tabDepartamentosCategorias/getAll', [TabDepartamentosCategoriasController::class, 'getAll']);


    Route::get('tabDepartamentosCategorias/getByDep/{id}', [TabDepartamentosCategoriasController::class, 'getByDep']);
    Route::post('tabDepartamentosCategorias/register', [TabDepartamentosCategoriasController::class, 'store']);
    Route::post('tabDepartamentosCategorias/addDelete', [TabDepartamentosCategoriasController::class, 'AddDelete']);
    Route::delete('tabDepartamentosCategorias/delete', [TabDepartamentosCategoriasController::class, 'delete']);

    //CATALOGOS
    //Rutas de catalogo Roles
    Route::get('catRoles/getAll', [CatRolesController::class, 'getAll']);
    Route::get('catRoles/getMesa', [CatRolesController::class, 'getMesa']);
    Route::get('catRoles/getById/{id}', [CatRolesController::class, 'getById']);
    Route::post('catRoles/register', [CatRolesController::class, 'store']);
    Route::put('catRoles/update/{id}', [CatRolesController::class, 'update']);
    Route::post('catRoles/exportar', [CatRolesController::class, 'exportar']);

    //Rutas Catalogo Unidad de Medida
    Route::get('catUnidadMedidas/getAll', [CatUnidadMedidasController::class, 'getAll']);
    Route::get('catUnidadMedidas/getById/{id}', [CatUnidadMedidasController::class, 'getById']);
    Route::post('catUnidadMedidas/register', [CatUnidadMedidasController::class, 'store']);
    Route::put('catUnidadMedidas/update/{id}', [CatUnidadMedidasController::class, 'update']);
    Route::post('catUnidadMedidas/exportar', [CatUnidadMedidasController::class, 'exportar']);


    Route::get('usuario/getAllUserAlmacen/{idCarga}', [UsuarioController::class, 'getAllUserAlmacen']);
    Route::get('usuario/getAllUser', [UsuarioController::class, 'getAllUser']);
    Route::get('usuario/getAllUserAsignado/{idCarga}', [UsuarioController::class, 'getAllUserAsignado']);

    //Ruta para tabla Observaciones
    Route::get('tabObsercaiones/getAll', [TabObservacionesController::class, 'getAll']);
    Route::get('tabObsercaiones/getById/{id}', [TabObservacionesController::class, 'getById']);
    Route::get('tabObsercaiones/getByIdCarga/{idCarga}', [TabObservacionesController::class, 'getByIDCarga']);
    Route::get('tabObsercaiones/getByIdCargaIdUser/{idCarga}/{idUser}', [TabObservacionesController::class, 'getByIDCargaIDUser']);
    Route::post('tabObsercaiones/register', [TabObservacionesController::class, 'store']);
    Route::put('tabObsercaiones/update/{id}', [TabObservacionesController::class, 'update']);


    //Ruta para cat moneda
    Route::get('catMoneda/getAll', [CatMonedaController::class, 'getAll']);
    Route::get('catMoneda/getById/{id}', [CatMonedaController::class, 'getById']);
    Route::post('catMoneda/register', [CatMonedaController::class, 'store']);
    Route::put('catMoneda/update/{id}', [CatMonedaController::class, 'update']);
    Route::post('catMoneda/exportar', [CatMonedaController::class, 'exportar']);

    //Ruta para cat centro
    Route::get('catCentro/getAll', [CatCentroController::class, 'getAll']);
    Route::get('catCentro/getById/{id}', [CatCentroController::class, 'getById']);
    Route::post('catCentro/register', [CatCentroController::class, 'store']);
    Route::put('catCentro/update/{id}', [CatCentroController::class, 'update']);

    //Ruta para cat departamentos
    Route::get('catDepartamentos/getAll', [CatDepartamentosController::class, 'getAll']);
    Route::get('catDepartamentos/getById/{id}', [CatDepartamentosController::class, 'getById']);
    Route::post('catDepartamentos/register', [CatDepartamentosController::class, 'store']);
    Route::put('catDepartamentos/update/{id}', [CatDepartamentosController::class, 'update']);
    Route::post('catDepartamentos/exportar', [CatDepartamentosController::class, 'exportar']);

    //Ruta para cat tipos
    Route::get('catTipos/getAll', [CatTiposController::class, 'getAll']);
    Route::get('catTipos/getById/{id}', [CatTiposController::class, 'getById']);
    Route::get('catTipos/getByDpto/{id}', [CatTiposController::class, 'getByDpto']);
    Route::post('catTipos/register', [CatTiposController::class, 'store']);
    Route::put('catTipos/update/{id}', [CatTiposController::class, 'update']);
    Route::post('catTipos/exportar', [CatTiposController::class, 'exportar']);
    //Ruta para cat categorias
    Route::get('catCategorias/getAll', [CatCategoriasController::class, 'getAll']);
    Route::get('catCategorias/getById/{id}', [CatCategoriasController::class, 'getById']);
    Route::get('catCategorias/getByDpto/{id}', [CatCategoriasController::class, 'getByDpto']);
    Route::post('catCategorias/register', [CatCategoriasController::class, 'store']);
    Route::put('catCategorias/update/{id}', [CatCategoriasController::class, 'update']);
    Route::get('catCategorias/getByIdCat/{id}', [CatCategoriasController::class, 'getByIdCat']);
    Route::post('catCategorias/exportar', [CatCategoriasController::class, 'exportar']);
    Route::post('tabDepartamento/cateDepartamento', [TabDepartamentosCategoriasController::class, 'AddDelete']);

    //Ruta para tab observaciones solicitud
    Route::get('ObservacionSolicitud/getAll', [TabObservacionesSolicitudController::class, 'getAll']);
    Route::get('ObservacionSolicitud/getById/{id}', [TabObservacionesSolicitudController::class, 'getById']);
    Route::get('ObservacionSolicitud/solicitud/{id}', [TabObservacionesSolicitudController::class, 'getBySolicitud']);
    Route::post('ObservacionSolicitud/register', [TabObservacionesSolicitudController::class, 'store']);
    Route::put('ObservacionSolicitud/update/{id}', [TabObservacionesSolicitudController::class, 'update']);

    Route::post('tabSolicitudes/cambiarEstatus', [TabSolicitudesController::class, 'cambiarEstatus']);

    //Ruta para tab observaciones solicitud detalle
    Route::get('tabobservacionesSolicitudDetalle/getAll', [TabObesrvacionesDetalleController::class, 'getAll']);
    Route::get('tabobservacionesSolicitudDetalle/getById/{id}', [TabObesrvacionesDetalleController::class, 'getById']);
    Route::get('tabobservacionesSolicitudDetalle/getByIdDetalle/{id}', [TabObesrvacionesDetalleController::class, 'getByIdDetalle']);
    Route::post('tabobservacionesSolicitudDetalle/register', [TabObesrvacionesDetalleController::class, 'store']);
    Route::put('tabobservacionesSolicitudDetalle/update/{id}', [TabObesrvacionesDetalleController::class, 'update']);

    //Ruta para tab cotizaciones  solicitud
    Route::get('tabCotizacionesSolicitudes/getAll', [TabCotizacionesSolicitudesController::class, 'getAll']);
    Route::get('tabCotizacionesSolicitudes/getById/{id}', [TabCotizacionesSolicitudesController::class, 'getById']);
    Route::get('tabCotizacionesSolicitudes/getByIdDetalle/{id}', [TabCotizacionesSolicitudesController::class, 'getByIdDetalle']);
    Route::post('tabCotizacionesSolicitudes/register', [TabCotizacionesSolicitudesController::class, 'store']);
    Route::put('tabCotizacionesSolicitudes/update', [TabCotizacionesSolicitudesController::class, 'update']);
    Route::post('tabCotizacionesSolicitudes/delete', [TabCotizacionesSolicitudesController::class, 'delete']);

    Route::get('TabArchivosObservacionesDetalle/getAll', [TabArchivosObservacionesDetalleController::class, 'getAll']);
    Route::get('TabArchivosObservacionesDetalle/getById/{id}', [TabArchivosObservacionesDetalleController::class, 'getById']);
    Route::get('TabArchivosObservacionesDetalle/getByIDSolicitudDeta/{id}', [TabArchivosObservacionesDetalleController::class, 'getByIDSolicitudDeta']);
    Route::post('TabArchivosObservacionesDetalle/register', [TabArchivosObservacionesDetalleController::class, 'store']);
    Route::put('TabArchivosObservacionesDetalle/update/{id}', [TabArchivosObservacionesDetalleController::class, 'update']);
    Route::post('TabArchivosObservacionesDetalle/delete', [TabArchivosObservacionesDetalleController::class, 'delete']);

    //Ruta para tab observaciones solicitud detalle
    Route::get('TabObservacionesSolicitudReqInfo/getAll', [TabObservacionesSolicitudReqInfoController::class, 'getAll']);
    Route::get('TabObservacionesSolicitudReqInfo/getById/{id}', [TabObservacionesSolicitudReqInfoController::class, 'getById']);
    Route::get('TabObservacionesSolicitudReqInfo/getByIdSolicitud/{id}', [TabObservacionesSolicitudReqInfoController::class, 'getByIdSolicitud']);
    Route::post('TabObservacionesSolicitudReqInfo/register', [TabObservacionesSolicitudReqInfoController::class, 'store']);
    Route::put('TabObservacionesSolicitudReqInfo/update/{id}', [TabObservacionesSolicitudReqInfoController::class, 'update']);

    Route::get('TabArchivosObservacionesSolicitudReqInfo/getAll', [TabArchivosObservacionesSolicitudReqInfoController::class, 'getAll']);
    Route::get('TabArchivosObservacionesSolicitudReqInfo/getById/{id}', [TabArchivosObservacionesSolicitudReqInfoController::class, 'getById']);
    Route::get('TabArchivosObservacionesSolicitudReqInfo/getByIDSolicitudDeta/{id}', [TabArchivosObservacionesSolicitudReqInfoController::class, 'getByIDSolicitudDeta']);
    Route::post('TabArchivosObservacionesSolicitudReqInfo/register', [TabArchivosObservacionesSolicitudReqInfoController::class, 'store']);
    Route::put('TabArchivosObservacionesSolicitudReqInfo/update/{id}', [TabArchivosObservacionesSolicitudReqInfoController::class, 'update']);
    Route::post('TabArchivosObservacionesSolicitudReqInfo/delete', [TabArchivosObservacionesSolicitudReqInfoController::class, 'delete']);

    Route::get('solicitud/enviar/{id}', [SolicitudController::class, 'enviar']);
    Route::post('solicitud/asignar', [SolicitudController::class, 'asignar']);

    Route::post('solicitud/actualizarEstatus', [SolicitudController::class, 'actualizarEstatus']);

    Route::post('solicitud/subirFactura', [SolicitudController::class, 'subirFactura']);

    Route::post('solicitud/concluir', [SolicitudController::class, 'concluir']);

    Route::post('solicitud/revertir', [SolicitudController::class, 'revertir']);

    Route::post('solicitud/mandarFactura', [SolicitudController::class, 'mandarFactura']);
    Route::get('solicitud/getFacturaPDF/{id}', [SolicitudController::class, 'getFacturaPDF']);
    Route::get('solicitud/getFacturaXML/{id}', [SolicitudController::class, 'getFacturaXML']);

    Route::get('solicitud/getConsola', [SolicitudController::class, 'getConsola']);
    Route::get('solicitud/getMesaAyuda', [SolicitudController::class, 'getMesaAyuda']);
    Route::post('solicitud/getDashboard', [SolicitudController::class, 'getDashboard']);
    Route::put('solicitud/editarTicket/{id}', [SolicitudController::class, 'editarTicket']);

    Route::get('solicitud/calcularPrecio/{id}', [SolicitudController::class, 'calcularPrecio']);

    Route::get('user/validarCantidadRFC', [DatosFiscalesController::class, 'validarCantidadRFC']);
    Route::get('user/validarCantidadUsuarios', [UsuarioController::class, 'validarCantidadUsuarios']);

    Route::post('suscripcion/iniciar/{id}', [SuscripcionController::class, 'iniciar']);
    Route::get('suscripcion/getAll', [SuscripcionController::class, 'getAll']);

    // MovimientoSaldo
    Route::get('MovimientoSaldo/getAll', [MovimientoSaldoController::class, 'getAll']);
    Route::get('MovimientoSaldo/getMyMovimientos', [MovimientoSaldoController::class, 'getMyMovimientos']);
    Route::get('MovimientoSaldo/getById/{id}', [MovimientoSaldoController::class, 'getById']);
    Route::post('MovimientoSaldo/register', [MovimientoSaldoController::class, 'store']);
    Route::put('MovimientoSaldo/update/{id}', [MovimientoSaldoController::class, 'update']);
    Route::get('MovimientoSaldo/exportExcel', [MovimientoSaldoController::class, 'exportExcel']);
    Route::get('MovimientoSaldo/exportPdf', [MovimientoSaldoController::class, 'exportPdf']);

    Route::post('solicitud/getGeneralByUsuario', [SolicitudController::class, 'getGeneralByUsuario']);
    Route::post('solicitud/getByUsuario', [SolicitudController::class, 'getByUsuario']);
});
Route::post('stripeWebhook/handle', [StripeWebhookController::class, 'handle']);
Route::post('stripe/crearPagoByPrepago', [StripeController::class, 'crearPagoByPrepago']);
Route::post('stripe/crearPagoByMensual', [StripeController::class, 'crearPagoByMensual']);

Route::post('stripe/confirmStripePayment', [StripeController::class, 'confirmStripePayment']);

Route::post('stripe/confirmStripePaymentMensual', [StripeController::class, 'confirmStripePaymentMensual']);


//Facturacion

//Cat Estatus Sat
Route::get('CatEstatusSat/getAll', [CatEstatusSatController::class, 'getAll']);
Route::get('CatEstatusSat/getById/{id}', [CatEstatusSatController::class, 'getById']);
Route::post('CatEstatusSat/register', [CatEstatusSatController::class, 'store']);
Route::put('CatEstatusSat/update/{id}', [CatEstatusSatController::class, 'update']);

//Cat Regimenes Fiscales
Route::get('CatRegimenesFiscales/getAll', [CatRegimenesFiscalesController::class, 'getAll']);
Route::get('CatRegimenesFiscales/getById/{id}', [CatRegimenesFiscalesController::class, 'getById']);
Route::get('CatRegimenesFiscales/getByMoralOFisica/{esPersonaMoral}', [CatRegimenesFiscalesController::class, 'getByMoralOFisica']);
Route::post('CatRegimenesFiscales/register', [CatRegimenesFiscalesController::class, 'store']);
Route::put('CatRegimenesFiscales/update/{id}', [CatRegimenesFiscalesController::class, 'update']);

//Cat Tipo contrato
Route::get('CatTipoContacto/getAll', [CatTipoContactoController::class, 'getAll']);
Route::get('CatTipoContacto/getById/{id}', [CatTipoContactoController::class, 'getById']);
Route::post('CatTipoContacto/register', [CatTipoContactoController::class, 'store']);
Route::put('CatTipoContacto/update/{id}', [CatTipoContactoController::class, 'update']);

//Cat Tipo Direccion
Route::get('CatTipoDireccion/getAll', [CatTipoDireccionController::class, 'getAll']);
Route::get('CatTipoDireccion/getById/{id}', [CatTipoDireccionController::class, 'getById']);
Route::post('CatTipoDireccion/register', [CatTipoDireccionController::class, 'store']);
Route::put('CatTipoDireccion/update/{id}', [CatTipoDireccionController::class, 'update']);

//Tab Clientes
Route::get('TabClientes/getAll', [TabClientesController::class, 'getAll']);
Route::get('TabClientes/getById/{id}', [TabClientesController::class, 'getById']);
Route::post('TabClientes/register', [TabClientesController::class, 'store']);
Route::put('TabClientes/update/{id}', [TabClientesController::class, 'update']);

//Tab Clientes Fiscales
Route::get('TabClientesFiscales/getAll', [TabClientesFiscalesController::class, 'getAll']);
Route::get('TabClientesFiscales/getById/{id}', [TabClientesFiscalesController::class, 'getById']);
Route::post('TabClientesFiscales/register', [TabClientesFiscalesController::class, 'store']);
Route::put('TabClientesFiscales/update/{id}', [TabClientesFiscalesController::class, 'update']);

//Tab Contactos
Route::get('TabContacto/getAll', [TabContactoController::class, 'getAll']);
Route::get('TabContacto/getById/{id}', [TabContactoController::class, 'getById']);
Route::post('TabContacto/register', [TabContactoController::class, 'store']);
Route::put('TabContacto/update/{id}', [TabContactoController::class, 'update']);
Route::post('TabContacto/mandarSMS', [TabContactoController::class, 'mandarSMS']);

//Tab Direcciones
Route::get('TabDirecciones/getAll', [TabDireccionesController::class, 'getAll']);
Route::get('TabDirecciones/getById/{id}', [TabDireccionesController::class, 'getById']);
Route::post('TabDirecciones/register', [TabDireccionesController::class, 'store']);
Route::put('TabDirecciones/update/{id}', [TabDireccionesController::class, 'update']);

// EstatusMovimiento
Route::get('EstatusMovimiento/getAll', [EstatusMovimientoController::class, 'getAll']);
Route::get('EstatusMovimiento/getById/{id}', [EstatusMovimientoController::class, 'getById']);
Route::post('EstatusMovimiento/register', [EstatusMovimientoController::class, 'store']);
Route::put('EstatusMovimiento/update/{id}', [EstatusMovimientoController::class, 'update']);

// EstadoSolicitud
Route::get('EstadoSolicitud/getAll', [EstadoSolicitudController::class, 'getAll']);
Route::get('EstadoSolicitud/getById/{id}', [EstadoSolicitudController::class, 'getById']);
Route::post('EstadoSolicitud/register', [EstadoSolicitudController::class, 'store']);
Route::put('EstadoSolicitud/update/{id}', [EstadoSolicitudController::class, 'update']);

// Factura
Route::get('Factura/getAll', [FacturaController::class, 'getAll']);
Route::get('Factura/getById/{id}', [FacturaController::class, 'getById']);
Route::post('Factura/register', [FacturaController::class, 'store']);
Route::put('Factura/update/{id}', [FacturaController::class, 'update']);


// Precio
Route::get('Precio/getAll', [PrecioController::class, 'getAll']);
Route::get('Precio/getById/{id}', [PrecioController::class, 'getById']);
Route::post('Precio/register', [PrecioController::class, 'store']);
Route::put('Precio/update/{id}', [PrecioController::class, 'update']);

// Servicio
Route::get('Servicio/getAll', [ServicioController::class, 'getAll']);
Route::get('Servicio/getById/{id}', [ServicioController::class, 'getById']);
Route::post('Servicio/register', [ServicioController::class, 'store']);
Route::put('Servicio/update/{id}', [ServicioController::class, 'update']);

// Solicitud

Route::get('solicitud/getAll', [SolicitudController::class, 'getAll']);
Route::get('solicitud/procesar/{id}', [SolicitudController::class, 'procesar']);

Route::post('solicitud/rechazar', [SolicitudController::class, 'rechazar']);


Route::get('solicitud/eliminar/{id}', [SolicitudController::class, 'eliminar']);
Route::get('solicitud/obtenerImagen/{id}', [SolicitudController::class, 'obtenerImagen']);
Route::get('solicitud/getById/{id}', [SolicitudController::class, 'getById']);
Route::post('solicitud/register', [SolicitudController::class, 'store']);
Route::put('solicitud/update/{id}', [SolicitudController::class, 'update']);
Route::post('solicitud/actualizarReceptor', [SolicitudController::class, 'actualizarReceptor']);
Route::get('solicitud/getTodosDatos/{id}', [SolicitudController::class, 'getTodosDatos']);

Route::get('motivoRechazo/getAllActivo', [MotivoRechazoController::class, 'getAllActivo']);

Route::get('datosFiscales/getAll', [DatosFiscalesController::class, 'getAll']);
Route::get('datosFiscales/getById/{id}', [DatosFiscalesController::class, 'getById']);
Route::get('datosFiscales/getByUsr/{id}', [DatosFiscalesController::class, 'getByUsr']);
Route::post('datosFiscales/register', [DatosFiscalesController::class, 'store']);

Route::post('datosFiscales/eliminarReceptor/{id}', [DatosFiscalesController::class, 'eliminarReceptor']);

Route::post('datosFiscales/extraerDatosCFDI', [DatosFiscalesController::class, 'extraerDatosCFDI']);

Route::post('datosFiscales/registerCompleto', [DatosFiscalesController::class, 'storeCompleto']);
Route::put('datosFiscales/updateCompleto', [DatosFiscalesController::class, 'updateCompleto']);
Route::post('datosFiscales/registerConDomicilio', [DatosFiscalesController::class, 'storeConDomicilio']);
Route::put('datosFiscales/update/{id}', [DatosFiscalesController::class, 'update']);

//Apis para autenticacion 2Fa

Route::post('autenticacion2fa/generarqr', [AuthController::class, 'enable2FA']);
Route::post('autenticacion2fa/verifyqr', [AuthController::class, 'verify2FA']);

Route::get('catMontosPrepago/getAll', [CatMontosPrepagoController::class, 'getAll']);
Route::get('catMontosPrepago/getById/{id}', [CatMontosPrepagoController::class, 'getById']);

Route::get('catMontosPrepago/getByPlan/{id}', [CatMontosPrepagoController::class, 'getByPlan']);
Route::post('catMontosPrepago/register', [CatMontosPrepagoController::class, 'store']);
Route::put('catMontosPrepago/update/{id}', [CatMontosPrepagoController::class, 'update']);
Route::put('catMontosPrepago/activate/{id}', [CatMontosPrepagoController::class, 'activate']);
Route::put('catMontosPrepago/deactivate/{id}', [CatMontosPrepagoController::class, 'deactivate']);

Route::get('catPlanes/getAll', [CatPlanesController::class, 'getAll']);
Route::get('catPlanes/getAllVigentes', [CatPlanesController::class, 'getAllVigentes']);
Route::get('catPlanes/getById/{id}', [CatPlanesController::class, 'getById']);
Route::post('catPlanes/register', [CatPlanesController::class, 'store']);
Route::put('catPlanes/update/{id}', [CatPlanesController::class, 'update']);


Route::post('stripe/create-payment-intent', [StripeController::class, 'createPaymentIntent']);
