<?php

use App\Http\Controllers\BrController;
use Illuminate\Support\Facades\Route;

// Route::get("/api/brex/all",[BrController::class, 'get_brex_list'])->middleware('auth')->name('api.get_brex_list');
Route::get("/api/br/all",[BrController::class, 'get_br_list'])->middleware('auth')->name('api.get_br_list');
Route::post("/api/br/create",[BrController::class, 'create_br'])->middleware('auth')->name('api.create_br');
Route::get("/api/getbrex/all",[BrController::class, 'get_brex_list'])->middleware('auth')->name('api.get_brex_list');
Route::get("/api/getbrdp/all",[BrController::class, 'get_brdp_list'])->middleware('auth')->name('api.get_brdp_list');

// Route::post("/api/brex/create",[BrController::class, 'create_brex'])->middleware('auth')->name('api.create_brex');

// dari brex/general.php
Route::get("/brex2/{project_name}/{filename}",[BrexController::class, 'app'])->middleware('auth')->name('get_brex_app');
Route::get("/api/brex/{project_name}/{filename}/transform",[BrexController::class, 'transform'])->middleware('auth')->name('api.get_brex_transform');

// dari brdp/general.php
Route::get("/brdp/{project_name}/{filename}",[BrdpController::class, 'app'])->middleware('auth')->name('get_brdp_app');
Route::get("/api/brdp/{project_name}/{filename}/transform",[BrdpController::class, 'transform'])->middleware('auth')->name('get_brdp_transform');
Route::get("/api/brdp/{project_name}/{filename}/{brParaId}/transform",[BrdpController::class, 'transformBrPara'])->middleware('auth')->name('get_brdp_transformBrPara');
Route::get("/api/brdp/{project_name}/{filename}/search",[BrdpController::class, 'search'])->middleware('auth')->name('get_brdp_search');
// Route::get("/api/{projectName}/{filename}/

