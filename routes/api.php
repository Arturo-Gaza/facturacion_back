<?php

use App\Http\Controllers\ArchivoCarga\TabDetalleCargaController;
use App\Http\Controllers\ArchivoConteo\TabConteoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\cargaArchivoController;
use App\Http\Controllers\Catalogos\CatAlmacenesController;
use App\Http\Controllers\Catalogos\CatGpoFamiliaController;
use App\Http\Controllers\Catalogos\CatProductosController;
use App\Http\Controllers\Catalogos\CatRolesController;
use App\Http\Controllers\Catalogos\CatUnidadMedidasController;
use App\Http\Controllers\UsuarioController;
use App\Http\Middleware\AcceptJsonMiddleware;
use App\Models\ArchivoCarga\tab_detalle_carga;
use App\Models\ArchivoConteo\TabConteo;
use Illuminate\Support\Facades\Route;

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('auth/login', [AuthController::class, 'login']);

//Rutas Tabla Conteo
Route::get('TabConteo/getById/{id}', [TabConteoController::class, 'getById']);
Route::post('TabConteo/register', [TabConteoController::class, 'store']);
Route::get('TabConteo/getAll', [TabConteoController::class, 'getAll']);
Route::put('TabConteo/update/{id}', [TabConteoController::class, 'update']);


Route::middleware(['auth:sanctum', AcceptJsonMiddleware::class])->group(function () {

    //Cierre de sesión
    Route::get('auth/logout/{id}', [AuthController::class, 'logout']);
    Route::post('auth/register', [AuthController::class, 'register']);
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



    //Ruta api archivo csv
    Route::post('/process-csv', [cargaArchivoController::class, 'processCsv']);

    //Rutas Carga detalle
    Route::get('tabCargaDetalle/getAll', [TabDetalleCargaController::class, 'getAll']);
    Route::get('tabCargaDetalle/getById/{id}', [TabDetalleCargaController::class, 'getById']);
    Route::post('tabCargaDetalle/register', [TabDetalleCargaController::class, 'store']);
    Route::put('tabCargaDetalle/update/{id}', [TabDetalleCargaController::class, 'update']);
});
