<?php

use App\Http\Controllers\CsdbController;
use App\Http\Controllers\CsdbServiceController;
use App\Http\Controllers\DmController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::get('/api/getdmcstaged/all', [DmController::class, 'get_dmc_staged_list'])->middleware('auth')->name('api.get_dmc_staged_list');
Route::get('/api/getdmcunstaged/all', [DmController::class, 'get_dmc_unstaged_list'])->middleware('auth')->name('api.get_dmc_unstaged_list');