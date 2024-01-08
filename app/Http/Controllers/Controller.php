<?php

namespace App\Http\Controllers;

// use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
// use Illuminate\Foundation\Validation\ValidatesRequests;

use App\Models\Csdb;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class Controller extends BaseController
{
  // use AuthorizesRequests, ValidatesRequests;

  public function index(Request $request)
  {
    return view('welcome');
  }

  public function authcheck()
  {
    return response()->json([
      'name' => Auth::user()->name,
      'email' => Auth::user()->email,
    ],200);
  }

  public function getAllRoutesNamed()
  {
    $allRoutes = Route::getRoutes()->getRoutes();
    $allRoutes = array_filter($allRoutes, fn ($r) => $r->getName());

    foreach ($allRoutes as $k => $v) {
      $allRoutes[$v->getName()] = $v;
      unset($allRoutes[$k]);
    }
    $allRoutes = array_map(function ($v) {
      $params = $v->parameterNames();
      foreach($params as $k => $n){
        $params[$n] = ":$n";
        unset($params[$k]);
      }
      $path = route($v->getName(), $params, false);
      $ret = [
        'name' => $v->getName(),
        'method' => $v->methods(),
        'path' => $path,
        'params' => $params,
      ];
      return $ret;
    }, $allRoutes);
    return $allRoutes;
  }


  /**
   * digunakan untuk mencari file.
   * Fungsi ini dipakai saat seeding csdb SQL
   */
  public static function get_file(string $path, string $filename = '', bool $all = false){
    $exclude = array('.','..','.git','.gitignore');
    // $arr = array_diff(scandir($dir), $exclude);
    if($path && $filename){
      $file = file_get_contents($path.DIRECTORY_SEPARATOR.$filename, FILE_USE_INCLUDE_PATH);
      return [$file, mime_content_type($path.DIRECTORY_SEPARATOR.$filename)];
    }
    // elseif($path){
    elseif($path){
      $list = array();
      $ls = array_diff(scandir($path), $exclude);
      foreach($ls as $l){
        if(!is_dir($path.DIRECTORY_SEPARATOR.$l)){
          array_push($list,$l);
        }
      }
      return $list;
    }
  }

  public function ret($code, $messages = [])
  {
    return response()->json([
      'messages' => $messages,
    ], $code);
  }


  
  /**
   * all returned array contains path is relative path
   */
  public static function searchFile(string $path, string $filename = '', bool $getFile = false)
  {
    $exclude = array('.','..','.git','.gitignore');
    $list = array();
    foreach(array_diff(scandir($path), $exclude) as $l){
      if(is_dir($path."/".$l)){
        $x = self::searchFile($path."/".$l); // get file
        $x = array_map((fn($v) => $l."/".$v), $x); // agar jadi relative path
        $list = array_merge($list, $x);
      } else {
        $list[] = $l;
      }
    }
    if($filename){
      $text =  array_values(array_filter($list, fn($v) => str_contains($v, $filename)))[0];
      if($getFile){
        return file_get_contents($path. DIRECTORY_SEPARATOR. $text);
      } else {
        return $text;
      }
    }
    return $list;
  }

  public function route(Request $request, string $name)
  {
    return redirect()->route($name, $request->all());
  }
  
}
