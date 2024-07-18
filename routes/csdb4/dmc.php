<?php

use App\Http\Controllers\Csdb\DmcController;
use Illuminate\Support\Facades\Route;

Route::get('/api/dmcsearchmodel', [DmcController::class, 'searchModel'])->middleware('auth')->name('api.dmc_search_model');