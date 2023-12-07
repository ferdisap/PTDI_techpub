<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IetmController extends Controller
{
  public function __invoke()
  {
    return view('ietm.app');
  }

  // public function getindex()
  // {
  //   return view('ietm/app');
  // }
}
