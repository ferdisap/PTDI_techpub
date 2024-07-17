<?php

use App\Http\Controllers\CsdbController;
use App\Http\Controllers\CsdbServiceController;
use App\Http\Controllers\DmlController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::post("/api/createdml",[DmlController::class, 'create'])->middleware('auth')->name('api.create_dml');
Route::get("/api/dmrl/all",[DmlController::class, 'get_dmrl_list'])->middleware('auth')->name('api.get_dmrl_list');
Route::get("/api/content/html/{filename}",[DmlController::class, 'read_html_content'])->middleware('auth')->name('api.get_html_content');
Route::post("/api/dmlupdate/{filename}",[DmlController::class, 'dmlupdate'])->middleware('auth')->name('api.dmlupdate');