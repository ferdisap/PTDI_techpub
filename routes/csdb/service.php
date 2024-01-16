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

// Route::get('/csdb/js/request', [CsdbServiceController::class, 'provide_csdb_js'])->middleware('auth')->name('get_request_csdb_js');
// Route::get('/csdb/object/request', [CsdbServiceController::class, 'provide_csdb_object'])->middleware('auth')->name('get_request_csdb_object');

// sementara route ini belum dimanfaatkan, karena transform dilakukan di server side
// Route::post("/csdb/object/CSDB", [CsdbServiceController::class, 'CSDB']);
// sementara route ini belum dimanfaatkan, karena xsl transform dilakukan di server side
// Route::get('/csdb/xsl/request', [CsdbServiceController::class, 'provide_csdb_xsl'])->middleware('auth')->name('get_request_csdb_xsl');

// Route::get("/csdb/object/transform", [CsdbServiceController::class, 'provide_csdb_transform'])->name('get_transform_csdb');
// Route::get("/csdb/object/export", [CsdbServiceController::class, 'provide_csdb_export'])->middleware('auth')->name('get_export_csdb');

Route::get("/api/csdb/search", [CsdbServiceController::class, 'search'])->middleware('auth')->name('api.csdb_search');