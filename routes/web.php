<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/agregar', 'App\\Http\\Controllers\\ProductoController@agregarProducto')->name('buscar-producto');
Route::post('/agregar-buscar-producto', 'App\\Http\\Controllers\\ProductoController@agregarBuscar')->name('agregare-producto');