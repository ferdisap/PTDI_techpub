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

// Route::get('/ms', [CsdbController::class, 'general_index']);
// Route::get('/ms/{project?}', [CsdbController::class, 'general_index'])->middleware('auth');
// Route::get('/ms/{project?}/{name?}', [CsdbController::class, 'general_index'])->middleware('auth');
Route::get('/ms/{project?}/{name?}/{isUpdate?}', [CsdbController::class, 'general_index'])->middleware('auth');

Route::get('/api/csdb', [CsdbController::class, 'getallcsdb'])->middleware('auth')->name('api.get_csdb_object_all');
Route::get('/api/getobject', [CsdbController::class, 'getcsdb'])->middleware('auth')->name('api.getobject');
Route::post("api/csdb/object/update", [CsdbController::class, 'postupdate2'])->middleware('auth')->name('api.post_update_csdb_object'); // update_csdb_object

// Route::get('/csdb/object/create', [CsdbController::class, 'getcreate'])->middleware('auth');

// Route::post("/csdb/object/create", [CsdbController::class, 'postcreate'])->middleware('auth')->name('get_create_csdb_object'); //create_csdb_object

// Route::get("/csdb/object/update", [CsdbController::class, 'getupdate'])->middleware('auth')->name('get_update_csdb_object');

// Route::post("/csdb/object/update", [CsdbController::class, 'postupdate'])->middleware('auth')->name('post_update_csdb_object'); // update_csdb_object

// Route::get('/csdb/object/delete', [CsdbController::class, 'getdelete'])->middleware('auth')->name('get_delete_csdb_object');

// Route::get('/csdb/object/restore', [CsdbController::class, 'getrestore'])->middleware('auth')->name('get_restore_csdb_object');

// Route::post('/csdb/object/delete', [CsdbController::class, 'postdelete'])->middleware('auth')->name('post_delete_csdb_object');

// Route::get('/csdb/object/detail', [CsdbController::class, 'getdetail'])->middleware('auth')->name('get_detail_csdb_object');

// Route::post("/csdb/object/verify", [CsdbProcessingController::class, 'postverify'])->middleware('auth')->name('post_csdb_object_verify');
