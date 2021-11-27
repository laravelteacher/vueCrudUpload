<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// get data from this router
Route::get('photo', 'App\Http\Controllers\API\PhotoController@indexx');
// save data by this router
Route::post('st', 'App\Http\Controllers\API\PhotoController@store');
// update
Route::put('update/{id}', 'App\Http\Controllers\API\PhotoController@update');
// remove
Route::delete('delete/{id}', 'App\Http\Controllers\API\PhotoController@destroy');
