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

//Compradores
Route::get('buyer', 'BuyerController@index');
Route::get('buyer/{id}', 'BuyerController@show');
Route::post('buyer', 'BuyerController@store');
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
Route::post('reservation', 'ReservationController@store');
Route::get('reservation/{id}', 'ReservationController@show');
Route::put('reservation/{id}', 'ReservationController@update');
Route::delete('reservation/{id}', 'ReservationController@destroy');
