<?php

use App\Http\Controllers\BrdpController;
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

Route::get('/', function () {
    return view('welcome_N219_techpub');
});

Route::get('/brdp', [BrdpController::class, 'index']);
Route::get('/brdp/{aircraft}', [BrdpController::class, 'detail']);

// Route::get('/brdp/{aircraft}', function ($aircraft) {
//   return view('brdp/brdp_' . $aircraft, [
//     'title' => 'brdp ' . $aircraft
//   ]);
// });
