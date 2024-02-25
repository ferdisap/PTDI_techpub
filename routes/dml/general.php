<?php

use App\Http\Controllers\DmlController;
use Illuminate\Support\Facades\Route;

Route::get("/dml/{view?}",[DmlController::class, 'app'])->middleware('auth')->name('get_dml_app');

// Route::get("/api/dml/all",[DmlController::class, 'get'])->middleware('auth')->name('api.get_dml_list'); // nanti tidak dipakai lagi
Route::get("/api/dml/all",[DmlController::class, 'get_dml_list'])->middleware('auth')->name('api.get_dml_list');
Route::get("/api/csl/all",[DmlController::class, 'get_csl_list'])->middleware('auth')->name('api.get_csl_list');

// new for csdb3
Route::post("/api/createdml",[DmlController::class, 'create'])->middleware('auth')->name('api.create_dml');
Route::post("/api/commitdml/{filename}",[DmlController::class, 'commit'])->middleware('auth')->name('api.commit_dml');
Route::post("/api/issuedml/{filename}",[DmlController::class, 'issue'])->middleware('auth')->name('api.issue_dml');
Route::post("/api/editdml/{filename}",[DmlController::class, 'edit'])->middleware('auth')->name('api.edit_dml');
Route::post("/api/adddmlentry/{filename}/",[DmlController::class, 'addEntry'])->middleware('auth')->name('api.addEntry_dml');
Route::get("/api/getentrylist/{filename}",[DmlController::class, 'getEntry'])->middleware('auth')->name('api.get_dmlentry');

// Route::post("/api/dml/create",[DmlController::class, 'create'])->middleware('auth')->name('api.post_create_dml');
// Route::post("/api/dml/{project_name}/{filename}/addentry",[DmlController::class, 'addEntry'])->middleware('auth')->name('api.addEntry_dml'); // sudah tidak terpakai karena tidak ada lagi project_name


// ### DML Editting ###
// Route::post("/api/dmlcontentupdate/{filename}",[DmlController::class, 'dmlcontentupdate'])->middleware('auth')->name('api.dmlcontentupdate');
Route::post("/api/dmlupdate/{filename}",[DmlController::class, 'dmlupdate'])->middleware('auth')->name('api.dmlupdate');

// ### for staging
Route::post("/api/tostaging/{filename}", [DmlController::class, 'create_csl_forstaging'])->middleware('auth')->name('api.tostaging');
Route::get("/api/getcsltostaging", [DmlController::class, 'get_csl_forstaging'])->middleware('auth')->name('api.get_csl_forstaging');
Route::get("/api/getcslstaging/all", [DmlController::class, 'get_csl_staging_list'])->middleware('auth')->name('api.get_csl_staging_list');
Route::get("/api/pushtostaging/{filename}",[DmlController::class, 'push_csl_forstaging'])->middleware('auth')->name('api.push_csl_forstaging');
Route::get("/api/declinestaging/{filename}",[DmlController::class, 'decline_csl_forstaging'])->middleware('auth')->name('api.decline_csl_forstaging');
//ini digantikan sama CsdbController@delete // Route::get("/api/deletedml/{filename}",[DmlController::class, 'deletedml'])->middleware('auth')->name('api.delete_dml');
Route::get("/api/accepttostaged/{filename}",[DmlController::class, 'acceptcsl'])->middleware('auth')->name('api.accept_dml');
