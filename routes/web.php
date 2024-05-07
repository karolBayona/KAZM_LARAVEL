<?php

use App\Infrastructure\Controllers\StreamsController;
use App\Infrastructure\Controllers\UserController;
use App\Infrastructure\Controllers\Topofthetops;
use Illuminate\Support\Facades\Route;

Route::get('/analytics/users', UserController::class);
Route::get('/analytics/streams', StreamsController::class);
Route::get('/analytics/topsofthetops', [Topofthetops::class, 'getTopOfTheTops']);
