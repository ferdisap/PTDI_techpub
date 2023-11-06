<?php

namespace App\Http\Controllers\Csdb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DmlController extends Controller
{
  public function indexDML()
  {  
    return view('csdb/dml/dml_index', [
      'title' => 'DML Index',
    ]);
  }

  public function table(Request $request, string $aircraft)
  {
    $path_dmrl = base_path(). DIRECTORY_SEPARATOR. "ietp_{$aircraft}". DIRECTORY_SEPARATOR. "csdb". DIRECTORY_SEPARATOR. "data_management_list". DIRECTORY_SEPARATOR. 'dmrl';
    $path_csl = base_path(). DIRECTORY_SEPARATOR. "ietp_{$aircraft}". DIRECTORY_SEPARATOR. "csdb". DIRECTORY_SEPARATOR. "data_management_list". DIRECTORY_SEPARATOR. 'csl';
    $list_dmrl = parent::get_file($path_dmrl);
    $list_csl = parent::get_file($path_csl);
    dd($list_dmrl, $list_csl);

    return view("csdb/dml/dml_{$aircraft}_index",[
      'title' => "dml_{$aircraft}",
      // 'dml' =>
    ]);
  }
}
