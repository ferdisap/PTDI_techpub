<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\Csdb\DmcController;
use App\Http\Controllers\CsdbController;
use App\Http\Controllers\CsdbProcessingController;
use App\Http\Controllers\CsdbServiceController;
use App\Http\Controllers\ProjectController;
use App\Models\Csdb;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/csdb/js/request', [CsdbServiceController::class, 'provide_csdb_js'])->middleware('auth')->name('get_request_csdb_js');

Route::get('/csdb/object/request', [CsdbServiceController::class, 'provide_csdb_object'])->middleware('auth')->name('get_request_csdb_object');

Route::post("/csdb/object/CSDB", [CsdbServiceController::class, 'CSDB']);