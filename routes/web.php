<?php


use App\Infrastructure\Controllers\StreamsController;
use App\Infrastructure\Controllers\UsersController;
use App\Infrastructure\Controllers\Topofthetops;
use Illuminate\Support\Facades\Route;

Route::get('/analytics/users',UsersController::class);
Route::get('/analytics/streams', StreamsController::class);
Route::get('/analytics/topsofthetops', [Topofthetops::class, 'getTopOfTheTops']);
