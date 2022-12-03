<?php

use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RequestController;
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

// routes to handle sent/received/withdrawn requests
Route::resource('request', RequestController::class);

// handle connections requests
Route::get('/', [ConnectionController::class, 'index']);
Route::get('connectionsInCommon', [ConnectionController::class, 'getMoreConnectionsInCommon']);
