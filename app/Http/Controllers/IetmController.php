<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IetmController extends Controller
{
  public function getindex()
  {
    return view('ietm/index');
  }
}
