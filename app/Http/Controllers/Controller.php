<?php

namespace App\Http\Controllers;

// use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
// use Illuminate\Foundation\Validation\ValidatesRequests;

use App\Models\Csdb;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Ptdi\Mpub\Helper;

class Controller extends BaseController
{
  // use AuthorizesRequests, ValidatesRequests;

  public function index(Request $request)
  {
    // return view('welcome');
    return view('techpub.app');
  }

  public function authcheck()
  {
    return response()->json([
      'name' => Auth::user()->name,
      'email' => Auth::user()->email,
    ],200);
  }

  // public function getWorkerJs(Request $request)
  // {
  //   $worker = file_get_contents(resource_path('js/csdb4/worker.js'));
  //   return Response::make($worker,200,[
  //     'content-type' => 'application/javascript'
  //   ]);
  // }
  public function getAxiosJs(Request $request)
  {
    $axoiospath = realpath(getcwd().'../../node_modules/axios/dist/axios.js');
    $axios = file_get_contents($axoiospath);
    return Response::make($axios,200,[
      'content-type' => 'application/javascript'
    ]);
  }

  public static function getAllRoutesNamed()
  {
    $allRoutes = Route::getRoutes()->getRoutes();
    $allRoutes = array_filter($allRoutes, fn ($r) => $r->getName());
    
    foreach ($allRoutes as $k => $v) {
      $allRoutes[$v->getName()] = $v;
      unset($allRoutes[$k]);
    }
    $allRoutes = array_map(function ($v) {
      $params = $v->parameterNames(); // ['csdb', 'repo']
      $bindingFields = $v->bindingFields(); // ['csdb' => 'filename', 'repo' => 'name']
      foreach($params as $k => $param){
        if(!empty($bindingFields) AND isset($bindingFields[$param])){
          $param = $bindingFields[$param];  // param 'csdb' diubah menjadi 'filename'. karena csdb adalah model class 
        }
        $params[$param] = ":$param";
        unset($params[$k]);
      }
      $path = route($v->getName(), array_values($params), false); // "/api/ietm/{repo:name}/{filename}" menjadi "/api/ietm/:name/:filename" agar JS mudah memasukkan key dan valuenya
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
    $data = [
      'messages' => $messages,
    ];
    $args = func_get_args();
    for ($i=2; $i < count($args); $i++) { 
      if(is_array($args[$i])){
        $data = array_merge($data, $args[$i]);
      } else {
        $data[] = $args[$i];
      }
    }
    return response()->json($data, $code);
  }

  /**
   * akan membuat output sama dengan $fail() pada $request->validate();
   * jika ingin mengirim response berupa $model->get(), maka parameter adalah [data: $this->model->get()->toArray()];
   * jika ingin mengirim response berupa $model->paginate(), maka parameter adalah $this->model->toArray();
   */
  public function ret2($code, $messages = [])
  {
    $data = ["message" => ''];
    $args = func_get_args();

    if(!($code >= 200 AND $code < 300)){
      $data['errors'] = [];
    }

    $isArr = function($message, $fn) use(&$data, $code){
      if(is_array($message)) {
        foreach($message as $key => $value){
          // jika array sequential 
          if((int)$key OR $key == 0){
            if(is_array($value)){
              $fn($value, $fn);
            } else {
              if($value){
                $data['message'] .= $value . \PHP_EOL;
              }
            }
          }
          // jika array assoc
          else {
            if(isset($data['errors'])){
              $data['errors'][$key] = $value;
            } else {
              $data[$key] = $value;
            }
          }
        }
      } else {
        if($message){
          $data['message'] .= $message .\PHP_EOL;
        }
      }
    };

    for ($i=1; $i < count($args); $i++) { 
      $message =  $args[$i];
      $isArr($message, $isArr);
    }
    return response()->json($data, $code);
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

    /**
   * $model bisa berupa ModelsCsdb atau DB::table()
   */
  public mixed $model;

  /**
   * default search column db is filename
   * harus set $this->model terlebih dahulu baru bisa jalankan fungsi ini
   */
  public function search($keyword)
  {
    $keywords = Helper::explodeSearchKey(str_replace("_",'\_',$keyword));
    foreach($keywords as $k => $keyword){
      if((int)$k OR $k == 0){ // jika $keyword == eg.: 'MALE-0001Z-P', ini tidak ada separator '::' jadi default pencarian column filename
        $this->model->whereRaw("filename LIKE '%{$keyword}%' ESCAPE '\'");
      } else {
        $column = Csdb::columnNameMatching($k, 'csdb');
        if($column){
          $this->model->whereRaw("{$column} LIKE '%{$keyword}%' ESCAPE '\'");
        }
        else {
          $messages[] = "'{$column}::{$keyword}' cannot be found.";
        }
      }
    }
    return $keywords;
  }
  
}
