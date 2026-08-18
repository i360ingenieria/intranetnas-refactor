
<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ArchivoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PlatformController;

Route::get('/dashboard', [DashboardController::class, 'index']);
Route::get('/buscador', [ArchivoController::class, 'buscar']);
Route::get('/platforms', [PlatformController::class, 'index']);