<?php

use App\Http\Controllers\BrexController;
use Illuminate\Support\Facades\Route;

Route::get("/brex2/{project_name}/{filename}",[BrexController::class, 'app'])->middleware('auth')->name('get_brex_app');

Route::get("/api/brex/all",[BrexController::class, 'get'])->middleware('auth')->name('api.get_brex_list');

Route::get("/api/brex/{project_name}/{filename}/transform",[BrexController::class, 'transform'])->middleware('auth')->name('api.get_brex_transform');
// Route::get("/api/brdp/{project_name}/{filename}/{brParaId}/transform",[BrdpController::class, 'transformBrPara'])->middleware('auth')->name('get_brdp_transformBrPara');
// Route::get("/api/brdp/{project_name}/{filename}/search",[BrdpController::class, 'search'])->middleware('auth')->name('get_brdp_search');

// for csdb3
Route::post("/api/brex/create",[BrexController::class, 'create'])->middleware('auth')->name('api.create_brex');

