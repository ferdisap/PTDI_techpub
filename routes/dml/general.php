<?php

use App\Http\Controllers\DmlController;
use Illuminate\Support\Facades\Route;

Route::get("/dml/{view?}",[DmlController::class, 'app'])->middleware('auth')->name('get_dml_app');

Route::get("/api/dml/all",[DmlController::class, 'get'])->middleware('auth')->name('api.get_dml_list');
Route::post("/api/dml/create",[DmlController::class, 'create'])->middleware('auth')->name('api.post_create_dml');
Route::post("/api/dml/{project_name}/{filename}/addentry",[DmlController::class, 'addEntry'])->middleware('auth')->name('api.post_addEntry_dml');
