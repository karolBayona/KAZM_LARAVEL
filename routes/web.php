<?php


use App\Http\Controllers\StreamsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\Topofthetops;
use Illuminate\Support\Facades\Route;

Route::get('/analytics/users',UsersController::class);
Route::get('/analytics/streams', StreamsController::class);
Route::get('/analytics/topsofthetops', [Topofthetops::class, 'getTopOfTheTops']);
