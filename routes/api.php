<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Catalogos\Egresos\CatClasificacionAfectacionController;
use App\Http\Controllers\Catalogos\Egresos\CatActividadInstitucionalController;
use App\Http\Controllers\Catalogos\Egresos\CatCapitulosController;
use App\Http\Controllers\Catalogos\Egresos\CatConceptosController;
use App\Http\Controllers\Catalogos\Egresos\CatEstadoAfectacionController;
use App\Http\Controllers\Catalogos\Egresos\CatFinalidadController;
use App\Http\Controllers\Catalogos\Egresos\CatFuentesFinanciamientoController;
use App\Http\Controllers\Catalogos\Egresos\CatFuncionesController;
use App\Http\Controllers\Catalogos\Egresos\CatObjetoGastosController;
use App\Http\Controllers\Catalogos\Egresos\CatProgramasPresupuestariosController;
use App\Http\Controllers\Catalogos\Egresos\CatRamosController;
use App\Http\Controllers\Catalogos\Egresos\CatSubfuncionesController;
use App\Http\Controllers\Catalogos\Egresos\CatTipoAfectacionController;
use App\Http\Controllers\Catalogos\Egresos\CatTipoGastosController;
use App\Http\Controllers\Catalogos\Egresos\CatTipoMovimientoController;
use App\Http\Controllers\Catalogos\Egresos\CatTipoRamosController;
use App\Http\Controllers\Catalogos\Usuarios\CatAreasController;
use App\Http\Controllers\Catalogos\Usuarios\CatEntidadesFederativasController;
use App\Http\Controllers\Catalogos\Usuarios\CatUnidadesResponsablesController;
use App\Http\Controllers\ParametroController;
use App\Http\Controllers\CatSistemaController;
use App\Http\Controllers\UsuarioSistemaController;
use App\Http\Controllers\PermisoContoller;
use App\Http\Controllers\UsuarioRolController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use App\Http\Middleware\AcceptJsonMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

//Rutas publicaas
//Esta ruta cambiarla a privada
Route::post('auth/login', [AuthController::class, 'login']);
//Route::get('parametroAll', [ParametroController::class,'index']);


Route::middleware(['auth:sanctum', AcceptJsonMiddleware::class])->group(function () {

    //Cierre de sesión
    Route::get('auth/logout/{id}', [AuthController::class, 'logout']);
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::get('usuario/getPermisosByUsuario/{id}', [UsuarioController::class, 'getPermisosByUsuario']);
    Route::get('usuario/getAll', [UsuarioController::class, 'getAll']);
    Route::put('usuario/update/{id}', [UsuarioController::class, 'update']);

});


