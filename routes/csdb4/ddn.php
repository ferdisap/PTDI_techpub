<?php

use App\Http\Controllers\DdnController;
use Illuminate\Support\Facades\Route;

Route::get("/api/ddn/create",[DdnCOntroller::class, 'create'])->middleware('auth')->name('api.create_ddn');
Route::get("/api/ddn/all",[DdnController::class, 'get_ddn_list'])->middleware('auth')->name('api.get_ddn_list');

