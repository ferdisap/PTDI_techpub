<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\Csdb\DmcController;
use App\Http\Controllers\CsdbController;
use App\Http\Controllers\CsdbProcessingController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RepoController;
use App\Models\Csdb;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/ietm/repo', [RepoController::class, 'get'])->middleware('auth')->name('get_repo');

Route::post('/ietm/repo/create', [RepoController::class, 'getcreate'])->middleware('auth')->name('post_create_repo');

Route::get("/api/ietm/repo", function(){
  return response()->json([
    'repo' => 'foobar repo'
  ],200);
});
