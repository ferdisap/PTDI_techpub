<?php

use App\Http\Controllers\Csdb\DmcController;
use Illuminate\Support\Facades\Route;

Route::get('/dmc',[DmcController::class, 'indexDMC']);
Route::get('/dmc/{aircraft}',[DmcController::class, 'table']);
Route::get('/dmc/{aircraft}/transform',[DmcController::class, 'transform']);
Route::post('/dmc/{aircraft}/transform',[DmcController::class, 'transforming']);