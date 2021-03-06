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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Auth
Route::group([
    'prefix' => 'auth',
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});

//Rutas protegidas
Route::group(['middleware' => ['apiJwt']], function () {

    //Compradores
    Route::get('buyer', 'BuyerController@index');
    Route::get('buyer/{id}', 'BuyerController@show');
    Route::put('buyer/{id}', 'BuyerController@update');
    Route::delete('buyer/{id}', 'BuyerController@destroy');

    //Conciertos
    Route::get('concert', 'ConcertController@index');
    Route::post('concert', 'ConcertController@store');
    Route::get('concert/{id}', 'ConcertController@show');
    Route::put('concert/{id}', 'ConcertController@update');
    Route::delete('concert/{id}', 'ConcertController@destroy');

    //Reservaciones
    Route::get('reservation', 'ReservationController@index');
    Route::get('reservation/{id}', 'ReservationController@show');
    Route::put('reservation/{id}', 'ReservationController@update');
    Route::delete('reservation/{id}', 'ReservationController@destroy');

});

//Crear Comprador
Route::post('buyer', 'BuyerController@store');

//Crear Reserva
Route::post('reservation', 'ReservationController@store');

