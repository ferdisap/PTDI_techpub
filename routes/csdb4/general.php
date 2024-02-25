<?php

use App\Http\Controllers\CsdbController;
use App\Http\Controllers\CsdbServiceController;
use App\Http\Controllers\DmlController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::get("/csdb4/{view?}",[CsdbController::class, 'app'])->where('view','(.*)')->middleware('auth');
Route::get("/api/allobjects",[CsdbController::class, 'get_allobjects_list'])->middleware('auth')->name('api.get_allobjects_list');
Route::get("/api/object/all",[CsdbController::class, 'get_objects_list'])->middleware('auth')->name('api.get_objects_list');


Route::get("/api/csdbtransform/{filename}", [CsdbServiceController::class, 'provide_csdb_transform3'])->middleware('auth')->name('api.transform_csdb');
Route::post("/api/csdbcreate",[CsdbController::class, 'create'])->middleware('auth')->name('api.create_object');
Route::get("/api/deletion/all",[CsdbController::class, 'get_deletion_list'])->middleware('auth')->name('api.get_deletion_list');

Route::post("/api/updateobject/{filename}", [CsdbController::class, 'update'])->middleware('auth')->name('api.update_object');
Route::post("/api/uploadICN", [CsdbController::class, 'uploadICN'])->middleware('auth')->name('api.upload_ICN');

Route::get('/api/getobject/{filename}', [CsdbController::class, 'getFile'])->middleware('auth')->name('api.get_object');
Route::get('/api/getdmcstaged/all', [CsdbController::class, 'get_dmc_staged_list'])->middleware('auth')->name('api.get_dmc_staged_list');
Route::get('/api/geticnstaged/all', [CsdbController::class, 'get_icn_staged_list'])->middleware('auth')->name('api.get_icn_staged_list');
Route::get('/api/export/{filename}', [CsdbServiceController::class, 'export'])->middleware('auth')->name('api.get_export_file');

Route::get("/api/commit/{filename}",[CsdbController::class, 'commit'])->middleware('auth')->name("api.commit_object");
Route::get("/api/issue/{filename}", [CsdbController::class, 'issue'])->middleware('auth')->name('api.issue_object');
Route::post("/api/edit/{filename}",[CsdbController::class, 'edit'])->middleware('auth')->name('api.edit_object');
// Route::post('api/pushtostage',)



Route::get("/api/delete/{filename}", [CsdbController::class, 'delete'])->middleware('auth')->name('api.delete_object');
Route::get("/api/restore/{filename}", [CsdbController::class, 'restore'])->middleware('auth')->name('api.restore_object');
Route::post("/api/permanentdelete", [CsdbController::class, 'permanentDelete'])->middleware('auth')->name('api.permanentdelete_object');
Route::get("/api/harddelete/{filename}", [CsdbController::class, 'harddelete'])->middleware('auth'); // untuk developer saja





// Route::get("/api/dml/all",[CsdbController::class, 'get'])->middleware('auth')->name('api.get_dml_list');
// Route::post("/api/dml/create",[CsdbController::class, 'create'])->middleware('auth')->name('api.post_create_dml');
// Route::post("/api/dml/{project_name}/{filename}/addentry",[CsdbController::class, 'addEntry'])->middleware('auth')->name('api.post_addEntry_dml');

// get Model
Route::get("/api/model/{filename}",[CsdbController::class, 'get_object_model'])->middleware('auth')->name('api.get_object_model');

// For Folder.vue get objects, get allobjects list
Route::get("/api/byfolder-allobjects",[CsdbController::class, 'forfolder_get_allobjects_list'])->middleware('auth')->name('api.requestbyfolder.get_allobject_list');

#### DmlController ####
Route::get("/api/dmrl/all",[DmlController::class, 'get_dmrl_list'])->middleware('auth')->name('api.get_dmrl_list');
