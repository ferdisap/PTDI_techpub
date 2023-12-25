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

Route::get('/repo', [RepoController::class, 'getindex'])->middleware('auth')->name('get_index_repo'); // untuk csdb management system, yang lain untuk diakses oleh ietm
Route::get('/repo/delete', [RepoController::class, 'getdelete'])->middleware('auth')->name('get_delete_repo'); // untuk csdb management system, yang lain untuk diakses oleh ietm

Route::get('/ietm/repo', [RepoController::class, 'get'])->middleware('auth')->name('get_repo');

Route::post('/ietm/repo/create', [RepoController::class, 'getcreate'])->middleware('auth')->name('post_create_repo');
Route::post('/api/ietm/repo/create', [RepoController::class, 'getcreate2'])->middleware('auth')->name('api.post_create_repo');

Route::get("/api/ietm/repo", [RepoController::class, 'provide_repo'])->name('provide_repo');
Route::get("/api/ietm/repo/{name}", [RepoController::class, 'provide_repo_object'])->name('provide_repo_object');
Route::get("/api/ietm/{repo:name}/{filename}", [RepoController::class, 'provide_object_detail'])->name('provide_object_detail');

Route::get("/api/ietm-pmc/{filename}", [RepoController::class, 'handle_pmc']);

// Route::get("/api/ietm/pmc", [RepoController::class, 'handle_pmc']);
// Route::get("/tes", function(){
//   dd('aabb');
// });