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
}
