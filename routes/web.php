<?php

use App\Http\Controllers\BrdpController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DmoduleController;
use App\Http\Controllers\BrexController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;
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
Route::get('/', [Controller::class, 'index'])->name('welcome');

Route::get('/dashboard', fn() => view('dashboard'))->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// sudah diganti dengan vue js
Route::get('/brdp', [BrdpController::class, 'indexBrdp']);
// Route::get('/brdp/{aircraft}', [BrdpController::class, 'table']);

// sudah diganti dengan vue js
Route::get('/brex', [BrexController::class, 'indexBrex']);
// Route::get('/brex/{aircraft}', [BrexController::class, 'table']);

// Route::get('/refreshLocalStorage', function(){
//   return view('general.refreshLocalStorage');
// });
// Route::get("/dmodule", [DmoduleController::class, 'indexDmodule']);
// Route::post("/dmodule/validate", [DmoduleController::class, 'validate'])->name('validate-dmodule');

// Route::get('tesxsl', function(){
//   return view('general.test.testxsl');
// });

// require __Dir__."/fileRequest.php";

// Route::get('/editor', function(){
//   return view('coba.editor');
// });

// require __Dir__."/tcpdf/tcpdf.php";
// require __Dir__."/csdb/general.php";
// require __Dir__."/csdb/service.php";
// require __Dir__."/project/general.php";
// require __Dir__."/ietm/general.php";
// require __Dir__."/ietm/repo.php";
require __Dir__."/csdb4/general.php";
require __Dir__."/csdb4/dml.php";
require __Dir__."/csdb4/comment.php";
require __Dir__."/csdb4/ddn.php";
require __Dir__."/csdb4/dmc.php";
// require __Dir__."/csdb3/general.php";
// require __Dir__."/dml/general.php";
// require __Dir__."/br/general.php";
// require __Dir__."/dm/general.php";
// require __Dir__."/icn/general.php";

Route::get('/auth/check', [Controller::class, 'authcheck'])->middleware('auth'); // berguna untuk vue
Route::get('/route/{name}', [Controller::class, 'route']); // masih digunakan di xsl
Route::get('/getAllRoutes', [Controller::class, 'getAllRoutesNamed']); // berguna untuk vue

Route::get("/api/info/{name}", function(Request $request, string $name){
  $content = file_get_contents(resource_path("notes/info/{$name}.md"));
  if($content){
    $status = 200;
    $contentType = "text/markdown";
  }
  return Response::make($content ?? '',$status ?? 400,[
    "Content-Type" => $contentType ?? "text/plain"
  ]);
})->middleware('auth')->name('api.info');

Route::get("/api/alert/{name}", function(Request $request, string $name){
  $content = file_get_contents(resource_path("notes/alert/{$name}.md"));
  if($content){
    $status = 200;
    $contentType = "text/markdown";
  }
  return Response::make($content ?? '',$status ?? 400,[
    "Content-Type" => $contentType ?? "text/plain"
  ]);
})->middleware('auth')->name('api.alert');

Route::get('/worker/{filename}', [Controller::class, 'getWorker'])->middleware('auth');
// Route::get('/axios', [Controller::class, 'getAxiosJs']);