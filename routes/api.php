<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RestController;

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

Route::controller(AuthController::class)->group(function () {
	Route::post('login', 'login');
    Route::post('registro-cliente', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');

});

Route::controller(RestController::class)->group(function () {
	Route::post('recarga-billetera', 'Wallet');
    Route::get('consultar-billetera', 'Balance');
    Route::post('pagar-billetera', 'Pay');
    Route::get('verificar/{code}/{token}', 'Verify');

});
