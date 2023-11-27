<?php

use App\Http\Controllers\BrdpController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DmoduleController;
use App\Http\Controllers\BrexController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\File;
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
require __DIR__.'/csdb/dmc.php';

require __Dir__."/fileRequest.php";

Route::get('/editor', function(){
  return view('coba.editor');
});

// require __Dir__."/tcpdf/tcpdf.php";
require __Dir__."/csdb/general.php";
require __Dir__."/csdb/service.php";
require __Dir__."/project/general.php";





Route::get('/teshtml2pdf', function() {
  // require base_path(). DIRECTORY_SEPARATOR. "vendor". DIRECTORY_SEPARATOR. "spipu".DIRECTORY_SEPARATOR. "html2pdf". DIRECTORY_SEPARATOR. "examples" . DIRECTORY_SEPARATOR. "about.php";
  // require base_path(). "/vendor/spipu/html2pdf/examples/about.php";
  // dd(dirname(__FILE__), __FILE__, public_path());
  // try {
  //   $html2pdf = new Html2Pdf('P', 'A4', 'fr', true, 'UTF-8', array(0, 0, 0, 0));
  //   $html2pdf->pdf->SetDisplayMode('fullpage');

  //   ob_start();
  //   // include dirname(__FILE__).'/res/about.php';
  //   include public_path(). '/examples/res/about.php';
  //   $content = ob_get_clean();

  //   $html2pdf->writeHTML($content);
  //   $html2pdf->createIndex('Sommaire', 30, 12, false, true, 2, null, '10mm');
  //   $html2pdf->output('about.pdf');
  // } catch (Html2PdfException $e) {
  //   $html2pdf->clean();

  //   $formatter = new ExceptionFormatter($e);
  //   echo $formatter->getHtmlMessage();
  // }
  //   try {
  //     ob_start();
  //     include public_path().'/examples/res/example13.php';
  //     $content = ob_get_clean();

  //     $html2pdf = new Html2Pdf('P', 'A4', 'fr');
  //     $html2pdf->writeHTML($content);
  //     $html2pdf->output('example13.pdf');
  // } catch (Html2PdfException $e) {
  //     $html2pdf->clean();

  //     $formatter = new ExceptionFormatter($e);
  //     echo $formatter->getHtmlMessage();
  // }
});
