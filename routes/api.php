<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
/* RUTAS DE HISTORIAL */
Route::get('history/get', [\App\Http\Controllers\HistoryController::class, 'getHistory']);

/* RUTAS DE BANCOS Y ENTIDADES */
Route::get('entity/get-bcv', [\App\Http\Controllers\BankingEntityController::class, 'getBcv']);
Route::get('entity/list-entities', [\App\Http\Controllers\BankingEntityController::class, 'getRateList']);

/* Crear observación */
Route::post('observation/create', [\App\Http\Controllers\ObservationController::class, 'createObservation']);

/* obtener version */
Route::get('app-config/get-version', [\App\Http\Controllers\AppConfigDailyCoinController::class, 'getVersion']);
