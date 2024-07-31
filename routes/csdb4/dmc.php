<?php

use App\Http\Controllers\Csdb\DmcController;
use Illuminate\Support\Facades\Route;

Route::get('/api/dmcsearch', [DmcController::class, 'searchCsdbs'])->middleware('auth')->name('api.dmc_search');