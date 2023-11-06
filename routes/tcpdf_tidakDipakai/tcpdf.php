<?php

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get("/tcpdf", function(){

  // dd(storage_path('app/public'),base_path('ietp_n219/csdb/multimemdia'));
  // dd(base_path());
  // dd(app_path('ietp_n219'),storage_path('app/public'));
  // dd(app_path('ietp_n219'),storage_path('app/public'));
  // dd(dirname(__FILE__),__FILE__);
  // return require __DIR__."./example_001.php";
  // return require __DIR__."./tes_001.php";
  return require __DIR__."./tes_002.php";

});