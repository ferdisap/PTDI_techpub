<?php

namespace App\Http\Controllers;

use App\Models\Csdb;
use Illuminate\Http\Request;

class BrController extends Controller
{
  // public function get_brex_list()
  // {
  //   $brexs = Csdb::where('filename', 'like' ,"DMC-%-%-%-%-%-022%")->get();
  //   return $brexs;
  // }

  public function get_list()
  {
    $br = Csdb::where('filename', 'like', "DMC-%-022%")->orWhere('filename', 'like', "DMC-%-024%")->get();
    return $br;
  }

  public function create_br(Request $request)
  {
    // sementara pakai CsdbController@create dulu
    return (new CsdbController())->create($request);
  }
}
