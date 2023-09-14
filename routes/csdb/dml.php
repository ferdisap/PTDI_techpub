<?php

use App\Http\Controllers\Csdb\DmlController;
use Illuminate\Support\Facades\Route;

Route::get('/dml',[DmlController::class, 'indexDML']);