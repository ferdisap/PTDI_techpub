<?php

namespace App\Http\Controllers;

// use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
// use Illuminate\Foundation\Validation\ValidatesRequests;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
  // use AuthorizesRequests, ValidatesRequests;

  public function index(Request $request)
  {
    switch ($request->utility) {
      case 'getfile':
        return $this->getFile($request->path,$request->ct);
      case 'requestList':
        return $this->generateAllStyle($request);
      default:
        return view('welcome');
        break;
    }
  }

  private function getfile($path,$ct)
  {
    switch ($ct) {
      case 'xml':
        return response(file_get_contents($path, FILE_USE_INCLUDE_PATH), 200, [
          'Content-Type' => 'application/xml'
        ]);
      case 'jpeg':
        return response(file_get_contents($path, FILE_USE_INCLUDE_PATH), 200, [
          'Content-Type' => 'image/jpeg'
        ]);
      case 'css':
        return response(file_get_contents($path, FILE_USE_INCLUDE_PATH), 200, [
          'Content-Type' => 'text/css'
        ]);
      case 'html':
        return view('brex/index');
      case 'javascript':
        return response(file_get_contents($path, FILE_USE_INCLUDE_PATH), 200, [
          'Content-Type' => 'text/javascript'
        ]);

      default:
        return false;
    }
  }

  public function anything(Request $request)
  {
    $pathInfo = $request->getPathInfo();

    if(strpos($pathInfo,'.xsl')){
      return $this->getfile('view/general/xsl' . $pathInfo, 'xml');
    } else {
      return $this->index($request);
    }
  }

  public function generateAllStyle(Request $request)
  {
    if ($request->utility == 'requestList'){
      $dir = base_path();
      $pathXsl = $request->path ?? "/ietp_n219/view/general/xsl";
  
      $arr = array();
  
      // Open a known directory, and proceed to read its contents
      if (is_dir($dir)) {
          if ($dh = opendir($dir . $pathXsl)) {
              while (($file = readdir($dh)) !== false) {
                  // echo $file == "x";
                  // echo "filename: $file" . "<br/>";
                  if ($file != "." && $file != ".."){
                    array_push($arr,$file);
                  }
              }
              closedir($dh);
          }
      }
      // return json_encode($arr);
      // dd($arr);
      return response()->json($arr);
    } 
    else {
      return view('general/generateAllStyle');
    }
  }
}
