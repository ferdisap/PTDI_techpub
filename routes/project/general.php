<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\Csdb\DmcController;
use App\Http\Controllers\CsdbController;
use App\Http\Controllers\ProjectController;
use App\Models\Csdb;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// PROJECT
Route::get("project", [ProjectController::class, 'index'])->middleware('auth')->name('index_project');

Route::get("project/create", [ProjectController::class, 'getcreate'])->middleware('auth')->name('get_create_project'); 

Route::post("project/create", [ProjectController::class, 'postcreate'])->middleware('auth')->name('post_create_project');

Route::get("project/delete", [ProjectController::class, 'getdelete'])->middleware('auth')->name('get_delete_project');
// Route::get('project/assign', [ProjectController::class, 'getassign'])->name('get_assign_object');
// Route::post("project/assign", [ProjectController::class, 'postassign'])->name('post_assign_object');

Route::get('project/detail', [ProjectController::class, 'getdetail'])->middleware('auth')->name('get_detail_project');
