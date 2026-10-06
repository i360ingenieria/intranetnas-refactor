
<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ArchivoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PlatformController;
use App\Http\Controllers\FichatecfController;

Route::get('/dashboard', [DashboardController::class, 'index']);
Route::get('/buscador', [ArchivoController::class, 'buscar']);
Route::get('/platforms', [PlatformController::class, 'index']);
Route::get(
    '/buscador-global',
    [ArchivoController::class, 'buscarGlobal']
);
Route::get(
    '/buscador-globalf',
    [FichatecfController::class, 'buscarGlobalf']
);