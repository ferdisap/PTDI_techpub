<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BrdpController extends Controller
{
  public function index()
  {
    return view('brdp/brdp_index', [
      'title' => 'BRDP Index'
    ]);
  }

  public function detail($aircraft)
  {
    return view('brdp/brdp_' . $aircraft, [
      'title' => 'brdp ' . $aircraft,
      'lists' => $this->make_list()
    ]);
  }

  private function make_list()
  {
    $lists = collect([
      [
        'ident' => 'foo',
        'title' => 'foo',
        'category' => 'foo',
        'audit' => 'foo',
        'decision' => 'foo',
      ],
      [
        'ident' => 'foo',
        'title' => 'foo',
        'category' => 'foo',
        'audit' => 'foo',
        'decision' => 'foo',
      ]
    ]);

    return $lists;
    dd($lists[0]->ident);
  }
}
