<?php

use App\Http\Controllers\BrController;
use Illuminate\Support\Facades\Route;

// Route::get("/api/brex/all",[BrController::class, 'get_brex_list'])->middleware('auth')->name('api.get_brex_list');
Route::get("/api/br/all",[BrController::class, 'get_list'])->middleware('auth')->name('api.get_list');
Route::post("/api/br/create",[BrController::class, 'create_br'])->middleware('auth')->name('api.create_br');

// Route::post("/api/brex/create",[BrController::class, 'create_brex'])->middleware('auth')->name('api.create_brex');

