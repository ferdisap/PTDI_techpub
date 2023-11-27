<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\Csdb\DmcController;
use App\Http\Controllers\CsdbController;
use App\Http\Controllers\CsdbProcessingController;
use App\Http\Controllers\ProjectController;
use App\Models\Csdb;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/csdb', [CsdbController::class, 'index']);

Route::get('/csdb/object/create', [CsdbController::class, 'getcreate'])->middleware('auth');

Route::post("/csdb/object/create", [CsdbController::class, 'postcreate'])->middleware('auth')->name('get_create_csdb_object'); //create_csdb_object

Route::get("/csdb/object/update", [CsdbController::class, 'getupdate'])->middleware('auth')->name('get_update_csdb');

Route::post("/csdb/object/update", [CsdbController::class, 'postupdate'])->middleware('auth')->name('get_update_csdb_object'); // update_csdb_object

Route::post('/csdb/object/delete', [CsdbController::class, 'delete'])->middleware('auth')->name('delete_csdb_object');

Route::get('/csdb/object/detail', [CsdbController::class, 'getdetail'])->middleware('auth')->name('get_detail_csdb_object');

Route::post("/csdb/object/verify", [CsdbProcessingController::class, 'postverify'])->middleware('auth')->name('post_csdb_object_verify');

// Route::get('/csdb/object/request', [CsdbController::class, 'request'])->middleware('auth')->name('request_csdb_object');

// Route::get('/require/csdbjs', function(Request $request){
//   return response(File::get(resource_path("js/csdb/{$request->get('filename')}")),200,[
//     'Content-Type' => "application/javascript"
//   ]);
// })->name("get_csdb_js");

// Route::get('dump', function(){
//   $lists = Controller::get_file(storage_path('app/csdb'));
//   foreach($lists as $obj){
//     if(!Csdb::where('path',"csdb/{$obj}")->latest('updated_at')->first('id')){
//       Csdb::create([
//         'path' => "csdb/{$obj}",
//         'description' => 'none',
//         'status' => "seeded",
//         'initiator_id' => 1,
//       ]);
//     }
//   }
// });