<?php

use App\Infrastructure\Controllers\CreateNewUserController;
use App\Infrastructure\Controllers\FollowStreamerController;
use App\Infrastructure\Controllers\GetUsersListController;
use App\Infrastructure\Controllers\StreamsController;
use App\Infrastructure\Controllers\GetStreamersController;
use App\Infrastructure\Controllers\TopOfTheTopsController;
use Illuminate\Support\Facades\Route;

Route::get('/analytics/streamers', GetStreamersController::class);
Route::get('/analytics/streams', StreamsController::class);
Route::get('/analytics/topsofthetops', TopOfTheTopsController::class);
Route::post('/analytics/users', CreateNewUserController::class);
Route::get('/analytics/users', GetUsersListController::class);
Route::post('/analytics/follow', FollowStreamerController::class);
