<?php

namespace App\Http\Controllers\Csdb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DmlController extends Controller
{
  public function indexDML(){
    $list = $this->getList();
    dd($list);
    dd('xxx');
  }

  public function getList()
  {
    $dir = base_path();
    return $dir;
  }
}
