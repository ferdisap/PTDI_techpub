<?php

use App\Http\Controllers\CsdbController;
use App\Http\Controllers\CsdbServiceController;
use App\Http\Controllers\IcnController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::get('/api/geticnstaged/all', [IcnController::class, 'get_icn_staged_list'])->middleware('auth')->name('api.get_icn_staged_list');