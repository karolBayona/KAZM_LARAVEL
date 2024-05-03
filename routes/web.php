<?php


use App\Http\Controllers\StreamsController;
use App\Http\Controllers\Users;
use App\Http\Controllers\Topofthetops;
use Illuminate\Support\Facades\Route;

Route::get('/analytics/users', [Users::class, 'getUser']);
Route::get('/analytics/streams', StreamsController::class);
Route::get('/analytics/topsofthetops', [Topofthetops::class, 'getTopOfTheTops']);
