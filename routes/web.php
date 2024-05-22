<?php

use App\Infrastructure\Controllers\CreateNewUserController;
use App\Infrastructure\Controllers\StreamsController;
use App\Infrastructure\Controllers\GetStreamersController;
use App\Infrastructure\Controllers\Topofthetops;
use Illuminate\Support\Facades\Route;

Route::get('/analytics/streamers', GetStreamersController::class);
Route::get('/analytics/streams', StreamsController::class);
Route::get('/analytics/topsofthetops', [Topofthetops::class, 'getTopOfTheTops']);
Route::post('/analytics/users', CreateNewUserController::class);
