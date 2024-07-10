<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\CsdbController;
use App\Http\Controllers\CsdbServiceController;
use App\Http\Controllers\DmlController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::post("/api/createcomment",[CommentController::class, 'create'])->middleware('auth')->name('api.create_comment');