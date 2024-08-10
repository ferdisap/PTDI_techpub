<?php

use App\Http\Controllers\CsdbController;
use App\Http\Controllers\CsdbServiceController;
use App\Http\Controllers\DmlController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::post("/api/createdml",[DmlController::class, 'create'])->middleware('auth')->name('api.create_dml');
Route::get("/api/dmrl/all",[DmlController::class, 'get_dmrl_list'])->middleware('auth')->name('api.get_dmrl_list');
// Route::post("/api/dmlupdate/{filename}",[DmlController::class, 'dmlupdate'])->middleware('auth')->name('api.dmlupdate');
Route::post("/api/dmlupdate/{filename}",[DmlController::class, 'update'])->middleware('auth')->name('api.dmlupdate');
Route::get("/api/json/{filename}",[DmlController::class, 'read_json'])->middleware('auth')->name('api.read_json');