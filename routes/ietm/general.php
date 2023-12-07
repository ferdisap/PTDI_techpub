<?php

use App\Http\Controllers\CsdbController;
use App\Http\Controllers\IetmController;
use Illuminate\Support\Facades\Route;

Route::get("/ietm/{view?}", IetmController::class)->where('view','(.*)');
// Route::get("/ietm", [IetmController::class, 'getindex']);

