<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\Csdb\DmcController;
use App\Http\Controllers\CsdbController;
use App\Http\Controllers\CsdbProcessingController;
use App\Http\Controllers\CsdbServiceController;
use App\Http\Controllers\ProjectController;
use App\Models\Csdb;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Route::get('/csdb', [CsdbController::class, 'index']);
// Route::get('/csdb/object/create', [CsdbController::class, 'getcreate'])->middleware('auth');
// Route::post("/csdb/object/create", [CsdbController::class, 'postcreate'])->middleware('auth')->name('get_create_csdb_object'); //create_csdb_object
// Route::get("/csdb/object/update", [CsdbController::class, 'getupdate'])->middleware('auth')->name('get_update_csdb_object');
// Route::post("/csdb/object/update", [CsdbController::class, 'postupdate'])->middleware('auth')->name('post_update_csdb_object'); // update_csdb_object
Route::get('/csdb/object/delete', [CsdbController::class, 'getdelete'])->middleware('auth')->name('get_delete_csdb_object');
Route::get('/csdb/object/restore', [CsdbController::class, 'getrestore'])->middleware('auth')->name('get_restore_csdb_object');
Route::post('/csdb/object/delete', [CsdbController::class, 'postdelete'])->middleware('auth')->name('post_delete_csdb_object');
// Route::get('/csdb/object/detail', [CsdbController::class, 'getdetail'])->middleware('auth')->name('get_detail_csdb_object');
// Route::post("/csdb/object/verify", [CsdbProcessingController::class, 'postverify'])->middleware('auth')->name('post_csdb_object_verify');


// for vue.js
Route::get('/csdb/{project?}/{name?}/{isUpdate?}', [CsdbController::class, 'general_index'])->middleware('auth');

Route::get('/api/csdb', [CsdbController::class, 'getcsdbdata'])->middleware('auth')->name('api.get_csdb_object_data');
Route::get('/api/getobject', [CsdbController::class, 'getcsdb'])->middleware('auth')->name('api.getobject');
Route::post("/api/csdb/object/create", [CsdbController::class, 'postcreate2'])->middleware('auth')->name('api.post_create_csdb_object'); //create_csdb_object
Route::post("api/csdb/object/update", [CsdbController::class, 'postupdate2'])->middleware('auth')->name('api.post_update_csdb_object'); // update_csdb_object
Route::post("/api/csdb/object/verify", [CsdbProcessingController::class, 'postverify2'])->middleware('auth')->name('api.post_csdb_object_verify');

###### service ######
Route::get("/api/csdb/{project_name}/{filename}", [CsdbServiceController::class, 'provide_csdb_transform2'])->middleware('auth')->name('api.get_transform_csdb');
Route::get("/api/{projectName}/{filename}/pdf", [CsdbServiceController::class, 'provide_csdb_pdf'])->middleware('auth')->name('api.pdf_csdb');
Route::get("/api/repo", [RepoController::class, 'getindex2'])->middleware('auth')->name('api.get_repo_index');

### NOTE
/**
 * harusnya route name 'api.getobject' sama dengan provide_csdb_transform2 jika yang dikelarkan adalah ICN.
 * sehingga frontend tidak perlu request 2x jika ingin mengupdate ICN, dari page detailObject->updateObject.
 */