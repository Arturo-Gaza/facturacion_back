<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Catalogos\CatAlmacenesController;
use App\Http\Controllers\Catalogos\CatRolesController;
use App\Http\Controllers\Catalogos\CatUnidadMedidasController;
use App\Http\Controllers\UsuarioController;
use App\Http\Middleware\AcceptJsonMiddleware;
use App\Models\Catalogos\CatRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

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
Route::get('catUnidadMedidas/getAll', [CatUnidadMedidasController ::class, 'getAll']);
Route::get('catUnidadMedidas/getById/{id}', [CatUnidadMedidasController::class, 'getById']);
Route::post('catUnidadMedidas/register', [CatUnidadMedidasController::class, 'store']);
Route::put('catUnidadMedidas/update/{id}', [CatUnidadMedidasController::class, 'update']);

//Rutas publicaas
//Esta ruta cambiarla a privada
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);
//Route::get('parametroAll', [ParametroController::class,'index']);


Route::middleware(['auth:sanctum', AcceptJsonMiddleware::class])->group(function () {

    //Cierre de sesión
    Route::get('auth/logout/{id}', [AuthController::class, 'logout']);

    Route::get('usuario/getPermisosByUsuario/{id}', [UsuarioController::class, 'getPermisosByUsuario']);
    Route::get('usuario/getAll', [UsuarioController::class, 'getAll']);
    Route::put('usuario/update/{id}', [UsuarioController::class, 'update']);

});


