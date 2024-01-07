<?php

use App\Http\Controllers\BrdpController;
use Illuminate\Support\Facades\Route;

Route::get("/brdp/{project_name}/{filename}",[BrdpController::class, 'app'])->middleware('auth')->name('get_brdp_app');

Route::get("/api/brdp/{project_name}/{filename}/transform",[BrdpController::class, 'transform'])->middleware('auth')->name('get_brdp_transform');
Route::get("/api/brdp/{project_name}/{filename}/{brParaId}/transform",[BrdpController::class, 'transformBrPara'])->middleware('auth')->name('get_brdp_transformBrPara');
Route::get("/api/brdp/{project_name}/{filename}/search",[BrdpController::class, 'search'])->middleware('auth')->name('get_brdp_search');
// Route::get("/api/{projectName}/{filename}/
