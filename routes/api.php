<?php

use App\Http\Controllers\ActualizarEstatusAsignacionController;
use App\Http\Controllers\ArchivoCarga\ActualizarStatusController;
use App\Http\Controllers\ArchivoCarga\InsertarArchivoController;
use App\Http\Controllers\ArchivoCarga\InsertarFaltantesCatController;
use App\Http\Controllers\ArchivoCarga\ObtenerCargaIdController;
use App\Http\Controllers\ArchivoCarga\TabArchivoDetalleController;
use App\Http\Controllers\ArchivoCarga\TabDetalleCargaController;
use App\Http\Controllers\ArchivoCarga\TabObservacionesController;
use App\Http\Controllers\ArchivoCompletoController;
use App\Http\Controllers\ArchivoCompletoDetalleController;
use App\Http\Controllers\ArchivoConteo\TabConteoController;
use App\Http\Controllers\AsignacionCarga\TabAsignacionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\cargaArchivoController;
use App\Http\Controllers\Catalogos\CatAlmacenesController;
use App\Http\Controllers\Catalogos\CatGpoFamiliaController;
use App\Http\Controllers\Catalogos\CatProductosController;
use App\Http\Controllers\Catalogos\CatRolesController;
use App\Http\Controllers\Catalogos\CatUnidadMedidasController;
use App\Http\Controllers\DetalleArchivoController;
use App\Http\Controllers\noInsertarFaltantesController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\UsuarioDetalleCargaController;
use App\Http\Middleware\AcceptJsonMiddleware;
use App\Models\ArchivoCarga\tab_detalle_carga;
use App\Models\ArchivoConteo\TabConteo;
use Illuminate\Support\Facades\Route;

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::get('cargaArchivo/{id}', [ObtenerCargaIdController::class, 'getByDetalleCargaId']);
Route::put('ActualizarStatus/{id}', [ActualizarStatusController::class, 'actualizarEstatus']);

Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);
Route::get('cargas-usuario/{id_usuario}', [ArchivoCompletoController::class, 'getCargasByUsuario']);

Route::post('/insertarArchivo', [InsertarArchivoController::class, 'insertarArchivo']);

//Ruta para insertar datos faltantes
Route::get('/obtenerID', [cargaArchivoController::class, 'obtenerNuevoId']);
Route::get('detalleUsuarioAsignacion/{idUser}', [UsuarioDetalleCargaController::class, 'UsuarioDetalleCarga']);




Route::middleware(['auth:sanctum', AcceptJsonMiddleware::class])->group(function () {

    //Cierre de sesión
    Route::get('auth/logout/{id}', [AuthController::class, 'logout']);

    Route::get('usuario/getById/{id}', [UsuarioController::class, 'getById']);
    Route::get('usuario/getAll', [UsuarioController::class, 'getAll']);
    Route::put('usuario/update/{id}', [UsuarioController::class, 'update']);

    //CATALOGOS
    //Rutas de catalogo Roles
    Route::get('catRoles/getAll', [CatRolesController::class, 'getAll']);
    Route::get('catRoles/getById/{id}', [CatRolesController::class, 'getById']);
    Route::post('catRoles/register', [CatRolesController::class, 'store']);
    Route::put('catRoles/update/{id}', [CatRolesController::class, 'update']);

    //Rutas de catalogo Almacenes
    Route::get('catAlmacenes/getAll', [CatAlmacenesController::class, 'getAll']);
    Route::get('catAlmacenes/getById/{id}', [CatAlmacenesController::class, 'getById']);
    Route::post('catAlmacenes/register', [CatAlmacenesController::class, 'store']);
    Route::put('catAlmacenes/update/{id}', [CatAlmacenesController::class, 'update']);

    //Rutas Catalogo Unidad de Medida
    Route::get('catUnidadMedidas/getAll', [CatUnidadMedidasController::class, 'getAll']);
    Route::get('catUnidadMedidas/getById/{id}', [CatUnidadMedidasController::class, 'getById']);
    Route::post('catUnidadMedidas/register', [CatUnidadMedidasController::class, 'store']);
    Route::put('catUnidadMedidas/update/{id}', [CatUnidadMedidasController::class, 'update']);

    //Rutas Catalogo Grupo familia
    Route::get('catGpoFamilia/getAll', [CatGpoFamiliaController::class, 'getAll']);
    Route::get('catGpoFamilia/getById/{id}', [CatGpoFamiliaController::class, 'getById']);
    Route::post('catGpoFamilia/register', [CatGpoFamiliaController::class, 'store']);
    Route::put('catGpoFamilia/update/{id}', [CatGpoFamiliaController::class, 'update']);

    //Rutas Catalogo Productos
    Route::get('catProductos/getAll', [CatProductosController::class, 'getAll']);
    Route::get('catProductos/getById/{id}', [CatProductosController::class, 'getById']);
    Route::post('catProductos/register', [CatProductosController::class, 'store']);
    Route::put('catProductos/update/{id}', [CatProductosController::class, 'update']);
    Route::get('catProductos/getAllPersonalizado', [CatProductosController::class, 'getAllPersonalizado']);

    //Ruta api archivo csv

    //Rutas Carga detalle
    Route::get('tabCargaDetalle/getAll', [TabDetalleCargaController::class, 'getAll']);
    Route::get('tabCargaDetalle/getById/{id}', [TabDetalleCargaController::class, 'getById']);
    Route::post('tabCargaDetalle/register', [TabDetalleCargaController::class, 'store']);
    Route::put('tabCargaDetalle/update/{id}', [TabDetalleCargaController::class, 'update']);
    Route::put('tabCargaDetalle/updateConteo/{id}', [TabDetalleCargaController::class, 'updateConte']);
    Route::put('tabCargaDetalle/updateValidarCierre', [TabDetalleCargaController::class, 'ValidarCierre']);
    Route::get('tabCargaDetalle/ValidarCierreUsuarios/{idCarga}', [TabDetalleCargaController::class, 'ValidarCierreUsuarios']);

    //Rutas detalle archivo
    Route::get('tabDetalleArchivo/getAll', [TabArchivoDetalleController::class, 'getAll']);
    Route::get('tabDetalleArchivo/getById/{id}', [TabArchivoDetalleController::class, 'getById']);
    Route::post('tabDetalleArchivo/register', [TabArchivoDetalleController::class, 'store']);
    Route::put('tabDetalleArchivo/update/{id}', [TabArchivoDetalleController::class, 'update']);

    //Rutas Tabla Conteo
    Route::get('TabConteo/getById/{id}', [TabConteoController::class, 'getById']);
    Route::post('TabConteo/register', [TabConteoController::class, 'store']);
    Route::get('TabConteo/getAll', [TabConteoController::class, 'getAll']);
    Route::put('TabConteo/update/{id}', [TabConteoController::class, 'update']);
    Route::get('TabConteo/getByIDCargaIDUser/{idCarga}/{idUser}', [TabConteoController::class, 'getByIDCargaIDUser']);
    Route::get('TabConteo/getByIDCarga/{idCarga}', [TabConteoController::class, 'getByIDCarga']);
    Route::get('TabConteo/getConteos/{idCarga}', [TabConteoController::class, 'getConteos']);
    Route::delete('TabConteo/deleteConteoAllByIDCargaIDUser/{idCarga}/{idUser}/{conteo}', [TabConteoController::class, 'DeleteAll']);

    //Rutas Tabla Asignacion
    Route::get('TabAsignacion/getById/{id}', [TabAsignacionController::class, 'getById']);
    Route::post('TabAsignacion/Asignacion', [TabAsignacionController::class, 'Asignacion']);
    Route::post('TabAsignacion/Designacion/{idUserDesig}', [TabAsignacionController::class, 'Designacion']);
    Route::post('TabAsignacion/register', [TabAsignacionController::class, 'store']);
    Route::get('TabAsignacion/getAll', [TabAsignacionController::class, 'getAll']);
    Route::put('TabAsignacion/update/{id}', [TabAsignacionController::class, 'update']);
    Route::put('TabAsignacion/CerrarAll/{idCarga}', [TabAsignacionController::class, 'CerrarAll']);
    Route::put('TabAsignacion/NuevoConteoAsignacion/{idCarga}', [TabAsignacionController::class, 'NuevoConteoAsignacion']);
    Route::get('TabAsignacion/getAllPersonalizado/{idCarga}/{idUsuario}', [TabAsignacionController::class, 'getByIdCargaIdUserPer']);
    Route::put('actualizarEstatus/{idUser}/{idCarga}', [ActualizarEstatusAsignacionController::class, 'actualizarEstatus']);
    Route::put('actualizarEstatusFechaInicio/{idUser}/{idCarga}', [ActualizarEstatusAsignacionController::class, 'actualizarEstatusFechaInicio']);
    Route::put('actualizarEstatusFechaFin/{idUser}/{idCarga}', [ActualizarEstatusAsignacionController::class, 'actualizarEstatusFechaFin']);

    Route::post('detalleArchivo/{idUser}', [DetalleArchivoController::class, 'detalleArchivo']);  //CARGAR CABECERA DETALLE_1
    Route::post('cargarArchivoCompleto/{idCargar}', [cargaArchivoController::class, 'cargarArchivoCompleto']); //CARGAR ARCHIVOS FALTANTES_3
    Route::post('InsertarDatos', [InsertarFaltantesCatController::class, 'procesoInsertar']); //INSERTAR PRODUCTOS_2
    Route::post('noInsertarFaltantes/{idUser}', [noInsertarFaltantesController::class, 'detalleArchivo']);

    Route::post('/process-csv', [cargaArchivoController::class, 'processCsv']);
    Route::post('nombreArchivoExi', [cargaArchivoController::class, 'archivoRepetido']);

    Route::get('usuario/getAllUserAlmacen/{idCarga}', [UsuarioController::class, 'getAllUserAlmacen']);
    Route::get('usuario/getAllUser', [UsuarioController::class, 'getAllUser']);
    Route::get('usuario/getAllUserAsignado/{idCarga}', [UsuarioController::class, 'getAllUserAsignado']);

    Route::get('detalleUsuarioAsignacion/{idUser}', [UsuarioDetalleCargaController::class, 'UsuarioDetalleCarga']);

    //Ruta para tabla Observaciones
    Route::get('tabObsercaiones/getAll', [TabObservacionesController::class, 'getAll']);
    Route::get('tabObsercaiones/getById/{id}', [TabObservacionesController::class, 'getById']);
    Route::get('tabObsercaiones/getByIdCarga/{idCarga}', [TabObservacionesController::class, 'getByIDCarga']);
    Route::get('tabObsercaiones/getByIdCargaIdUser/{idCarga}/{idUser}', [TabObservacionesController::class, 'getByIDCargaIDUser']);
    Route::post('tabObsercaiones/register', [TabObservacionesController::class, 'store']);
    Route::put('tabObsercaiones/update/{id}', [TabObservacionesController::class, 'update']);

    //Ruta para insertar catalogos y cargar completas
    Route::post('detalleUsuarioAsignacionCompleto/{idUser}', [ArchivoCompletoDetalleController::class, 'detalleArchivo']);
});
