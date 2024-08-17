<?php

use App\Http\Controllers\Csdb\DdnController;
use Illuminate\Support\Facades\Route;

Route::post("/api/ddn/create",[DdnCOntroller::class, 'create'])->middleware('auth')->name('api.create_ddn');

// get dispatced ddn
Route::get("/api/ddn/dispatched",[DdnController::class, 'list'])->middleware('auth')->name('api.get_ddn_list');

