<?php

use App\Http\Controllers\BrdpController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DmoduleController;
use App\Http\Controllers\BrexController;
use App\Http\Controllers\ProfileController;
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

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [Controller::class, 'index']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/brdp', [BrdpController::class, 'indexBrdp']);
Route::get('/brdp/{aircraft}', [BrdpController::class, 'table']);

Route::get('/brex', [BrexController::class, 'indexBrex']);
Route::get('/brex/{aircraft}', [BrexController::class, 'table']);

Route::get('/refreshLocalStorage', function(){
  return view('general.refreshLocalStorage');
});

Route::get("/dmodule", [DmoduleController::class, 'indexDmodule']);
Route::post("/dmodule/validate", [DmoduleController::class, 'validate'])->name('validate-dmodule');

Route::get('tesxsl', function(){
  return view('general.test.testxsl');
});

require __DIR__.'/csdb/dml.php';

require __Dir__."/fileRequest.php";
