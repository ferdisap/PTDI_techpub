<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

class BrexController extends Controller
{
  public function indexBrex()
  {
    if (request()->utility == 'getfile'){
      return $this->getFile(request()->path, request()->ct);
    } else {
      return view('brex/brex_index', [
        'title' => 'BREX Index'
      ]);
    }
  }

  public function table($aircraft)
  {
    // Blade::setPath(base_path() . DIRECTORY_SEPARATOR . 'ietp_n219' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR);
    // dd(Blade::getPath());
    // dd(__DIR__.'/../components');
    return view('brex/brex_' . $aircraft .'_index', [
      'title' => 'brex ' . $aircraft,
      // 'lists' => $this->brdpListToArray($lists)
    ]);
  }

  // private function getFile($path, $ct){    
  //   switch($ct){
  //     case 'xml':
  //       return response(file_get_contents($path, FILE_USE_INCLUDE_PATH), 200, [
  //         'Content-Type' => 'application/xml'
  //       ]);
  //     case 'jpg':
  //       return;
  //     case 'html':
  //       return view('brex/index');
  //     default:
  //       return false;        
  //   }
  // }
}
