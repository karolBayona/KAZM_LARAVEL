<?php

//use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
    //return view('welcome');
//});


use App\Http\Controllers\Streams;
use App\Http\Controllers\Users;

// Define una ruta que captura 'id' como parámetro de la ruta
Route::get('/analytics/users', [Users::class, 'getUser']);

Route::get('/analytics/streams', [Streams::class, 'getStreams']);

