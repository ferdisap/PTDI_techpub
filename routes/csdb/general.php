<?php

use App\Http\Controllers\Csdb\DmcController;
use App\Http\Controllers\CsdbController;
use App\Models\Csdb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/csdb',function(Request $request){
  $lists = array();
  if($mic = $request->get('mic')){
    $lists = Csdb::where('path', 'like', "csdb/{$mic}%");
    if($status = $request->get('status')){
      $lists->where('status', '=', $status);
    }
  }
  return  view('csdb.index', [
    'listsobj' => $lists ? $lists->get() : null,
    'mic' => $mic ?? null,
  ]);
});

Route::get('/csdb/object/create', function (){
  return view('csdb.create');
})->middleware('auth');
Route::post("/csdb/object/create", [CsdbController::class, 'create'])->middleware('auth')->name('create_csdb_object');

Route::get("/csdb/object/update", function(Request $request){
  $filename = $request->get('filename');
  $csdb_object = Csdb::where('path', 'like', "%{$filename}")->first(['path', 'description', 'initiator_id']);
  
  // return jika bukan text
  if(!str_contains("text",storage_path("app/{$csdb_object->path}"))){
    return response()->file(storage_path("app/{$csdb_object->path}"));
  }
  // return view dan text
  if($csdb_object){
    return view('csdb.update',[
      'xmleditor' => Storage::get("$csdb_object->path"),
      'description' => $csdb_object->description,
      'initiator' => $csdb_object->initiator->email
    ]);
  } else {
    return back();
  }
})->middleware('auth')->name('getUpdate_csdb');

Route::post("/csdb/object/update", [CsdbController::class, 'update'])->middleware('auth')->name('update_csdb_object');

Route::post('/csdb/object/delete', [CsdbController::class, 'delete'])->middleware('auth')->name('delete_csdb_object');