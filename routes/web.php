<?php

use App\Http\Controllers\BrdpController;
use App\Http\Controllers\BrexController;
use App\Http\Controllers\Controller;
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

Route::get('/', [Controller::class, 'index']);
// Route::get('/{anything}', [Controller::class, 'anything']);

Route::get('/brdp', [BrdpController::class, 'indexBrdp']);
Route::get('/brdp/{aircraft}', [BrdpController::class, 'table']);

Route::get('/brex', [BrexController::class, 'indexBrex']);
Route::get('/brex/{aircraft}', [BrexController::class, 'table']);

